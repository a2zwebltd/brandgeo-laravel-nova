<?php

namespace A2ZWeb\BrandGeoNova\Support;

use RuntimeException;

class EnvWriter
{
    /**
     * Set (or append) a key in the host app's .env file. Values are quoted.
     */
    public static function set(string $key, string $value): void
    {
        $path = app()->environmentFilePath();

        if (! is_writable($path)) {
            throw new RuntimeException("The environment file [{$path}] is not writable.");
        }

        $contents = file_get_contents($path);
        $line = $key.'="'.addcslashes($value, '"\\').'"';

        if (preg_match("/^{$key}=.*$/m", $contents)) {
            $contents = preg_replace("/^{$key}=.*$/m", $line, $contents);
        } else {
            $contents = rtrim($contents, PHP_EOL).PHP_EOL.PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($path, $contents);
    }
}
