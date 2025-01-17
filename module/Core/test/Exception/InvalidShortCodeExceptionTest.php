<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Core\Exception\InvalidShortCodeException;
use Throwable;

class InvalidShortCodeExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePrevious
     */
    public function properlyCreatesExceptionFromCharset(?Throwable $prev): void
    {
        $e = InvalidShortCodeException::fromCharset('abc123', 'def456', $prev);

        $this->assertEquals('Provided short code "abc123" does not match the char set "def456"', $e->getMessage());
        $this->assertEquals($prev !== null ? $prev->getCode() : -1, $e->getCode());
        $this->assertEquals($prev, $e->getPrevious());
    }

    public function providePrevious(): iterable
    {
        yield 'null previous' => [null];
        yield 'instance previous' => [new Exception('Previous error', 10)];
    }

    /** @test */
    public function properlyCreatesExceptionFromNotFoundShortCode(): void
    {
        $e = InvalidShortCodeException::fromNotFoundShortCode('abc123');

        $this->assertEquals('Provided short code "abc123" does not belong to a short URL', $e->getMessage());
    }
}
