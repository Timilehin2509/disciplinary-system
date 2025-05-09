# API Documentation

## Base URL
```
http://localhost/disciplinary-system/api
```

## Authentication

### Login
```http
POST /login

Description: Authenticate user (admin, staff, or student)
Content-Type: application/json

Request Body:
{
    "username": "string",
    "password": "string"
}

Response (200):
{
    "message": "Login successful",
    "user": {
        "id": number,
        "name": "string",
        "role": "admin|staff|student",
        "email": "string"
    }
}
```

### Logout
```http
POST /logout

Description: End current session
Response (200): 
{
    "message": "Logged out successfully"
}
```

### Get Current User
```http
GET /me

Description: Get logged-in user details
Response (200):
{
    "id": number,
    "username": "string",
    "role": "admin|staff|student",
    "name": "string",
    "email": "string",
    "class": "string"  // Only for students
}
```

## Request Headers
```http
Content-Type: application/json
Authorization: Bearer {token}  // For protected endpoints
```

## Admin Endpoints

### Students Management

```http
GET /students
Description: List all students
Auth Required: Yes (admin)
Response (200): Array of students

POST /students
Description: Create new student
Auth Required: Yes (admin)
Request Body:
{
    "student_number": "string",
    "name": "string",
    "email": "string",
    "class": "string",
    "password": "string"
}

GET /students/{id}
Description: Get specific student
Auth Required: Yes (admin)

PUT /students/{id}
Description: Update student
Auth Required: Yes (admin)
Request Body:
{
    "name": "string",
    "email": "string",
    "class": "string"
}

DELETE /students/{id}
Description: Remove student
Auth Required: Yes (admin)
```

### Staff Management

```http
GET /staff
Description: List all staff
Auth Required: Yes (admin)

POST /staff
Description: Create staff account
Auth Required: Yes (admin)
Request Body:
{
    "username": "string",
    "password": "string",
    "name": "string",
    "email": "string"
}

GET /staff/{id}
Description: Get staff details
Auth Required: Yes (admin)

PUT /staff/{id}
Description: Update staff
Auth Required: Yes (admin)
Request Body:
{
    "name": "string",
    "email": "string"
}

DELETE /staff/{id}
Description: Remove staff
Auth Required: Yes (admin)
```

### Incident Management

```http
GET /incidents
Description: List all incidents
Auth Required: Yes (admin)
Query Params: status (optional)

GET /incidents/{id}
Description: Get incident details
Auth Required: Yes (admin)

PUT /incidents/{id}
Description: Update status
Auth Required: Yes (admin)
Request Body:
{
    "status": "Open|Investigate|Closed"
}

PUT /incidents/{id}/judgments
Description: Update judgments
Auth Required: Yes (admin)
Request Body:
{
    "judgments": [
        {
            "student_id": number,
            "punishment": "No Punishment|Suspension|Expulsion|Community Service",
            "details": "string"
        }
    ]
}
```

### Analytics

```http
GET /incidents/analytics/total
Description: Get total count
Auth Required: Yes (admin)
Response: { "total": number }

GET /incidents/analytics/by-type
Description: Get type distribution
Auth Required: Yes (admin)
Response: Array of { "type": string, "count": number }

GET /incidents/analytics/trend
Description: Get time series data
Auth Required: Yes (admin)
Query Params: period (default: 30)
Response: Array of { "date": string, "count": number }
```

## Staff Endpoints

```http
POST /incidents
Description: Report new incident
Auth Required: Yes (staff)
Content-Type: multipart/form-data
Request Body:
{
    "type": "Academic|Behavioral|Attendance|Other",
    "description": "string",
    "date_of_incidence": "YYYY-MM-DD",
    "students_involved": [number],
    "supporting_documents": [file]  // Optional
}

GET /incidents/mine
Description: List reported incidents
Auth Required: Yes (staff)

GET /incidents/{id}
Description: View incident details
Auth Required: Yes (staff)
Note: Only accessible if staff is reporter
```

## Student Endpoints

```http
GET /my-records
Description: View disciplinary records
Auth Required: Yes (student)
Response: Array of incidents with judgments
```

## Error Responses
```http
400 Bad Request: {"error": "Error message"}
401 Unauthorized: {"error": "Unauthorized"}
403 Forbidden: {"error": "Forbidden"}
404 Not Found: {"error": "Not found"}
405 Method Not Allowed: {"error": "Method not allowed"}
```

## Response Examples

### Login Success
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Teacher",
        "role": "staff",
        "email": "john@school.com"
    }
}
```

### Get Student Details
```json
{
    "id": 1,
    "student_number": "2024001",
    "name": "Alice Smith",
    "email": "alice@student.com",
    "class": "12A"
}
```

### List Incidents
```json
{
    "incidents": [
        {
            "id": 1,
            "type": "Academic",
            "description": "Caught cheating during math exam",
            "date_of_incidence": "2024-03-20",
            "status": "Open",
            "reporter_name": "John Teacher",
            "involved_students": ["Alice Smith", "Bob Johnson"]
        }
    ]
}
```

### Analytics Example
```json
{
    "by_type": [
        {"type": "Academic", "count": 15},
        {"type": "Behavioral", "count": 8},
        {"type": "Attendance", "count": 12}
    ],
    "trend": [
        {"date": "2024-03-01", "count": 3},
        {"date": "2024-03-02", "count": 1}
    ]
}
```

## File Upload
- Supported formats: pdf, doc, docx, jpg, jpeg, png
- Maximum file size: 5MB
- Files are stored in: `/uploads` directory

## Rate Limiting
- 100 requests per minute per IP
- 1000 requests per hour per user

## Versioning
Current version: v1
Endpoint format: `/api/v1/{endpoint}`

## Best Practices
1. Always check response status codes
2. Implement proper error handling
3. Use appropriate HTTP methods
4. Include authentication headers
5. Validate request payloads

## SDK Examples

### PHP
```php
$response = file_get_contents(
    'http://localhost/disciplinary-system/api/incidents',
    false,
    stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ])
);
```

### JavaScript (Fetch)
```javascript
fetch('http://localhost/disciplinary-system/api/incidents', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));
```