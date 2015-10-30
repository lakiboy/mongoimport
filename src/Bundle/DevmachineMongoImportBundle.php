<?php

namespace Devmachine\MongoImport\Bundle;

use Devmachine\MongoImport\Bundle\DependencyInjection\Compiler\AddImportersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DevmachineMongoImportBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddImportersPass());
    }
}
