<?php

namespace App\Controllers;

use Core\Request;
use Models\Users;

class UsersController
{

    public function index(Request $request) {
        $users=new Users();
        $allUsers=$users->getAllUsers();
        if ($allUsers) {
            return $allUsers;
        } else {
            return "No users found.";
        }
        
    }
    public function show(Request $request, $id) {
        $userModel=new Users();
        $user=$userModel->getUserById($id);
        if ($user) {
            return $user;
        } else {
            return "User not found.";
        }
    }
}
