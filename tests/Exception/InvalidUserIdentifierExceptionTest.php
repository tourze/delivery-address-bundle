<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\DeliveryAddressBundle\Exception\InvalidUserIdentifierException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidUserIdentifierException::class)]
final class InvalidUserIdentifierExceptionTest extends AbstractExceptionTestCase
{
    public function testConstructorWithEmptyIdentifier(): void
    {
        $exception = new InvalidUserIdentifierException('');

        $this->assertSame('用户标识符不能为空', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithInvalidIdentifier(): void
    {
        $identifier = 'invalid_user_123';
        $exception = new InvalidUserIdentifierException($identifier);

        $this->assertSame('用户标识符 "invalid_user_123" 无效', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithPreviousException(): void
    {
        $previous = new \RuntimeException('Previous error');
        $exception = new InvalidUserIdentifierException('test', $previous);

        $this->assertSame('用户标识符 "test" 无效', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testInheritsFromRuntimeException(): void
    {
        $exception = new InvalidUserIdentifierException('test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
