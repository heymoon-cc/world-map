<?php

namespace App\Themes;

interface ThemeInterface
{
    public function getName(): string;
    public function getPaint(string $fieldName, array $fallback = ['fill-color' => '#000']): array;
    public function getSprite(): string;
    public function getGlyphs(): string;
}
