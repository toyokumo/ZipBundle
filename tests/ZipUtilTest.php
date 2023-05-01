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
        $mock = $this->getMockBuilder(ZipArchive::class)
            ->onlyMethods(['open', 'extractTo'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('open')
            ->willReturn(true);

        $mock->expects($this->once())->method('extractTo');

        $z = new ZipUtil($mock);
        $res = $z->unzip('/dummy/dir');
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
    }
    public static function createInvalidErrorCodeProvider(): array
    {
        # See https://www.php.net/manual/en/ziparchive.open.php about Error Code.
        for ($i = 1; $i < 24; $i++) {
            $errCode[] = ['zipArchiveErrCode' => $i];
        }
        return $errCode;
    }
    /**
     * @dataProvider createInvalidErrorCodeProvider
     */
    public function testUnzipMustCatchExceptionDuringOpening(int $zipArchiveErrCode): void
    {
        $mock = $this->getMockBuilder(ZipArchive::class)
            ->onlyMethods(['open', 'extractTo'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('open')
            ->willReturn($zipArchiveErrCode);

        $z = new ZipUtil($mock);
        $mock->expects($this->never())->method('extractTo');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown file format');

        $z->unzip('/dummy/dir');
    }
    public function testUnzipMustCatchExceptionDuringExtracting(): void
    {
        $mock = $this->getMockBuilder(ZipArchive::class)
            ->onlyMethods(['open', 'extractTo'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('open')
            ->willReturn(true);

        $z = new ZipUtil($mock);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not extract file:');

        $mock
            ->expects($this->once())
            ->method('extractTo')
            ->willThrowException(new Exception());
        $z->unzip('/dummy/dir');
    }
}
