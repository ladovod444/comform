<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\TwigConfig;

return static function(TwigConfig $twig) {

    $PATH = substr(__DIR__, 0, strpos(__DIR__, "Resources"));

    $twig->path(
        $PATH.'Resources/view',
        'products-favorite'
    );

};
