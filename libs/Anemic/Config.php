<?php

declare(strict_types=1);

namespace Anemic {
    class Config
    {
        /**
         * Config values
         *
         * @var array<string, string> $values
         */
        static array $values = [];

        /**
         * Name of the configuration file
         */
        static string $config_file = "";

        /**
         * Get a value from the configuration file
         * 
         * @param string $key value name to get from the configuration file
         * @param string $def default value if the key is not found or is empty. Default = ""
         * 
         * @return string
         */
        static function Get(string $key, string $def = ""): string
        {
            if (empty(static::$values)) {
                assert(! empty(static::$config_file));
                $tmp = parse_ini_file(static::$config_file);
                if ($tmp !== false) {
                    static::$values = $tmp;
                }
            }

            return static::$values[$key] ?? $def;
        }
    }
}
