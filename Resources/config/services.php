<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Vinatis\TypeScriptGeneratorBundle\Command\GenerateAllCommand;
use Vinatis\TypeScriptGeneratorBundle\Command\GenerateInterfaceCommand;
use Vinatis\TypeScriptGeneratorBundle\Command\GeneratePackageCommand;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('vinatis.type_script_generator_all', GenerateAllCommand::class)
        ->args([new Reference('parameter_bag')])
        ->tag('console.command');

    $services->set('vinatis.type_script_generator_interface', GenerateInterfaceCommand::class)
        ->args([new Reference('parameter_bag')])
        ->tag('console.command');

    $services->set('vinatis.type_script_generator_package', GeneratePackageCommand::class)
        ->args([new Reference('parameter_bag')])
        ->tag('console.command');
};