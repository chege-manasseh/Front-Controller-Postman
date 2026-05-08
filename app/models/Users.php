<?php

namespace Models;

class Users extends Model
{


    public  function checkCredentials($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    public function createUser() {}

    public function getUserById($id)
    {
        $filename = __DIR__ . '/../../storage/data/messy_users.csv';
        $handle = fopen($filename, "r");

        // 1. Read the very first row (the headers)
        $rawheaders = fgetcsv($handle);
        $headers = array_map('strtolower', $rawheaders);
        // 2. Find where "id" is located (it might be at 0, 1, or 5!)
        $idIndex = array_search('id', $headers);

        if ($idIndex === false) {
            die("Error: This CSV doesn't have an 'id' column.");
        }

        // 3. Now loop through the rest of the data
        while (($data = fgetcsv($handle)) !== false) {
            // Use the dynamic index 
            if ($data[$idIndex] == $id) {
                fclose($handle);
                return array_combine($headers, $data);
            }
        }
        fclose($handle);
        return null;
    }

    public function getAllUsers()
    {
        $filename = __DIR__ . '/../../storage/data/messy_users.csv';
        $handle = fopen($filename, 'r');
        $headers = array_map('strtolower', fgetcsv($handle));
        $users = [];
        while (($row = fgetcsv($handle)) !== false) {
            $users[] = array_combine($headers, $row);
        }
        fclose($handle);
        return $users;
    }
}





























   // $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        // $stmt->execute(['username' => $username]);
        // $user = $stmt->fetch();

        // if ($user && password_verify($password, $user['password'])) {
        //     return $user;
        // } else {
        //     return false;
        // }