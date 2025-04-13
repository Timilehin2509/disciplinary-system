<?php
require_once __DIR__ . '/../config/database.php';

class Incident {
    private $conn;
    private $table = 'incidents';

    public $id;
    public $type;
    public $description;
    public $date_of_incidence;
    public $date_reported;
    public $status;
    public $supporting_documents;
    public $reporter_id;
    public $updated_by;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAll($filters = []) {
        $query = "SELECT i.*, u.name as reporter_name 
                FROM " . $this->table . " i
                LEFT JOIN users u ON i.reporter_id = u.id";
        
        if (!empty($filters['status'])) {
            $query .= " WHERE i.status = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT i.*, u.name as reporter_name,
                GROUP_CONCAT(DISTINCT s.id, ':', s.name, ':', IFNULL(is.punishment, 'None'), ':', IFNULL(is.details, '')) as students
                FROM " . $this->table . " i
                LEFT JOIN users u ON i.reporter_id = u.id
                LEFT JOIN incident_students is ON i.id = is.incident_id
                LEFT JOIN students s ON is.student_id = s.id
                WHERE i.id = :id
                GROUP BY i.id";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        // Validate type
        $valid_types = ['Academic', 'Behavioral', 'Attendance', 'Other'];
        if (!in_array($data->type, $valid_types)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table . "
                (type, description, date_of_incidence, date_reported, status, supporting_documents, reporter_id)
                VALUES (:type, :description, :date_of_incidence, :date_reported, :status, :supporting_documents, :reporter_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':type', $data->type);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':date_of_incidence', $data->date_of_incidence);
        $stmt->bindParam(':date_reported', $data->date_reported);
        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':supporting_documents', $data->supporting_documents);
        $stmt->bindParam(':reporter_id', $data->reporter_id);

        if($stmt->execute()) {
            $incident_id = $this->conn->lastInsertId();
            
            // Insert student relationships
            if(!empty($data->students)) {
                $this->addStudentsToIncident($incident_id, $data->students);
            }
            
            return $incident_id;
        }
        return false;
    }

    private function addStudentsToIncident($incident_id, $students) {
        $query = "INSERT INTO incident_students (incident_id, student_id) VALUES (:incident_id, :student_id)";
        $stmt = $this->conn->prepare($query);
        
        foreach($students as $student_id) {
            $stmt->bindParam(':incident_id', $incident_id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
        }
    }

    public function updateStatus($id, $status, $updated_by) {
        // Validate status
        $valid_statuses = ['Open', 'Investigate', 'Closed'];
        if (!in_array($status, $valid_statuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . "
                SET status = :status, updated_by = :updated_by
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':updated_by', $updated_by);
        
        return $stmt->execute();
    }

    private function validatePunishment($punishment) {
        $valid_punishments = ['No Punishment', 'Suspension', 'Expulsion', 'Community Service'];
        return in_array($punishment, $valid_punishments);
    }

    public function updateJudgments($incident_id, $judgments) {
        // Validate punishment types
        foreach($judgments as $judgment) {
            if (!$this->validatePunishment($judgment->punishment)) {
                return false;
            }
        }
        
        try {
            $this->conn->beginTransaction();
            
            foreach($judgments as $judgment) {
                $query = "UPDATE incident_students 
                        SET punishment = :punishment, details = :details
                        WHERE incident_id = :incident_id AND student_id = :student_id";

                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    ':incident_id' => $incident_id,
                    ':student_id' => $judgment->student_id,
                    ':punishment' => $judgment->punishment,
                    ':details' => $judgment->details
                ]);
            }
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    public function getCountByType() {
        $query = "SELECT type, COUNT(*) as count 
                 FROM " . $this->table . "
                 GROUP BY type
                 ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTrend($days = 30) {
        $query = "SELECT DATE(date_reported) as date, COUNT(*) as count 
                 FROM " . $this->table . "
                 WHERE date_reported >= DATE_SUB(CURRENT_DATE, INTERVAL :days DAY)
                 GROUP BY DATE(date_reported)
                 ORDER BY date_reported";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByReporterId($reporter_id) {
        $query = "SELECT i.*, 
                GROUP_CONCAT(DISTINCT s.name) as involved_students,
                COUNT(DISTINCT is.student_id) as student_count
                FROM " . $this->table . " i
                LEFT JOIN incident_students is ON i.id = is.incident_id
                LEFT JOIN students s ON is.student_id = s.id
                WHERE i.reporter_id = :reporter_id
                GROUP BY i.id
                ORDER BY i.created_at DESC";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reporter_id', $reporter_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStudentIncidents($student_id) {
        $query = "SELECT i.*, 
                 is.punishment,
                 is.details as judgment_details,
                 u.name as reporter_name
                 FROM incident_students is
                 JOIN " . $this->table . " i ON i.id = is.incident_id
                 LEFT JOIN users u ON i.reporter_id = u.id
                 WHERE is.student_id = :student_id
                 ORDER BY i.date_reported DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}