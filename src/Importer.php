<?php

namespace Devmachine\MongoImport;

use Devmachine\MongoImport\Exception\ImportException;
use Devmachine\MongoImport\Exception\UnsupportedFileFormatException;
use Devmachine\MongoImport\Loader\Loader;
use Doctrine\MongoDB\Connection;

class Importer
{
    private $mongo;

    /**
     * @var Loader[]
     */
    private $loaders = [];

    public function __construct(Connection $mongo, array $loaders = [])
    {
        $this->mongo = $mongo;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function addLoader(Loader $loader)
    {
        $this->loaders[$loader->getName()] = $loader;
    }

    /**
     * @return Loader[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param string $db
     * @param string $name
     * @param string $filePath
     *
     * @return int
     *
     * @throws UnsupportedFileFormatException
     * @throws ImportException
     */
    public function importCollection($db, $name, $filePath)
    {
        $format = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!isset($this->loaders[$format])) {
            throw UnsupportedFileFormatException::forFormat($format);
        }

        $data = $this->loaders[$format]->loadFile($filePath);
        $data = array_map('Devmachine\MongoImport\ExtendedJson::fromStrict', $data);

        $result = $this->mongo->selectCollection($db, $name)->batchInsert($data, [
            'safe' => 1,
        ]);

        if ($result['err']) {
            throw ImportException::fromServerError($result['err']);
        }

        return count($data);
    }
}
