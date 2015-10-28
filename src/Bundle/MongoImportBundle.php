<?php

namespace Devmachine\MongoImport\Bundle;

use Devmachine\MongoImport\Bundle\DependencyInjection\Compiler\AddImporterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MongoImportBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddImporterPass());
    }
}
