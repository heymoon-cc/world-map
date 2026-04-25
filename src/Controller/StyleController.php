<?php

namespace App\Controller;

use App\Factory\ThemeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class StyleController extends AbstractController
{
    public function __construct(
        private readonly string $origin,
        private readonly string $hostname,
        private readonly ThemeFactory $factory
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
                        "https://$this->hostname/tiles/{x}/{y}/{z}"
                    ],
                    'minZoom' => 0,
                    'maxZoom' => 14,
                    'maxzoom' => 22
                ]
            ],
            'sprite' => $theme->getSprite(),
            'glyphs' => $theme->getGlyphs(),
            'layers' => [
                [
                    'id' => 'sand',
                    'type' => 'circle',
                    'source' => 'base',
                    'source-layer' => 'sand',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
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
                    'layout' => [
                        'visibility' => 'visible'
                    ]
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
                    'layout' => [
                        'visibility' => 'visible'
                    ]
                ],
                [
                    'id' => 'residential',
                    'type' => 'line',
                    'source' => 'base',
                    'source-layer' => 'residential',
                    'layout' => [
                        'visibility' => 'visible'
                    ]
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
                    'type' => 'circle',
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
                    'paint' => $theme->getPaint('structure')
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
                            'property' => 'label'
                        ],
                        'text-ignore-placement' => false,
                        'text-allow-overlap' => false,
                        'text-pitch-alignment' => 'auto',
                        'text-rotation-alignment' => 'auto',
                        'text-transform' => 'none',
                        'text-optional' => false,
                        'symbol-placement' => 'line',
                        'visibility' => 'visible',
                        'text-size' => ['stops' => [[14, 0], [16, 14]]]
                    ],
                    'paint' => $theme->getPaint('labels')
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
                    'type' => 'circle',
                    'source' => 'base',
                    'source-layer' => 'wood',
                    'layout' => [
                        'visibility' => 'visible'
                    ],
                    'paint' => $theme->getPaint('wood')
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
                ]
            ],
            'id' => 'heymoon-base-tiles'
        ], 200, ['Access-Control-Allow-Origin' => $this->origin]);
    }
}
