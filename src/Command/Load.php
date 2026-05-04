<?php

namespace App\Command;

use App\Service\RedisServiceInterface;
use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\GeometryException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\IO\GeoJSON\FeatureCollection;
use Brick\Geo\IO\GeoJSONReader;
use HeyMoon\VectorTileDataProvider\Contract\GridServiceInterface;
use HeyMoon\VectorTileDataProvider\Contract\SourceFactoryInterface;
use HeyMoon\VectorTileDataProvider\Contract\TileServiceInterface;
use HeyMoon\VectorTileDataProvider\Entity\Feature;
use HeyMoon\VectorTileDataProvider\Entity\TilePosition;
use HeyMoon\VectorTileDataProvider\Spatial\WorldGeodeticProjection;
use RedisException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:load')]
class Load extends Command
{
    protected const MIN_ZOOM = [
        'building' => 10,
        'building_exrtusion' => 10,
        'building_labels' => 12
    ];

    protected const NEEDED_LAYERS = ['sand', 'water', 'river', 'bridge', 'playground', 'road', 'railway', 'playground',
        'tunnel', 'sidewalk', 'wall', 'greenfield', 'brownfield', 'scrub', 'heath', 'grassland', 'grass', 'greenland',
        'island', 'building', 'farmland', 'cemetery', 'allotments', 'apartment', 'residential', 'primary', 'secondary',
        'mall', 'wood', 'tertiary', 'trunk', 'trunk_link', 'primary_link', 'tertiary_link', 'secondary_link', 'dam',
        'tree', 'tree_row', 'stadium', 'motorcycle', 'structure', 'stone', 'outdoor', 'fountain', 'bench', 'embankment',
        'tower', 'school', 'staircase', 'bus_station', 'wetland', 'railway_crossing', 'marketplace', 'fence', 'ground',
        'crossing', 'cross', 'city', 'village', 'town', 'bus_stop', 'level_crossing', 'memorial', 'hospital', 'museum',
        'toilets', 'picnic_site', 'garden_centre', 'entrance', 'platform', 'square', 'doors', 'beach', 'wayside_cross',
        'ski', 'ski_rental', 'cliff', 'dog_park', 'islet', 'elevator', 'pharmacy', 'post_office', 'fire_station'];

    public function __construct(
        private readonly GeoJSONReader          $reader,
        private readonly SourceFactoryInterface $sourceFactory,
        private readonly GridServiceInterface   $gridService,
        private readonly TileServiceInterface   $tileService,
        private readonly RedisServiceInterface  $store,
        private readonly int                    $midZoom,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'File path');
        $this->addOption('zoom', 'z', InputOption::VALUE_OPTIONAL,
            'Start loading from particular zoom', 0);
        $this->addOption('overwrite', 'o', InputOption::VALUE_NEGATABLE,
            'Overwrite previous results', false);
    }

    /**
     * @throws GeometryException
     * @throws CoordinateSystemException
     * @throws UnexpectedGeometryException
     * @throws RedisException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $time = time();
        ini_set('memory_limit', '16G');
        $source = $this->sourceFactory->create();
        $layerNames = [];
        foreach ($input->getOption('path') as $path) {
            $output->writeln("Parsing $path...");
            /** @var FeatureCollection $collection */
            $collection = $this->reader->read(file_get_contents($path));
            $featureCount = count($collection->getFeatures());
            $output->writeln("$featureCount features read");
            foreach ($collection->getFeatures() as $feature) {
                $properties = (array)$feature->getProperties();
                $layer = $properties['place'] ?? $properties['highway'] ?? $properties['entrance'] ??
                    $properties['railway'] ?? $properties['surface'] ?? $properties['natural'] ??
                    $properties['landuse'] ?? $properties['power'] ?? $properties['amenity'] ?? $properties['leisure'] ??
                    $properties['barrier'] ?? $properties['man_made'] ?? $properties['pipeline'] ??
                    $properties['playground'] ?? $properties['waterway'] ?? $properties['shop'] ??
                    $properties['public_transport'] ?? $properties['aeroway'] ?? $properties['attraction'] ??
                    $properties['historic'] ?? $properties['tourism'] ??  (array_key_exists('cemetery', $properties) ?
                    'cemetery' : ($properties['building'] ?? null === 'yes' ? 'building' : null));
                if ($layer === null || str_contains($layer, ':') || str_contains($layer, ' ') ||
                    str_contains($layer, ';')) {
                    $layer = 'unclassified';
                }
                if (!in_array($layer, static::NEEDED_LAYERS, true)) {
                    continue;
                }
                $result = [];
                foreach ($properties as $name => $value) {
                    if (str_contains($name, ':') && !str_ends_with($name, ':ru') &&
                        !str_starts_with($name, 'addr:')) {
                        continue;
                    }
                    $result[$name] = $value;
                }
                $source->add($layer, $feature->getGeometry()->withSRID(WorldGeodeticProjection::SRID), $result,
                    static::MIN_ZOOM[$layer] ?? null);
                if (!in_array($layer, $layerNames, true)) {
                    $layerNames[] = $layer;
                    $output->writeln("Найден новый слой $layer");
                }
            }
            unset($collection);
        }
        $output->writeln("Mid zoom: $this->midZoom");
        $checkExisting = !$input->getOption('overwrite');
        foreach (range((int)$input->getOption('zoom'), $this->midZoom) as $zoom) {
            $output->writeln("Processing zoom $zoom");
            $grid = $this->gridService->getGrid($source, $zoom);
            $progress = new ProgressBar($output, $grid->count());
            $progress->start();
            $grid->iterate(function (TilePosition $position, array &$data) use ($checkExisting, $progress, $zoom) {
                if ($checkExisting && $this->store->getClient()->exists("tile$position")) {
                    $progress->advance();
                    return;
                }
                if ($zoom === $this->midZoom) {
                    $layers = [];
                    /** @var Feature $item */
                    /** @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection */
                    foreach ($data as &$item) {
                        $layers[$item->getLayer()->getName()][] = [
                            $item->getGeometry()->asText(), $item->getParameters()
                        ];
                    }
                    if ($layers) {
                        $this->store->getClient()->set("raw$position", gzencode(json_encode($layers)));
                    }
                }
                $this->store->getClient()->set("tile$position",
                    gzencode($this->tileService->getTileMVT($data, $position)->serializeToString()));
                $progress->advance();
            });
            $progress->finish();
            $output->write("\n");
            gc_collect_cycles();
        }
        $diff = time() - $time;
        $output->writeln("Finished in $diff seconds");
        return Command::SUCCESS;
    }
}
