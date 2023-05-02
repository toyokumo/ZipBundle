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
        $res = [$dir, $files] = $z->unzip('./tests/fixtures/test.zip');
        /*
         * unzip return value must be like as
         * Array(
         *  [0] => "/tmp/zip~"
         *  [1] => Array([0]=> /tmp/zip~)
         * )
         */
        $this->assertIsArray($res);
        $this->assertCount(2, $res);
        $this->assertStringStartsWith('/tmp/zip', $dir);
        $this->assertIsArray($files);
        $this->assertStringStartsWith('/tmp/zip', $files[0]);

        //Additional test for ZipUtil::clean()
        $this->assertTrue(file_exists($dir));
        $z->clean($dir);
        $this->assertFalse(file_exists($dir));
    }
    public static function createNotZipfilePathProvider(): array
    {
        return [
            // text file, not zip file.
            ['path' => './tests/fixtures/test.txt'],
            // text file renamed as zip file.
            ['path' => './tests/fixtures/not-zip.zip'],
        ];
    }
    /**
     * @dataProvider createNotZipfilePathProvider
     */
    public function testUnzipMustThrowExceptionWithoutZip($path): void
    {
        $z = new ZipUtil();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not open the file as zip archive.');

        $res = $z->unzip($path);
    }
}
