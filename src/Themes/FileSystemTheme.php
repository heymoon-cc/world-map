<?php

namespace App\Themes;

class FileSystemTheme implements ThemeInterface
{
    private array $configuration;

    public function __construct(string $path) {
        $this->configuration = json_decode(file_get_contents($path), true) ?: [];
    }

    public function getName(): string
    {
        return $this->configuration['name'];
    }

    public function getColor(string $fieldName, string $fallback = '#000'): string
    {
        return $this->configuration['colors'][$fieldName] ?? $fallback;
    }

    public function getSprite(): string
    {
        return $this->configuration['sprite'];
    }

    public function getGlyphs(): string
    {
        return $this->configuration['glyphs'];
    }
}
