<?php
// require_once 'config.php';
// include 'header.php';

$errors = [];
$success_message = '';

// Fetch courses for dropdown
// try {
//     $stmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name ASC");
//     $courses = $stmt->fetchAll();
// } catch(PDOException $e) {
//     $errors[] = "Error fetching courses: " . $e->getMessage();
// }

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $course_id = !empty($_POST['course_id']) ? (int)$_POST['course_id'] : null;
    
    // Validation
    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!validate_email($email)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email already exists
    if (empty($errors) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email already exists";
            }
        } catch(PDOException $e) {
            $errors[] = "Error checking email: " . $e->getMessage();
        }
    }
    
    // If no errors, insert student
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (name, email, course_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $course_id]);
            $success_message = "Student added successfully!";
            // Clear form data
            $name = $email = $course_id = '';
        } catch(PDOException $e) {
            $errors[] = "Error adding student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="add_student.css">
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

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Admission</h3>
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

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?php echo isset($errors) && in_array('Name is required', $errors) || in_array('Name must be at least 2 characters long', $errors) ? 'is-invalid' : ''; ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                   required>
                            <div class="form-text">Enter the student's full name</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control <?php echo isset($errors) && (in_array('Email is required', $errors) || in_array('Invalid email format', $errors) || in_array('Email already exists', $errors)) ? 'is-invalid' : ''; ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                   required>
                            <div class="form-text">Enter a valid email address</div>
                        </div>

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course (Optional)</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">Select a course (optional)</option>
                                <?php if (!empty($courses)): ?>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" 
                                                <?php echo (isset($course_id) && $course_id == $course['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['course_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">You can enroll the student in a course later if needed</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="students.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- <?php include 'footer.php'; ?> -->
</body>
</html>