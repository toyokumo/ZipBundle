<?php

namespace Toyokumo\ZipBundle;

use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class StreamedZipResponse extends StreamedResponse
{
    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * @var string
     */
    private $zipName;

    /**
     * Constructor.
     * @param string $zipName
     * @throws Exception
     */
    public function __construct(string $zipName)
    {
        $this->zipName = $zipName;
        $this->zip = new ZipArchive();
        parent::__construct(null, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment;filename="' . $this->zipName . '"',
        ]);
    }

    /**
     * @param string $name
     * @param string $content
     */
    public function fromString(string $name, string $content): void
    {
        $this->zip->open($this->zipName, ZipArchive::CREATE);
        $this->zip->addFromString($name, $content);
        $this->zip->close();
        $this->setCallback(function () {
            try {
                readfile($this->zipName);
            } finally {
                unlink($this->zipName);
            }
        });
    }
}
