<?php

namespace App\Themes;

interface ThemeInterface
{
    public function getName(): string;
    public function getColor(string $fieldName, string $fallback = '#000'): string;
    public function getSprite(): string;
    public function getGlyphs(): string;
}
