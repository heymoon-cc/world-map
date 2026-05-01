<?php

namespace App\Repository;

use App\Kernel;
use App\Themes\FileSystemTheme;

readonly class ThemeRepository
{
    public function __construct(private Kernel $kernel) {}

    public function get(string $name): ?FileSystemTheme
    {
        $path = "{$this->kernel->getProjectDir()}/themes/$name.json";
        if (file_exists($path)) {
            return new FileSystemTheme($path);
        }
        return null;
    }
}
