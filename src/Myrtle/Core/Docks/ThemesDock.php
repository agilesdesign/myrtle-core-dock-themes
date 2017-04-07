<?php

namespace Myrtle\Core\Docks;

use Myrtle\Themes\Providers\ThemesServiceProvider;

class ThemesDock extends Dock
{
    /**
     * Description for Dock
     *
     * @var string
     */
    public $description = 'Theme management';

    /**
     * List of providers to be registered
     *
     * @var array
     */
    public $providers = [
        ThemesServiceProvider::class,
    ];

    /**
     * List of config file paths to be loaded
     *
     * @return array
     */
    public function configPaths()
    {
        return [
            'docks.' . self::class => dirname(__DIR__, 3) . '/config/docks/themes.php',
            'abilities' => dirname(__DIR__, 3) . '/config/abilities.php',
        ];
    }
}
