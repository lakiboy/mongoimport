<?php

namespace Devmachine\MongoImport\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AddImportersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine_mongodb')) {
            return;
        }

        $managers = $container
            ->getParameterBag()
            ->resolveValue('%doctrine_mongodb.odm.connections%')
        ;
        $defaultManager = $container
            ->getParameterBag()
            ->resolveValue('%doctrine_mongodb.odm.default_document_manager%')
        ;
        $defaultDatabase = null;

        // Get default database.
        $config = $container->getDefinition(sprintf('doctrine_mongodb.odm.%s_configuration', $defaultManager));
        foreach ($config->getMethodCalls() as $call) {
            if ($call[0] === 'setDefaultDB') {
                $defaultDatabase = $call[1][0];
                break;
            }
        }

        foreach ($managers as $name => $id) {
            $decorator = new DefinitionDecorator('devmachine_mongoimport.importer.base');
            $decorator->replaceArgument(0, new Reference($id));
            $decorator->replaceArgument(1, $defaultDatabase);
            $container->setDefinition('devmachine_mongoimport.'.$name, $decorator);
        }

        $container->setAlias('devmachine_mongoimport', 'devmachine_mongoimport.'.$defaultManager);
    }
}
