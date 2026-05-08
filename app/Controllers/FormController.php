<?php
namespace App\Controllers;
use Models\Users;
use Core\Request;
class FormController {
    public function Login(Request $request) {
        // For demonstration, we'll just return the received data.
        // In a real application, you'd likely save this to a database.
        $data = $request->getData();
        $username = $data['username'] ?? 'Guest';
        $password = $data['password'] ?? '';
        $userModel = new Users();
        $user = $userModel->checkCredentials($username, $password);
        if ($user) {
            return [
                'status' => 'success',
                'message' => "Welcome, $username!"
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "Invalid credentials for $username."
            ];
        }
    }
}
