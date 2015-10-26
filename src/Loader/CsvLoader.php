<?php

namespace Devmachine\MongoImport\Loader;

class CsvLoader extends Loader
{
    public function getName()
    {
        return 'csv';
    }

    protected function load($contents)
    {
    }
}
