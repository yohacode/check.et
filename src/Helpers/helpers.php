<?php

if (!function_exists("env")) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? ($_SERVER[$key] ?? getenv($key));

        if ($value === false || $value === null) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists("config")) {
    function config(string $key, mixed $default = null): mixed
    {
        static $config = [];

        if (empty($config)) {
            foreach (glob(__DIR__ . "/../../config/*.php") as $file) {
                $name = basename($file, ".php");
                $config[$name] = require $file;
            }
        }

        return array_get($config, $key, $default);
    }
}

if (!function_exists("array_get")) {
    function array_get(array $array, string $key, mixed $default = null): mixed
    {
        $keys = explode(".", $key);

        foreach ($keys as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}
