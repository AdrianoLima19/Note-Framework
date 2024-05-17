<?php

declare(strict_types=1);

namespace Note;

use Exception;

class Filesystem
{
    const PUBLIC_FILE = 0644;
    const PRIVATE_FILE = 0600;
    const PUBLIC_DIR = 0755;
    const PRIVATE_DIR = 0700;

    /**
     * @param  string $path
     *
     * @return bool
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * @param  string $path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @param  string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @param  string $path
     *
     * @return string
     *
     * @throws Exception
     */
    public function read(string $path): string
    {
        if (!is_file($path) || !$content = file_get_contents($path)) {
            throw new Exception("Unable to read file at: \"{$path}\"", 1);
        }

        return $content;
    }

    /**
     * @param  string $path
     * @param  string $content
     * @param  int    $visibility
     *
     * @return int
     *
     * @throws Exception
     */
    public function write(string $path, string $content, int $visibility = self::PUBLIC_FILE): int
    {
        if (!$bytes = file_put_contents($path, $content)) {
            throw new Exception("Unable to write to file: \"{$path}\"", 1);
        }

        $this->setVisibility($path, $visibility);

        return $bytes;
    }

    /**
     * @param  string $path
     * @param  int    $visibility
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createDirectory(string $path, int $visibility = self::PUBLIC_DIR): bool
    {
        if (is_dir($path)) {
            $this->setVisibility($path, $visibility);

            return true;
        }

        if ($visibility !== self::PUBLIC_DIR && $visibility !== self::PRIVATE_DIR) {
            throw new Exception('To set directory visibility, use either "PUBLIC_DIR" or "PRIVATE_DIR".', 1);
        }

        if (!mkdir($path, $visibility, true)) {
            throw new Exception("Unable to write to file: \"{$path}\"", 1);
        }

        return true;
    }

    /**
     * @param  string $path
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(string $path): bool
    {
        return is_file($path) ? $this->deleteFile($path) : $this->deleteDirectory($path);
    }

    /**
     * @param  string $path
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteFile(string $path): bool
    {
        if (!is_file($path) || !unlink($path)) {
            throw new Exception("Unable to delete file: \"{$path}\"", 1);
        }

        return true;
    }

    /**
     * @param  string $path
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            throw new Exception("Directory does not exist: \"{$path}\"", 1);
        }

        $this->deleteDirectoryRecursively($path);

        return true;
    }

    /**
     * @param  string $path
     * @param  int    $visibility
     *
     * @return bool
     *
     * @throws Exception
     */
    public function setVisibility(string $path, int $visibility): bool
    {
        if (!file_exists($path)) {
            throw new Exception("Unable to find file or directory: \"{$path}\"", 1);
        }

        if (is_file($path)) {
            if ($visibility !== self::PUBLIC_FILE && $visibility !== self::PRIVATE_FILE) {
                throw new Exception('To change file visibility, use either "PUBLIC_FILE" or "PRIVATE_FILE".', 1);
            }
        } else {
            if ($visibility !== self::PUBLIC_DIR && $visibility !== self::PRIVATE_DIR) {
                throw new Exception('To change directory visibility, use either "PUBLIC_DIR" or "PRIVATE_DIR".', 1);
            }
        }

        if (chmod($path, $visibility)) {
            throw new Exception("Error Processing Request", 1);
        }

        return true;
    }

    public function getVisibility()
    {
    }

    public function move()
    {
    }

    public function copy()
    {
    }

    public function fileSize()
    {
    }

    public function lastModified()
    {
    }

    public function listFiles()
    {
    }

    /**
     * @param  string $path
     *
     * @return void
     *
     * @throws Exception
     */
    protected function deleteDirectoryRecursively(string $path): void
    {
        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            $filePath = "$path/$file";
            if (is_dir($filePath)) {
                $this->deleteDirectoryRecursively($filePath);
            } else {
                if (!unlink($filePath)) {
                    throw new Exception("Unable to delete file: \"{$filePath}\"");
                }
            }
        }

        if (!rmdir($path)) {
            throw new Exception("Unable to delete directory: \"{$path}\"", 1);
        }
    }
}
