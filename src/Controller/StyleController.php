<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class StyleController extends AbstractController
{
    public function __construct(
        private readonly string $origin,
        private readonly ThemeRepository $factory,
        private readonly KernelInterface $kernel
    ) {}

    #[Route('/style/{name}')]
    public function __invoke(string $name): JsonResponse
    {
        $theme = $this->factory->get($name);
        if (!$theme) {
            throw new NotFoundHttpException;
        }
        return $this->json([
            'version' => 8,
            'name' => $theme->getName(),
            'metadata' => [
                'maputnik:renderer' => 'mbgljs'
            ],
            'sources' => [
                'base' => [
                    'type' => 'vector',
                    'tiles' => [
                        "{$theme->getHost()}/tiles/{x}/{y}/{z}"
                    ],
                    'minZoom' => 0,
                    'maxZoom' => 14,
                    'maxzoom' => 22
                ],
                'continents' => [
                    'type' => 'geojson',
                    'data' => json_decode(file_get_contents("{$this->kernel->getProjectDir()}/continents.json"))
                ]
            ],
            'sprite' => "{$theme->getHost()}/{$theme->getSprite()}",
            'glyphs' => "{$theme->getHost()}/{$theme->getGlyphs()}",
            'layers' => [
                [
                    'id' => 'ground',
                    'type' => 'background',
                    'paint' => $theme->getPaint('ground')
                ],
                [
                    'id' => 'continents',
                    'type' => 'fill',
                    'source' => 'continents',
                    'paint' => $theme->getPaint('continents')
                ],
                [
                    'id' => 'sand',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'sand',
                    'paint' => $theme->getPaint('sand')
                ],
                [
                    'id' => 'water',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'water',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('water')
                ],
                [
                    'id' => 'waves',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'water',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('waves')
                ],
                [
                    'id' => 'river',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'river',
                    'paint' => $theme->getPaint('river')
                ],
                [
                    'id' => 'secondary',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'secondary',
                    'paint' => $theme->getPaint('secondary')
                ],
                [
                    'id' => 'embarkment',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'embarkment',
                    'layout' => [
                        'line-cap' => 'round',
                        'line-join' => 'round'
                    ],
                    'paint' => $theme->getPaint('embarkment')
                ],
                [
                    'id' => 'bridge-shadow',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'bridge',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => [
                        'line-width' => 2,
                        'line-opacity' => 0.3,
                        'line-translate' => [
                            3,
                            3
                        ],
                        'line-blur' => 0.5
                    ]
                ],
                [
                    'id' => 'bridge',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'bridge',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('bridge')
                ],
                [
                    'id' => 'primary',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'primary',
                    'paint' => $theme->getPaint('primary')
                ],
                [
                    'id' => 'residential',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'residential',
                    'paint' => $theme->getPaint('residential')
                ],
                [
                    'id' => 'road',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'road',
                    'layout' => [
                        'visibility' => 'visible'
                    ]
                ],
                [
                    'id' => 'railway',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'railway',
                    'paint' => $theme->getPaint('railway')
                ],
                [
                    'id' => 'tunnel',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'tunnel'
                ],
                [
                    'id' => 'sidewalk',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'sidewalk'
                ],
                [
                    'id' => 'wall',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'wall',
                    'layout' => [
                        'visibility' => 'visible'
                    ]
                ],
                [
                    'id' => 'battlefield',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'battlefield',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('battlefield')
                ],
                [
                    'id' => 'brownfield',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'brownfield',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('brownfield')
                ],
                [
                    'id' => 'scrub',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'scrub',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('scrub')
                ],
                [
                    'id' => 'heath',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'heath',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('heath')
                ],
                [
                    'id' => 'grassland',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'grassland',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('grassland')
                ],
                [
                    'id' => 'grass',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'grass',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('grass')
                ],
                [
                    'id' => 'greenfield',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'greenfield',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('greenfield')
                ],
                [
                    'id' => 'island',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'island',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('island')
                ],
                [
                    'id' => 'islet',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'islet',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('islet')
                ],
                [
                    'id' => 'platform',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'platform',
                    'layout' => [
                        'visibility' => 'visible'
                    ]
                ],
                [
                    'id' => 'playground',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'playground',
                    'paint' => $theme->getPaint('playground')
                ],
                [
                    'id' => 'mall',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'mall',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('mall')
                ],
                [
                    'id' => 'building',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'building',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('building')
                ],
                [
                    'id' => 'building_exrtusion',
                    'type' => 'fill-extrusion',
                    'source' => 'base',
                    'source-layer' => 'building',
                    'paint' => $theme->getPaint('structure'),
                    'minzoom' => 10
                ],
                [
                    'id' => 'structure',
                    'type' => 'fill-extrusion',
                    'source' => 'base',
                    'source-layer' => 'structure',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('structure')
                ],
                [
                    'id' => 'building_labels',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'building',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'addr:housenumber'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'point',
                        'visibility' => 'visible',
                        'text-size' => ['stops' => [[14, 0], [16, 14]]],
                        'text-padding' => 10
                    ],
                    'paint' => $theme->getPaint('labels'),
                    'minzoom' => 12
                ],
                [
                    'id' => 'farmland',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'farmland',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('farmland')
                ],
                [
                    'id' => 'cemetery',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'cemetery',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('cemetery')
                ],
                [
                    'id' => 'school',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'school',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('school')
                ],
                [
                    'id' => 'hospital',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'hospital',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('hospital')
                ],
                [
                    'id' => 'fire_station',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'fire_station',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('fire_station')
                ],
                [
                    'id' => 'dog_park',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'dog_park',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('dog_park')
                ],
                [
                    'id' => 'allotments',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'allotments',
                    'paint' => $theme->getPaint('allotments')
                ],
                [
                    'id' => 'apartment',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'apartment',
                    'layout' => [
                        'visibility' => 'visible'
                    ]
                ],
                [
                    'id' => 'wood',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'wood',
                    'paint' => $theme->getPaint('wood')
                ],
                [
                    'id' => 'trees',
                    'type' => 'fill',
                    'source' => 'base',
                    'source-layer' => 'wood',
                    'paint' => $theme->getPaint('trees')
                ],
                [
                    'id' => 'residential_labels',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'residential',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 14,
                        'text-keep-upright' => true,
                        'text-justify' => 'center'
                    ],
                    'paint' => $theme->getPaint('labels')
                ],
                [
                    'id' => 'primary_labels',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'primary',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 16,
                        'text-keep-upright' => true
                    ],
                    'paint' => $theme->getPaint('labels')
                ],
                [
                    'id' => 'secondary_labels',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'secondary',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 15,
                        'text-writing-mode' => [],
                        'text-keep-upright' => true
                    ],
                    'paint' => $theme->getPaint('labels')
                ],
                [
                    'id' => 'village',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'village',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 16,
                        'text-padding' => 100,
                    ],
                    'paint' => $theme->getPaint('labels'),
                    'minzoom' => 0,
                    'maxzoom' => 12,
                ],
                [
                    'id' => 'town',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'town',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 18,
                        'text-padding' => 100,
                    ],
                    'paint' => $theme->getPaint('labels'),
                    'minzoom' => 0,
                    'maxzoom' => 12,
                ],
                [
                    'id' => 'city',
                    'type' => 'symbol',
                    'source' => 'base',
                    'source-layer' => 'city',
                    'layout' => [
                        'text-font' => $theme->getFont(),
                        'text-field' => [
                            'type' => 'identity',
                            'property' => 'name'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => 20,
                        'text-padding' => 100,
                    ],
                    'paint' => $theme->getPaint('labels'),
                    'minzoom' => 0,
                    'maxzoom' => 12,
                ]
            ],
            'id' => 'heymoon-base-tiles'
        ], 200, ['Access-Control-Allow-Origin' => $this->origin]);
    }
}
