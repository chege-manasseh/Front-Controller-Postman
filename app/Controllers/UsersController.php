<?php

namespace App\Controllers;

use Core\Request;
use Models\Users;

class UsersController
{
    /**
     * GET /users
     * Returns all users from the CSV dataset.
     */
    public function index(Request $request): array
    {
        $users = (new Users())->getAllUsers();

        if (empty($users)) {
            http_response_code(404);
            return ['error' => 'No users found'];
        }

        return ['data' => $users, 'count' => count($users)];
    }

    /**
     * GET /users/{id}
     * Returns a single user by ID.
     */
    public function show(Request $request, string $id): array
    {
        if (!ctype_digit($id) || (int)$id <= 0) {
            http_response_code(400);
            return ['error' => 'Invalid ID — must be a positive integer'];
        }

        $user = (new Users())->getUserById((int)$id);

        if ($user === null) {
            http_response_code(404);
            return ['error' => "User with ID {$id} not found"];
        }

        return ['data' => $user];
    }

    /**
     * POST /users
     * Appends a new user row to the CSV dataset.
     * Body (JSON or form-data): name, email
     */
    public function store(Request $request): array
    {
        $data = $request->getData();
        $name  = trim($data['name']  ?? '');
        $email = trim($data['email'] ?? '');

        if ($name === '' || $email === '') {
            http_response_code(422);
            return ['error' => 'Missing required fields', 'required' => ['name', 'email']];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            return ['error' => 'Invalid email address'];
        }

        $result = (new Users())->addUser($name, $email);

        if (!$result) {
            http_response_code(500);
            return ['error' => 'Failed to write user to dataset'];
        }

        http_response_code(201);
        return ['message' => 'User added successfully', 'data' => ['name' => $name, 'email' => $email]];
    }

    /**
     * POST /users/import
     * Accepts a CSV file upload (field: usersFile) and stores it.
     */
    public function import(Request $request): array
    {
        $uploadDir = __DIR__ . '/../../storage/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $request->file('usersFile');

        if ($file === null) {
            http_response_code(400);
            return ['error' => 'No file received', 'hint' => "Send a multipart/form-data POST with field name 'usersFile'"];
        }

        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server size limit',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form size limit',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was sent',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension',
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            return ['error' => $uploadErrors[$file['error']] ?? 'Unknown upload error'];
        }

        $fileInfo    = pathinfo($file['name']);
        $extension   = strtolower($fileInfo['extension'] ?? '');

        if ($extension !== 'csv') {
            http_response_code(422);
            return ['error' => 'Only CSV files are accepted'];
        }

        $safeName    = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileInfo['filename']);
        $destination = $uploadDir . $safeName . '_' . date('Ymd') . '.csv';

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            http_response_code(500);
            return ['error' => 'Failed to store uploaded file'];
        }

        http_response_code(201);
        return ['message' => 'File imported successfully', 'filename' => basename($destination)];
    }
}