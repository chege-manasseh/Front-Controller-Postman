<?php

namespace Models;
use Exception;
class Users extends Model
{
    private string $dataFile = __DIR__ . '/../../storage/data/users.csv';

    public function getAllUsers(): array
    {
        $handle  = fopen($this->dataFile, 'r');
        $headers = array_map('strtolower', fgetcsv($handle));
        $users   = [];

        while (($row = fgetcsv($handle)) !== false) {
            $users[] = array_combine($headers, $row);
        }

        fclose($handle);
        return $users;
    }

    public function getUserById(int $id): ?array
    {
        $handle  = fopen($this->dataFile, 'r');
        $headers = array_map('strtolower', fgetcsv($handle));

        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($headers, $row);
            if ((int)$record['id'] === $id) {
                fclose($handle);
                return $record;
            }
        }

        fclose($handle);
        return null;
    }

    public function addUser(string $name, string $email): bool
    {
        $handle  = fopen($this->dataFile, 'r');
        $headers = array_map('strtolower', fgetcsv($handle));
        fclose($handle);

        // Auto-increment ID from existing rows
        $all    = $this->getAllUsers();
        $nextId = empty($all) ? 1 : max(array_column($all, 'id')) + 1;

        $row = [];
        foreach ($headers as $col) {
            $row[] = match ($col) {
                'id'    => $nextId,
                'name'  => $name,
                'email' => $email,
                default => '',
            };
        }

        $handle = fopen($this->dataFile, 'a');
        if ($handle === false) {
            return false;
        }

        try
        {
            fputcsv($handle, $row);
        } catch (Exception $e) {
            return false;
        } finally {
            fclose($handle);
        }
        return true;
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