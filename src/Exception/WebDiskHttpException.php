<?php

declare(strict_types=1);

namespace BlueGrid\Exception;

class WebDiskHttpException extends \RuntimeException
{
    public function __construct(private int $httpCode = 0, string $message = "")
    {
        parent::__construct($message);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}