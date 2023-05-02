<?php

namespace Toyokumo\ZipBundle;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

class ZipUtil
{
    /**
     * @var ZipArchive
     */
    private $zip;

    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    /**
     * Extracts the contents of the specified file and returns the path to the extracted directory.
     *
     * @param string $path The path to the file to extract.
     * @return array The path to the directory where the file contents were extracted.
     * @throws RuntimeException If the file cannot be opened or extracted.
     */
    public function unzip(string $path): array
    {
        $res = $this->zip->open($path);
        if ($res !== true) {
            throw new RuntimeException('Can not open the file as zip archive. Code: ' . $res);
        }
        $dir = sprintf('/tmp/%s', uniqid('zip'));
        try {
            $this->zip->extractTo($dir);
        } catch (Exception $e) {
            throw new RuntimeException('Can not extract file: ' . $e);
        }

        $files = [];
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $files[] = sprintf('%s/%s', $dir, $this->zip->getNameIndex($i));
        }
        if (!$this->zip->close()) {
            throw new RuntimeException('Can not close the file:');
        }

        return [$dir, $files];
    }

    /**
     * Remove the contents in the specified directory recursively.
     * @param string $dir The path to the directory to remove recursively.
     */
    public function clean(string $dir): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir() === true) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }
}
