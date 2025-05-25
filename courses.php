<?php
require_once 'config.php';

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// include 'header.php';

$errors = [];
$success_message = '';

// Handle course deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $course_id = (int)$_GET['delete'];
    try {
        // Check if course has enrolled students
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $student_count = $stmt->fetchColumn();
        
        if ($student_count > 0) {
            $errors[] = "Cannot delete course. It has $student_count enrolled student(s).";
        } else {
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $success_message = "Course deleted successfully!";
        }
    } catch(PDOException $e) {
        $errors[] = "Error deleting course: " . $e->getMessage();
    }
}

// Handle course addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $course_name = sanitize_input($_POST['course_name'] ?? '');
    
    if (empty($course_name)) {
        $errors[] = "Course name is required";
    } elseif (strlen($course_name) < 3) {
        $errors[] = "Course name must be at least 3 characters long";
    } else {
        // Check if course name already exists
        try {
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_name = ?");
            $stmt->execute([$course_name]);
            if ($stmt->fetch()) {
                $errors[] = "Course name already exists";
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
                $stmt->execute([$course_name]);
                $success_message = "Course added successfully!";
            }
        } catch(PDOException $e) {
            $errors[] = "Error adding course: " . $e->getMessage();
        }
    }
}

// Fetch all courses with student count
try {
    $stmt = $pdo->query("
        SELECT c.id, c.course_name, COUNT(s.id) as student_count 
        FROM courses c 
        LEFT JOIN students s ON c.id = s.course_id 
        GROUP BY c.id, c.course_name 
        ORDER BY c.course_name ASC
    ");
    $courses = $stmt->fetchAll();
} catch(PDOException $e) {
    $errors[] = "Error fetching courses: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="courses.css">
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">All Courses</h3>
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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($courses)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No courses found. Add your first course using the form on the right.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Name</th>
                                        <th>Enrolled Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($course['id']); ?></td>
                                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $course['student_count']; ?> student(s)</span>
                                            </td>
                                            <td>
                                                <?php if ($course['student_count'] == 0): ?>
                                                    <a href="?delete=<?php echo $course['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled 
                                                            title="Cannot delete course with enrolled students">Delete</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Course</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="course_name" 
                                   name="course_name" 
                                   placeholder="Enter course name" 
                                   required>
                            <div class="form-text">Enter a unique course name</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Course Statistics</h5>
                </div>
                <div class="card-body">
                    <?php
                    $total_courses = count($courses);
                    $total_enrolled = array_sum(array_column($courses, 'student_count'));
                    ?>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary"><?php echo $total_courses; ?></h4>
                            <small class="text-muted">Total Courses</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?php echo $total_enrolled; ?></h4>
                            <small class="text-muted">Total Enrollments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- <?php include 'footer.php'; ?> -->
</body>
</html>
