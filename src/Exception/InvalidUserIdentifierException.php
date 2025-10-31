<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Exception;

class InvalidUserIdentifierException extends \RuntimeException
{
    public function __construct(string $identifier = '', ?\Throwable $previous = null)
    {
        $message = '' === $identifier
            ? '用户标识符不能为空'
            : sprintf('用户标识符 "%s" 无效', $identifier);

        parent::__construct($message, 0, $previous);
    }
}
