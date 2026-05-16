<?php

namespace Core;

class Request
{
    private string $method;
    private string $path;
    private array  $data;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path   = $this->parsePath();
        $this->data   = $this->parseData();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    private function parsePath(): string
    {
        $path     = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        return $position === false ? $path : substr($path, 0, $position);
    }

    private function parseData(): array
    {
        $data        = $_GET;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if ($this->method !== 'GET') {
            if (str_contains($contentType, 'application/json')) {
                $json = json_decode(file_get_contents('php://input'), true);
                $data = array_merge($data, $json ?? []);
            } elseif (
                str_contains($contentType, 'application/x-www-form-urlencoded') ||
                str_contains($contentType, 'multipart/form-data')
            ) {
                if ($this->method === 'POST') {
                    $data = array_merge($data, $_POST);
                } else {
                    parse_str(file_get_contents('php://input'), $parsed);
                    $data = array_merge($data, $parsed);
                }
            }
        }

        return $data;
    }
}

