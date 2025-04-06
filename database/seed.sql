-- Insert admin and staff users (password is 'password123' hashed)
INSERT INTO users (username, password, role, name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'admin@school.com'),
('teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'John Teacher', 'john@school.com'),
('teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'Jane Teacher', 'jane@school.com');

-- Insert students (password is 'password123' hashed)
INSERT INTO students (student_number, name, email, class, password) VALUES
('2024001', 'Alice Smith', 'alice@student.com', '12A', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('2024002', 'Bob Johnson', 'bob@student.com', '12A', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('2024003', 'Charlie Brown', 'charlie@student.com', '11B', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('2024004', 'Diana Wilson', 'diana@student.com', '11B', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert incidents
INSERT INTO incidents (type, description, date_of_incidence, date_reported, status, reporter_id, updated_by) VALUES
('Academic', 'Caught cheating during math exam', '2024-03-20', '2024-03-20', 'Open', 2, NULL),
('Behavioral', 'Disrupting class repeatedly', '2024-03-21', '2024-03-21', 'Investigate', 2, 1),
('Attendance', 'Skipped entire school day', '2024-03-22', '2024-03-22', 'Closed', 3, 1),
('Other', 'Unauthorized use of mobile phone', '2024-03-23', '2024-03-23', 'Open', 2, NULL);

-- Insert incident-student relationships with judgments
INSERT INTO incident_students (incident_id, student_id, punishment, details) VALUES
(1, 1, 'No Punishment', 'First warning issued'),
(1, 2, 'Suspension', '2-day suspension and parent meeting'),
(2, 3, 'Community Service', '10 hours library duty'),
(3, 4, 'Suspension', '1-day suspension'),
(4, 1, NULL, NULL);