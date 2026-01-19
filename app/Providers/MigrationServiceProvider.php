<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerMigrationPaths();
    }

    /**
     * Register migration paths from database config.
     */
    protected function registerMigrationPaths(): void
    {
        $migrationConfig = config('database.migrations', []);
        // Базовые пути
        $paths = $migrationConfig['paths'] ?? [database_path('migrations')];


        // Автоматическое включение подпапок
        if ($migrationConfig['include_subdirectories'] ?? false) {
            $paths = array_merge($paths, $this->getSubdirectoryPaths($paths));
        }

        // Убираем дубликаты и проверяем существование
        $paths = array_unique($paths);
        $paths = array_filter($paths, function ($path) {
            return File::exists($path);
        });

        // Регистрируем пути
        if (!empty($paths)) {
            $this->loadMigrationsFrom($paths);
        }
    }

    /**
     * Get all subdirectories from given paths.
     */
    protected function getSubdirectoryPaths(array $basePaths): array
    {
        $subPaths = [];

        foreach ($basePaths as $path) {
            if (!File::exists($path)) {
                continue;
            }

            $directories = File::directories($path);
            $subPaths = array_merge($subPaths, $directories);
        }

        return $subPaths;
    }
}
