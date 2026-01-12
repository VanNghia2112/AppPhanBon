<?php
// app/models/User.php

class User
{
    private $conn;
    private $table = "account";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function all()
    {
        $sql = "SELECT id, username, fullname, role FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $sql = "SELECT id, username, fullname, role FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (username, fullname, password, role)
                VALUES (:username, :fullname, :password, :role)";
        $stmt = $this->conn->prepare($sql);

        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        return $stmt->execute([
            ':username' => $data['username'],
            ':fullname' => $data['fullname'],
            ':password' => $hashed,
            ':role'     => $data['role']
        ]);
    }

    public function update($id, $data)
    {
        // Nếu có nhập mật khẩu mới thì update, không thì giữ nguyên
        if (!empty($data['password'])) {
            $sql = "UPDATE {$this->table}
                    SET username = :username,
                        fullname = :fullname,
                        password = :password,
                        role = :role
                    WHERE id = :id";
            $params = [
                ':username' => $data['username'],
                ':fullname' => $data['fullname'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':role'     => $data['role'],
                ':id'       => $id
            ];
        } else {
            $sql = "UPDATE {$this->table}
                    SET username = :username,
                        fullname = :fullname,
                        role = :role
                    WHERE id = :id";
            $params = [
                ':username' => $data['username'],
                ':fullname' => $data['fullname'],
                ':role'     => $data['role'],
                ':id'       => $id
            ];
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
