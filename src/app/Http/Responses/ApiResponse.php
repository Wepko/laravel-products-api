<?php

// app/Http/Responses/ApiResponse.php
namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;


//ToDo: Тут будет Swagger Api для всего проекта!
class ApiResponse
{
    private array $meta = [];
    private array $headers = [];

    public function __construct(
        private mixed $data = null,
        private string $message = 'Success',
        private int $status = 200,
        private bool $success = true
    ) {}

    public static function make(): self
    {
        return new self();
    }

    public function data(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function status(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function success(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    public function addMeta(string $key, mixed $value): self
    {
        $this->meta[$key] = $value;
        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function toResponse(): JsonResponse
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];

        // Если есть мета-данные, добавляем их
        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        return response()->json($response, $this->status, $this->headers);
    }
}
