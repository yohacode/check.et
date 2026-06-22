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
            foreach (glob(__DIR__ . "/../config/*.php") as $file) {
                $name = basename($file, ".php");
                $config[$name] = require $file;
            }
        }

        return data_get($config, $key, $default);
    }
}
