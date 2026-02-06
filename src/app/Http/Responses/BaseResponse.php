<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseResponse implements Arrayable
{
    protected bool $success = true;
    protected mixed $data = null;
    protected ?string $message = null;
    protected array $meta = [];
    protected array $links = [];
    protected int $statusCode = Response::HTTP_OK;

    public function __construct(
        mixed $data = null,
        ?string $message = null,
        bool $success = true
    ) {
        $this->data = $data;
        $this->message = $message;
        $this->success = $success;
    }

    public static function success(
        mixed $data = null,
        ?string $message = null
    ): self {
        return new static($data, $message, true);
    }

    public static function error(
        ?string $message = null,
        mixed $errors = null,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): self {
        $response = new static($errors, $message, false);
        $response->statusCode = $statusCode;
        return $response;
    }

    public function withMeta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    public function withLinks(array $links): self
    {
        $this->links = array_merge($this->links, $links);
        return $this;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'message' => $this->message,
            'meta' => $this->meta,
            'links' => $this->links,
            'timestamp' => now()->toISOString(),
        ];
    }

    public function toResponse(): JsonResponse
    {
        return response()->json(
            $this->toArray(),
            $this->statusCode
        );
    }
}
