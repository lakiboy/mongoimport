<?php

namespace Doctrine\MongoDB\Importer\Reader;

use Doctrine\MongoDB\Importer\Exception\InvalidImportDataException;

class PrettyJsonReader extends Reader
{
    private $asArray;

    public function __construct($asArray = true)
    {
        $this->asArray = $asArray;
    }

    /**
     * @param string $contents
     *
     * @return array
     */
    protected function read($contents)
    {
        $contents = static::fixJson($contents);

        if (null === $data = json_decode($contents, $this->asArray)) {
            throw InvalidImportDataException::fromJson(
                json_last_error(),
                json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * @param string $contents
     *
     * @return string
     */
    private static function fixJson($contents)
    {
        return '['.preg_replace('/^\}$\s+\{$/m', '},{', $contents).']';
    }
}
