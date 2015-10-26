<?php

namespace Devmachine\MongoImport;

final class Util
{
    /**
     * @param array $doc
     *
     * @return array
     */
    public static function replaceMongoIds(array $doc)
    {
        foreach ($doc as $key => $val) {
            if (is_array($val)) {
                if (isset($val['$oid'])) {
                    $doc[$key] = new \MongoId($val['$oid']);
                } else {
                    $doc[$key] = self::replaceMongoIds($val);
                }
            }
        }

        return $doc;
    }

    private function __construct()
    {
    }
}
