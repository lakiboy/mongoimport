<?php

namespace Devmachine\MongoImport\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class AddImporterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine_mongodb')) {
            return;
        }

        $names = array_keys($container->getParameterBag()->resolveValue('%doctrine_mongodb.odm.connections%'));

        foreach ($names as $name) {
            $decorator = new DefinitionDecorator('mongoimport.importer');
            $decorator->replaceArgument(0, $name);
            $container->setDefinition('mongoimport.importer.'.$name, $decorator);
        }
    }
}
