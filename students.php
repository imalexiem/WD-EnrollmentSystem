<?php
// Temporarily comment out database operations until connection is fixed
/*
require_once 'config.php';

$errors = [];
$success_message = '';

try {
    $stmt = $pdo->query("SELECT s.id, s.name, s.email, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.name ASC");
    $students = $stmt->fetchAll();
} catch(PDOException $e) {
    $errors[] = "Error fetching students: " . $e->getMessage();
}
*/

// Temporary empty array for testing layout
$students = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - ISCP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="students.css">
</head>
<body>
    <nav>
        <div class="nav-brand">
            <img src="assets/logo.png" alt="ISCP Logo" class="nav-logo">
            <a href="index.php" class="brand-text">ISCP</a>
        </div>
        <ul>
            <li><a href="add_student.php">Admissions</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="enroll.php">Enrollment</a></li>
            <li><a href="students.php">Students</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2>All Students</h2>
                        <a href="add_student.php" class="btn btn-primary mb-3">Add New Student</a>
                        <p>No students found. Add your first student using the button above.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>