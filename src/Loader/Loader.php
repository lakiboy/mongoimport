<?php

namespace Devmachine\MongoImport\Loader;

use Devmachine\MongoImport\Exception\FileNotFoundException;

abstract class Loader
{
    /**
     * @param string $filePath
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    public function loadFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw FileNotFoundException::forFile($filePath);
        }

        $contents = file_get_contents($filePath);

        return $this->load($contents);
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @param string $contents
     *
     * @return array
     *
     * @throws \Devmachine\MongoImport\Exception\InvalidImportDataException
     */
    abstract protected function load($contents);
}
