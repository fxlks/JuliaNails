<?php

namespace AmeliaBooking\Infrastructure\Services\Mailchimp;

use RuntimeException;

class MailchimpRequestException extends RuntimeException
{
    private string $endpoint;
    private string $method;
    private int $status;
    private ?string $responseBody;

    public function __construct(
        string $message,
        string $endpoint,
        string $method,
        int $status = 0,
        ?string $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->status = $status;
        $this->responseBody = $responseBody;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
