<?php

namespace Igsem\Docs\Services;

use Phalcon\Config;
use Phalcon\Di\Service;

class DocsService extends Service
{
    /**
     * Get options for scan
     *
     * @param array $config
     * @return array
     */
    public static function getOptions(array $config)
    {
        $options = [];
        if (array_key_exists('exclude', $config)) {
            $options['exclude'] = self::getExclude($config['exclude']);
        }
        return $options;
    }

    /**
     * Get exclude options property
     *
     * @param mixed $exclude
     * @return string|array|null
     */
    protected static function getExclude($exclude)
    {
        if (is_string($exclude)) {
            return $exclude;
        }
        if ($exclude instanceof Config) {
            return $exclude->toArray();
        }
        return null;
    }
}