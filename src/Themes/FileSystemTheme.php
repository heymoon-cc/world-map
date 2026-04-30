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

    public function getPaint(string $fieldName, array $fallback = ['fill-color' => '#000']): array
    {
        return $this->configuration['paint'][$fieldName] ?? $fallback;
    }

    public function getSprite(): string
    {
        return $this->configuration['sprite'];
    }

    public function getGlyphs(): string
    {
        return $this->configuration['glyphs'];
    }

    public function getFont(): array
    {
        return $this->configuration['font'];
    }

    public function getHost()
    {
        return $this->configuration['host'];
    }
}
