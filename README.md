# School Disciplinary System API

A PHP-based REST API for managing school disciplinary records.

## Features
- Authentication system with role-based access (Admin, Staff, Student)
- Student management
- Staff management  
- Incident reporting and tracking
- Judgment management
- Analytics and reporting
- File upload support for supporting documents

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache with mod_rewrite enabled
- XAMPP (recommended) or similar stack

## Installation

### 1. Setup Project
```bash
# Clone repository
git clone <repository-url>
cd disciplinary-system

# Create uploads directory
mkdir uploads
chmod 755 uploads  # Linux/Mac
# OR
icacls uploads /grant Users:F  # Windows
```

### 2. Database Setup

#### Option 1: Using MySQL Workbench
1. Open MySQL Workbench
2. Create new connection if needed:
   - Connection Name: `XAMPP MySQL`
   - Hostname: `127.0.0.1`
   - Port: `3307`
   - Username: `root`
3. Open connection
4. Open and execute `database/setup.sql`
5. Open and execute `database/seed.sql`

#### Option 2: Using Command Line
```bash
cd database
mysql -u root -P 3307 < setup.sql
mysql -u root -P 3307 disciplinary_system < seed.sql
```

#### Option 3: Using phpMyAdmin
1. Open `http://localhost/phpmyadmin`
2. Create new database `disciplinary_system`
3. Import `setup.sql`
4. Import `seed.sql`

### 3. Configuration
1. Copy config template:
```bash
cp config/config.example.php config/config.php
```

2. Update configuration as needed:
```php
return [
    'db_host' => 'localhost',
    'db_port' => '3307',
    'db_name' => 'disciplinary_system',
    'db_user' => 'root',
    'db_pass' => '',
    'upload_dir' => __DIR__ . '/../uploads/',
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']
];
```

## Directory Structure
```
disciplinary-system/
├── api/
│   ├── admin/           # Admin endpoints
│   │   ├── incidents/
│   │   ├── staff/
│   │   └── students/
│   ├── auth/           # Authentication endpoints
│   ├── staff/          # Staff endpoints
│   └── student/        # Student endpoints
├── config/            # Configuration files
├── database/          # Database setup and seeds
├── middleware/        # Authentication middleware
├── models/            # Data models
├── uploads/          # File uploads directory
└── utils/            # Utility classes
```

## Test Credentials
```
Admin:
- Username: admin
- Password: password123

Staff:
- Username: teacher1
- Password: password123

Student:
- Username: 2024001
- Password: password123
```

## Development Setup
1. Configure XAMPP:
   - Apache port: 80
   - MySQL port: 3307
2. Place project in `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
3. Access API at `http://localhost/disciplinary-system/api/`

## Testing
Use Postman or similar tool to test the API endpoints. Import the provided Postman collection from `docs/postman_collection.json`.