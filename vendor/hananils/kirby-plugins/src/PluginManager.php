<?php

namespace Hananils\Plugins;

use Kirby\Cms\App as Kirby;
use Kirby\Data\Data;
use Kirby\Plugin\Plugin;
use SplFileInfo;

/**
 * Plugin Manager autoloads plugin configuration from the file system:
 *
 * translation: /translations/{code}.{php|json}
 * blueprints: /blueprints/{name}.yml (including subfolders)
 * snippets: /snippets/{name}.php (including subfolders)
 * config: /config/{name}
 * methods: /methods/{type}.php
 */
class PluginManager
{
    private string $vendor = 'hananils';
    private string $id;
    private string $root;
    private array $info;
    private string $name = '';
    private string $version = '';
    private array $configuration = [];
    private mixed $license = null;

    public function __construct(string $id, string $root, mixed $license = null)
    {
        $this->id = $id;
        $this->root = $root;
        $this->info = Data::read($this->root . '/composer.json', fail: false);
        $this->license = $license;
    }

    /**
     * Shorthand to autoload plugin.
     */
    public static function autoload(string $id, string $root): Plugin
    {
        $manager = new self($id, $root);

        return $manager->load();
    }

    /**
     * Loads the plugin.
     */
    public function load(): Plugin|null
    {
        return Kirby::plugin(
            root: $this->root,
            name: $this->name(),
            extends: $this->configuration(),
            version: $this->version(),
            license: $this->license()
        );
    }

    /**
     * Returns the plugin name, adding the vendor prefix to the id, if missing.
     */
    public function name(): string
    {
        if ($this->name === '') {
            $this->name = $this->id;

            if (!str_contains($this->name, '/')) {
                $this->name = "$this->vendor/$this->id";
            }
        }

        return $this->name;
    }

    /**
     * Returns the plugin version defined in composer.json.
     */
    public function version(): string
    {
        if ($this->version === '') {
            $this->version = $this->info['version'] ?? '0.0.1dev';
        }

        return $this->version;
    }

    /**
     * Returns the plugin license.
     */
    public function license(): mixed
    {
        if (
            $this->license === null &&
            array_key_exists('license', $this->info)
        ) {
            $this->license = $this->info['license'];
        }

        return $this->license;
    }

    /**
     * Creates a configuration array by reading information from the file system.
     */
    public function configuration(): array
    {
        // Discover plugin configuration
        $this->discoverTranslations();
        $this->discoverReferences('blueprints');
        $this->discoverReferences('snippets');
        $this->discoverDefinitions('config');
        $this->discoverDefinitions('methods', 'Methods');

        return array_filter($this->configuration);
    }

    /**
     * Discovers translations, either reading arrays from JSON or PHP files.
     */
    private function discoverTranslations(): void
    {
        $path = $this->root . '/translations';
        $directory = new Discovery($path);

        $this->ensureConfigurationGroup('translations');

        foreach ($directory as $file) {
            $name = $this->getName($file);
            $pathname = $file->getPathname();

            if ($file->getExtension() === 'json') {
                $translations = Data::read($pathname, fail: false);
            } else {
                $translations = require $pathname;
            }

            $this->configuration['translations'][$name] = $translations;
        }
    }

    /**
     * Discovers group references, mapping folders to paths.
     */
    private function discoverReferences(string $group): void
    {
        $path = $this->root . "/$group";
        $directory = new Discovery($path);
        $id = $this->id;

        $this->ensureConfigurationGroup($group);

        foreach ($directory as $file) {
            if ($file->isDir()) {
                $subdirectory = new Discovery($file->getPathname());
                $subfolder = $file->getFilename();

                foreach ($subdirectory as $subfile) {
                    $name = $this->getName($subfile);

                    // It's important to list the subfolder first to comply
                    // with Kirby's naming conventions. For instance, file
                    // blueprints are expected to be located at `/files`
                    // without any prefix
                    $this->configuration[$group][
                        "$subfolder/$id/$name"
                    ] = $subfile->getPathname();
                }

                continue;
            }

            $name = $this->getName($file);

            $this->configuration[$group]["$id/$name"] = $file->getPathname();
        }
    }

    /**
     * Discovers group definitions, requiring files.
     */
    private function discoverDefinitions(
        string $group,
        string $suffix = ''
    ): void {
        $path = $this->root . "/$group";
        $directory = new Discovery($path);

        foreach ($directory as $file) {
            $name = $this->getName($file);

            if ($suffix !== '') {
                $name .= $suffix;
            }

            $this->configuration[$name] = require $file->getPathname();
        }
    }

    /**
     * Ensures that the given group name is defined on the configuration object.
     */
    private function ensureConfigurationGroup(string $group): void
    {
        if (!isset($this->configuration[$group])) {
            $this->configuration[$group] = [];
        }
    }

    /**
     * Gets the file's basename without extension.
     */
    private function getName(SplFileInfo $file): string
    {
        return $file->getBasename('.' . $file->getExtension());
    }
}
