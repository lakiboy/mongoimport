<?php

namespace Doctrine\MongoDB\Importer\Reader;

use Doctrine\MongoDB\Importer\Exception\FileNotFoundException;

abstract class Reader
{
    /**
     * @param string $filePath
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    public function readFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw FileNotFoundException::forFile($filePath);
        }

        $contents = file_get_contents($filePath);

        return $this->read($contents);
    }

    /**
     * @param string $contents
     *
     * @return array
     *
     * @throws \Doctrine\MongoDB\Importer\Exception\InvalidImportDataException
     */
    abstract protected function read($contents);
}
