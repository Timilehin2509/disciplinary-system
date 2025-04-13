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
        // Fix: Use getInstance() instead of new Database()
        $database = Database::getInstance();
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
        try {
            $this->conn->beginTransaction();
            
            // Check unique constraints
            if ($this->usernameExists($data->username)) {
                throw new Exception('Username already exists');
            }
            
            // Hash password
            $data->password = password_hash($data->password, PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (username, password, role, name, email) 
                    VALUES (:username, :password, :role, :name, :email)";
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                'username' => $data->username,
                'password' => $data->password,
                'role' => $data->role,
                'name' => $data->name,
                'email' => $data->email
            ]);
            
            if (!$success) {
                throw new Exception('Database insert failed');
            }
            
            $id = $this->conn->lastInsertId();
            $this->conn->commit();
            return $id;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
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

    public function usernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}