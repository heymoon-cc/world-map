<?php

namespace App\Controller;

use App\Service\RedisServiceInterface;
use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\GeometryIOException;
use Brick\Geo\Exception\InvalidGeometryException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\Geometry;
use HeyMoon\VectorTileDataProvider\Contract\SourceFactoryInterface;
use HeyMoon\VectorTileDataProvider\Contract\TileServiceInterface;
use HeyMoon\VectorTileDataProvider\Entity\TilePosition;
use HeyMoon\VectorTileDataProvider\Spatial\WebMercatorProjection;
use RedisException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TilesController extends AbstractController
{
    public function __construct(
        private readonly int $midZoom,
        private readonly string $origin,
        private readonly RedisServiceInterface $store,
        private readonly TileServiceInterface $tileService,
        private readonly SourceFactoryInterface $sourceFactory,
    ) {}

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return Response
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     * @throws RedisException
     */
    #[Route('/tiles/{x}/{y}/{z}')]
    public function __invoke(int $x, int $y, int $z): Response
    {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', '5s');
        $tile = $this->getTile(TilePosition::xyzFlip($x, $y, $z));
        return new Response($tile, $tile ? 200 : 201, [
            'Content-Type' => 'application/x-protobuf',
            'Access-Control-Allow-Origin' => $this->origin,
            'Content-Encoding' => 'gzip',
            'Cache-Control' => 'max-age=604800, public'
        ]);
    }

    /**
     * @throws CoordinateSystemException
     * @throws RedisException
     * @throws UnexpectedGeometryException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     */
    private function getTile(TilePosition $position): string|null
    {
        $tile = $this->store->getClient()->get("tile$position");
        if ($tile) {
            return $tile;
        }
        if ($position->getZoom() <= $this->midZoom) {
            return null;
        }
        $scale = pow(2, $position->getZoom() - $this->midZoom);
        $raw = $this->store->getClient()->get('raw'.TilePosition::xyz(
                (int)floor($position->getColumn() / $scale),
                (int)floor($position->getRow() / $scale),
                $this->midZoom
            ));
        if ($raw === null) {
            return null;
        }
        $decoded = json_decode(gzdecode($raw), true);
        if (!$decoded) {
            return null;
        }
        $source = $this->sourceFactory->create();
        foreach ($decoded as $layer => $data) {
            foreach ($data as $item) {
                $source->add($layer, Geometry::fromText(reset($item), WebMercatorProjection::SRID), end($item));
            }
        }
        $result = gzencode($this->tileService->getTileMVT($source->getFeatures(), $position)->serializeToString());
        $this->store->getClient()->set("tile$position", $result);
        return $result;
    }
}
