# School Disciplinary System API

A PHP-based REST API for managing school disciplinary records.

## Project Overview
A comprehensive disciplinary management system that allows schools to:
- Track student disciplinary incidents
- Manage incident reports and judgments
- Generate analytics and reports
- Maintain secure role-based access

## Features
- Authentication system with role-based access (Admin, Staff, Student)
- Student management
- Staff management  
- Incident reporting and tracking
- Judgment management
- Analytics and reporting
- File upload support for supporting documents

## System Architecture
- Built on PHP 7.4+ backend
- MySQL database for data persistence
- Session-based authentication
- Role-based access control (RBAC)
- RESTful API architecture

## Security Features
- Encrypted passwords using PHP's password_hash()
- Session timeout after 30 minutes
- Role-based access control
- SQL injection prevention using PDO
- File upload validation
- XSS protection

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

## Troubleshooting Guide

### Common Issues

1. **Database Connection Errors**
   - Verify MySQL is running on port 3307
   - Check credentials in config.php
   - Ensure database exists

2. **File Upload Issues**
   - Check uploads directory permissions
   - Verify file size < 5MB
   - Ensure file type is supported
   - Check PHP upload_max_filesize setting

3. **API Access Issues**
   - Verify Apache mod_rewrite is enabled
   - Check .htaccess file exists
   - Confirm correct file permissions
   - Verify session is active

### Quick Fixes
```bash
# Enable mod_rewrite
sudo a2enmod rewrite  # Linux
# For Windows: Uncomment LoadModule rewrite_module in httpd.conf

# Fix permissions
chmod 755 -R api/     # Linux
chmod 777 uploads/    # Linux
# Windows: Right-click > Properties > Security > Edit > Add Users > Full Control
```

## Development Guidelines
- Follow PSR-4 autoloading standard
- Use meaningful variable/function names
- Add comments for complex logic
- Validate all inputs
- Handle errors gracefully
- Log important operations

## Contributing
1. Fork the repository
2. Create feature branch
3. Follow coding standards
4. Add tests if applicable
5. Submit pull request

## License
MIT License - See LICENSE file