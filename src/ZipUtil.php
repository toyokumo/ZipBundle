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
        if ($this->zip->open($path) !== true) {
            throw new RuntimeException('Unknown file format');
        }
        $dir = implode(['/tmp', '/', uniqid('zip')]);
        try {
            $this->zip->extractTo($dir);

            if ($this->zip->numFiles === 0) {
                throw new RuntimeException('Extracted file is empty');
            }

            $files = [];
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $files[] = implode([$dir, '/', $this->zip->getNameIndex($i)]);
            }

            return [$dir, $files];
        } catch (Exception $e) {
            throw new RuntimeException('Can not extract file');
        } finally {
            $this->zip->close();
        }
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
