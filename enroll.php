<?php
// require_once 'config.php';
// include 'header.php';

$errors = [];
$success_message = '';

// Fetch students and courses for dropdowns
// try {
//     $stmt = $pdo->query("SELECT id, name, email FROM students ORDER BY name ASC");
//     $students = $stmt->fetchAll();
    
//     $stmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name ASC");
//     $courses = $stmt->fetchAll();
// } catch(PDOException $e) {
//     $errors[] = "Error fetching data: " . $e->getMessage();
// }

// Process enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : null;
    $course_id = !empty($_POST['course_id']) ? (int)$_POST['course_id'] : null;
    
    // Validation
    if (empty($student_id)) {
        $errors[] = "Please select a student";
    }
    
    if (empty($course_id)) {
        $errors[] = "Please select a course";
    }
    
    // If no validation errors, proceed with enrollment
    if (empty($errors)) {
        try {
            // Check if student exists
            $stmt = $pdo->prepare("SELECT name FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            $student_data = $stmt->fetch();
            
            if (!$student_data) {
                $errors[] = "Selected student not found";
            }
            
            // Check if course exists
            $stmt = $pdo->prepare("SELECT course_name FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course_data = $stmt->fetch();
            
            if (!$course_data) {
                $errors[] = "Selected course not found";
            }
            
            // If both exist, update enrollment
            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE students SET course_id = ? WHERE id = ?");
                $stmt->execute([$course_id, $student_id]);
                
                $success_message = "Successfully enrolled " . htmlspecialchars($student_data['name']) . 
                                 " in " . htmlspecialchars($course_data['course_name']) . "!";
            }
            
        } catch(PDOException $e) {
            $errors[] = "Error processing enrollment: " . $e->getMessage();
        }
    }
}

// Fetch current enrollments for display
// try {
//     $stmt = $pdo->query("
//         SELECT s.id, s.name, s.email, c.course_name, c.id as course_id
//         FROM students s 
//         LEFT JOIN courses c ON s.course_id = c.id 
//         ORDER BY s.name ASC
//     ");
//     $enrollments = $stmt->fetchAll();
// } catch(PDOException $e) {
//     $errors[] = "Error fetching enrollments: " . $e->getMessage();
// }
// ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="enroll.css">
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Enroll Student in Course</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($students)): ?>
                            <div class="alert alert-warning">
                                <strong>No students found!</strong> 
                                <a href="add_student.php">Add a student</a> first.
                            </div>
                        <?php elseif (empty($courses)): ?>
                            <div class="alert alert-warning">
                                <strong>No courses found!</strong> 
                                <a href="courses.php">Add a course</a> first.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                                    <select class="form-select" id="student_id" name="student_id" required>
                                        <option value="">Choose a student...</option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?php echo $student['id']; ?>" 
                                                    <?php echo (isset($student_id) && $student_id == $student['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($student['name']) . ' (' . htmlspecialchars($student['email']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Select Course <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">Choose a course...</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>" 
                                                    <?php echo (isset($course_id) && $course_id == $course['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course['course_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Enroll Student</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>