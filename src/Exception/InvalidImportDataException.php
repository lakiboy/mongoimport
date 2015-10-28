<?php

namespace Devmachine\MongoImport\Exception;

class InvalidImportDataException extends \RuntimeException
{
    /**
     * @param int    $code
     * @param string $cause
     *
     * @return static
     */
    public static function fromError($code, $cause = null)
    {
        if (!$cause) {
            switch ($code) {
                case JSON_ERROR_DEPTH:
                    $cause = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $cause = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $cause = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $cause = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $cause = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $cause = 'Unknown error';
            }
        }

        return new static(sprintf('Unable to decode json: %s', $cause), $code);
    }
}
