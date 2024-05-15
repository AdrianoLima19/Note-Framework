<?php

declare(strict_types=1);

namespace Note\Core;

final class Env
{
    public static function parseDotEnv(string $basePath)
    {
        if (file_exists("{$basePath}/.env.local")) {
            $file = "{$basePath}/.env.local";
        } else if (file_exists("{$basePath}/.env")) {
            $file = "{$basePath}/.env";
        } else return;

        $data = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($data as $row) {
            if (strpos(trim($row), '#') === 0) continue;

            list($name, $value) = explode('=', $row, 2);

            $name = trim($name);
            $value = trim($value, '"\' ');

            if (empty($value)) {
                $value = null;
            } else if ('null' === strtolower($value)) {
                $value = null;
            } else if ('true' === strtolower($value)) {
                $value = true;
            } else if ('false' === strtolower($value)) {
                $value = false;
            } else if (preg_match('/^\d+$/', $value)) {
                $value = intval($value);
            } else if (preg_match('/^-?\d*\.?\d+$/', $value)) {
                $value = floatval($value);
            }

            $env[$name] = $value;
        }

        return $env;
    }
}
