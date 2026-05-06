<?php

namespace Core;

class Request
{

    //Your Request class should answer:
    // What method is this?
    // What data came in?
    // In what format?
    // Give me a clean way to access it

    private $method;
    private $path;
    private $data;
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->getPath();
        $this->data = $this->getData();
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function getData()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // 1. Always start with URL parameters (GET)
        $this->data = $_GET;

        // 2. Handle Body Data based on Content-Type
        if ($this->method !== 'GET') {
            if (str_contains($contentType, 'application/json')) {
                // Handle JSON (API requests)
                $json = json_decode(file_get_contents('php://input'), true);
                $this->data = array_merge($this->data, $json ?? []);
            } elseif (
                str_contains($contentType, 'application/x-www-form-urlencoded') ||
                str_contains($contentType, 'multipart/form-data')
            ) {

                // Handle Form Data (Standard MVC Forms)
                // POST is auto-filled by PHP, but PUT/PATCH are not.
                if ($this->method === 'POST') {
                    $this->data = array_merge($this->data, $_POST);
                } else {
                    // For PUT/PATCH form-encoded data
                    parse_str(file_get_contents('php://input'), $parsed);
                    $this->data = array_merge($this->data, $parsed);
                }
            }
        }

        // 3. Include File Uploads (Optional but helpful for MVC)
        if (!empty($_FILES)) {
            $this->data = array_merge($this->data, $_FILES);
        }

        return $this->data;
    }
}
