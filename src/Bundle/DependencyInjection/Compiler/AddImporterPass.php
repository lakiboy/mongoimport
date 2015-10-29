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

        $names   = array_keys($container->getParameterBag()->resolveValue('%doctrine_mongodb.odm.document_managers%'));
        $default = $container->getParameterBag()->resolveValue('%doctrine_mongodb.odm.default_document_manager%');
        $config  = $container->getDefinition(sprintf('doctrine_mongodb.odm.%s_configuration', $default));

        // Set default database.
        foreach ($config->getMethodCalls() as $call) {
            if ($call[0] === 'setDefaultDB') {
                $container
                    ->getDefinition('devmachine_mongoimport.importer.factory')
                    ->replaceArgument(2, $call[1][0])
                ;
                break;
            }
        }

        foreach ($names as $name) {
            $decorator = new DefinitionDecorator('devmachine_mongoimport.importer');
            $decorator->replaceArgument(0, $name);
            $container->setDefinition('devmachine_mongoimport.'.$name, $decorator);
        }

        $container->setAlias('devmachine_mongoimport', 'devmachine_mongoimport.'.$default);
    }
}
