<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $role;
    public $name;
    public $email;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password'])) {
                foreach($row as $key => $value) {
                    if(property_exists($this, $key)) {
                        $this->$key = $value;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllStaff() {
        $query = "SELECT id, username, name, email, created_at, updated_at 
                 FROM " . $this->table . " 
                 WHERE role = 'staff'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                (username, password, role, name, email)
                VALUES (:username, :password, :role, :name, :email)";

        $stmt = $this->conn->prepare($query);
        
        $data->password = password_hash($data->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $data->username);
        $stmt->bindParam(':password', $data->password);
        $stmt->bindParam(':role', $data->role);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($data) {
        $query = "UPDATE " . $this->table . "
                SET name = :name, email = :email
                WHERE id = :id AND role = :role";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $data->id);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':role', $data->role);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND role = 'staff'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}