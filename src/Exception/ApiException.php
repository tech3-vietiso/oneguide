<?php

namespace Vietiso\OneGuide\Exception;

use Throwable;

class ApiException extends OneGuideException
{
    private int $statusCode;

    private array $errors;

    private ?array $response;

    public function __construct(
        string $message,
        int $statusCode = 0,
        array $errors = [],
        ?array $response = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->response = $response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Mảng lỗi validate server trả về (thường ở key "errors"). Rỗng nếu không có.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Có lỗi validate hay không.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Toàn bộ body JSON đã decode (null nếu response không phải JSON).
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}
