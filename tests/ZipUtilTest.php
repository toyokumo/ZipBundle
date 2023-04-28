<?php declare(strict_types=1);
namespace Toyokumo\ZipBundle;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Exception;
use ZipArchive;

final class ZipUtilTest extends TestCase
{
    public function testUnzipValidZip(): void
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
        $z->unzip('/dummy/dir');
    }
		public static function createInvalidErrorCodeProvider(): array
		{
			# See https://www.php.net/manual/en/ziparchive.open.php about Error Code.
			for($i=1; $i<21; $i++){
				$errCode[] = ['zipArchiveErrCode' => $i];
			}
			return $errCode;

		}
		/**
		 * @dataProvider createInvalidErrorCodeProvider
		 */
    public function testUnzipRaiseErrorDuringOpening(int $zipArchiveErrCode): void
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
				$this->expectExceptionMessage("Unknown file format");
				
        $z->unzip('/dummy/dir');
    }
    public function testUnzipRaiseErrorDuringExtracting(): void
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
				$this->expectExceptionMessage("Can not extract file:");

        $mock->expects($this->once())->method('extractTo')->willThrowException(new Exception());
        $z->unzip('/dummy/dir');
    }
}
