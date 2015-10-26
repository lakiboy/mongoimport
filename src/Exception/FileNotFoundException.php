<?php

namespace Devmachine\MongoImport\Exception;

class FileNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string $filePath
     *
     * @return static
     */
    public static function forFile($filePath)
    {
        return new static(sprintf('File "%s" not found.', $filePath));
    }
}
