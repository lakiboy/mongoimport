<?php

namespace Devmachine\MongoImport\Bundle;

use Devmachine\MongoImport\Importer;
use Doctrine\Common\Persistence\ConnectionRegistry;

class ImporterFactory
{
    private $doctrine;
    private $loaders;
    private $defaultDatabase;

    /**
     * @param ConnectionRegistry                      $doctrine
     * @param \Devmachine\MongoImport\Loader\Loader[] $loaders
     * @param string                                  $defaultDatabase
     */
    public function __construct(ConnectionRegistry $doctrine, array $loaders, $defaultDatabase)
    {
        $this->doctrine = $doctrine;
        $this->loaders = $loaders;
        $this->defaultDatabase = $defaultDatabase;
    }

    public function getImporter($name = 'default')
    {
        /* @var \Doctrine\MongoDB\Connection $dm */
        $dm = $this->doctrine->getConnection($name);

        $importer = new Importer($dm, $this->loaders);
        $importer->setDefaultDatabase($this->defaultDatabase);

        return $importer;
    }
}
