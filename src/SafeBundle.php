<?php

namespace MartenaSoft\WarehouseSafe;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SafeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container); // TODO: Change the autogenerated stub
    }

    public static function getConfigName(): string
    {
        return 'safe';
    }
}