<?php declare(strict_types=1);
namespace Toyokumo\ZipBundle;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Exception;
use ZipArchive;

final class ZipUtilTest extends TestCase
{
    public function testUnzipWithValidZip(): void
    {
        $z = new ZipUtil();
        $res = $z->unzip('./tests/fixtures/test.zip');
        /*
         * unzip return value must be like as
         * Array(
         *  [0] => "/tmp/zip~"
         *  [1] => Array([0]=> /tmp/zip~)
         * )
         */
        $this->assertIsArray($res);
        $this->assertIsArray($res[1]);
        $this->assertCount(2, $res);
        $this->assertStringStartsWith('/tmp/zip', $res[0]);
        $this->assertStringStartsWith('/tmp/zip', $res[1][0]);
    }
    public function testUnzipMustThrowExceptionWithoutZip(): void
    {
        $z = new ZipUtil();
        $this->expectExceptionMessage('Can not open the file as zip archive.');

        $res = $z->unzip('./tests/fixtures/test.txt');
    }
    public function testUnzipMustThrowExceptionWithRenamedZip(): void
    {
        $z = new ZipUtil();
        $this->expectExceptionMessage('Can not open the file as zip archive.');

        $res = $z->unzip('./tests/fixtures/notzip.zip');
    }
}
