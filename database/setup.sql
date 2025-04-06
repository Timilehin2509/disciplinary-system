CREATE DATABASE IF NOT EXISTS disciplinary_system;
USE disciplinary_system;

-- Users table for admin and staff
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Incidents table
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Academic','Behavioral','Attendance','Other') NOT NULL,
    description TEXT NOT NULL,
    date_of_incidence DATE NOT NULL,
    date_reported DATE NOT NULL,
    status ENUM('Open','Investigate','Closed') NOT NULL DEFAULT 'Open',
    supporting_documents TEXT,  -- File path(s) or JSON array of uploaded document links
    reporter_id INT NOT NULL,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Incident_Students junction table with judgment details
CREATE TABLE incident_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    student_id INT NOT NULL,
    punishment ENUM('No Punishment', 'Suspension', 'Expulsion', 'Community Service'),
    details TEXT,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);