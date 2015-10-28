<?php

namespace Devmachine\MongoImport\Bundle;

use Devmachine\MongoImport\Importer;
use Doctrine\Common\Persistence\ConnectionRegistry;

class ImporterFactory
{
    private $doctrine;
    private $loaders;

    /**
     * @param ConnectionRegistry                      $doctrine
     * @param \Devmachine\MongoImport\Loader\Loader[] $loaders
     */
    public function __construct(ConnectionRegistry $doctrine, array $loaders)
    {
        $this->doctrine = $doctrine;
        $this->loaders = $loaders;
    }

    public function getImporter($name = 'default')
    {
        /* @var \Doctrine\MongoDB\Connection $dm */
        $dm = $this->doctrine->getConnection($name);

        return new Importer($dm, $this->loaders);
    }
}
