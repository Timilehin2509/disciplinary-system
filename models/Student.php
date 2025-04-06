<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $conn;
    private $table = 'students';

    public $id;
    public $student_number;
    public $name;
    public $email;
    public $class;
    public $password;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($student_number, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE student_number = :student_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_number', $student_number);
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

    public function getAll() {
        $query = "SELECT id, student_number, name, email, class FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT id, student_number, name, email, class FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                (student_number, name, email, class, password) 
                VALUES (:student_number, :name, :email, :class, :password)";

        $stmt = $this->conn->prepare($query);
        
        $data->password = password_hash($data->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':student_number', $data->student_number);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':class', $data->class);
        $stmt->bindParam(':password', $data->password);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($data) {
        $query = "UPDATE " . $this->table . " 
                SET name = :name, email = :email, class = :class 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $data->id);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':class', $data->class);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}