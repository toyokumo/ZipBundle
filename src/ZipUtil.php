<?php

namespace Toyokumo\ZipBundle;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ZipUtil
{
    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function unzip(string $path): array
    {
        $this->zip->open($path);
        $dir = '/tmp/' . uniqid('zip');
        $this->zip->extractTo($dir);
        $files = [];
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $files[] = $dir . '/' . $this->zip->getNameIndex($i);
        }
        $this->zip->close();
        return [$dir, $files];
    }

    /**
     * @param string $dir
     * @throws Exception
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
