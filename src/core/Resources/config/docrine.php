<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine) {


    // Пример подключения Doctrine Custom Type
    // $doctrine->dbal()->type(CustomType::TYPE)->class(CustomType::class);

    $NAMESPACE = 'App\Core\Entity';

    $PATH = substr(__DIR__, 0, strpos(__DIR__, "Resources"));

    $emDefault = $doctrine
        ->orm()
        ->entityManager('default')
        ->autoMapping(true);

    $emDefault
        ->mapping('core')
        ->type('attribute')
        ->dir($PATH.'/Entity')
        ->isBundle(false)
        ->prefix($NAMESPACE)
        ->alias('core');
};
