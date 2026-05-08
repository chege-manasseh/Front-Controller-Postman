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
        $data = $_GET;

        if ($this->method !== 'GET') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

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

        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }

        return $data;
    }
}
