<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function(RoutingConfigurator $routes) {

    $PATH = substr(__DIR__, 0, strpos(__DIR__, "Resources"));

    $routes->import($PATH.'/Controller', 'attribute')
        ->prefix(\BaksDev\Core\Type\Locale\Locale::routes())
        ->namePrefix('products-favorite:');
};
