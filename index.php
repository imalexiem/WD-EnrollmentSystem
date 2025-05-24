<?php
    $title = "Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
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

    <div class="slideshow-container">
        <div class="slide fade">
            <img src="assets/bg1.jpg" alt="Background 1">
        </div>
        <div class="slide fade">
            <img src="assets/bg2.png" alt="Background 2">
        </div>
        <div class="slide fade">
            <img src="assets/bg3.jpg" alt="Background 3">
        </div>
        
        <!-- Add Navigation Arrows -->
        <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="next" onclick="changeSlide(1)">&#10095;</button>

        <!-- Page Indicators -->
        <div class="dot-container">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div> <!-- end of slideshow-container -->
    
    <div class="banner">
        <div class="banner-content">
            <h1>International State College of the Philippines</h1>
            <p>Empowering Minds, Building Futures</p>
        </div>
         <div class="enroll-button-container">
        <a href="add_student.php" class="enroll-button">Apply Now</a>
        </div>
    </div>

    <div class="banner-image">
        <img src="assets/iscpbanner.jpg" alt="ISCP Banner">
    </div>

    <div class="courses-section">
        <h2>Our Courses</h2>
        <div class="courses-slideshow">
            <div class="course-slide fade">
                <img src="assets/agriculture.jpg" alt="Agriculture">
            </div>
            <div class="course-slide fade">
                <img src="assets/bartending.jpg" alt="Bartending">
            </div>
            <div class="course-slide fade">
                <img src="assets/compeng.jpg" alt="Computer Engineering">
            </div>
            <div class="course-slide fade">
                <img src="assets/demonslaying.jpg" alt="Demon Slaying">
            </div>
            <div class="course-slide fade">
                <img src="assets/sorcery.jpg" alt="Sorcery">
            </div>

            <!-- Course Navigation Arrows -->
            <button class="course-prev" onclick="changeCourseSlide(-1)">&#10094;</button>
            <button class="course-next" onclick="changeCourseSlide(1)">&#10095;</button>

            <!-- Course Indicators -->
            <div class="course-dot-container">
                <span class="course-dot" onclick="currentCourseSlide(1)"></span>
                <span class="course-dot" onclick="currentCourseSlide(2)"></span>
                <span class="course-dot" onclick="currentCourseSlide(3)"></span>
                <span class="course-dot" onclick="currentCourseSlide(4)"></span>
                <span class="course-dot" onclick="currentCourseSlide(5)"></span>
            </div>
        </div> <!-- end of courses-slideshow -->
        <a href="courses.php" class="see-more-btn">See More</a>
    </div> <!-- end of courses-section -->

    <script>
        let slideIndex = 1;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        
        function showSlide(n) {
            if (n > slides.length) {
                slideIndex = 1;
            }
            if (n < 1) {
                slideIndex = slides.length;
            }
            
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Remove active state from all dots
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Show current slide and activate current dot
            slides[slideIndex - 1].classList.add('active');
            dots[slideIndex - 1].classList.add('active');
        }
        
        function changeSlide(n) {
            showSlide(slideIndex += n);
        }
        
        function currentSlide(n) {
            showSlide(slideIndex = n);
        }
        
        // Show initial slide
        showSlide(slideIndex);
        
        // Auto advance slides
        setInterval(() => {
            changeSlide(1);
        }, 8000);  // Changed from 5000 to 8000 milliseconds
        
        // Add mouse enter/leave events for slideshow and navbar
        const nav = document.querySelector('nav');
        const slideshowContainer = document.querySelector('.slideshow-container');
        let isInSlideshow = false;

        slideshowContainer.addEventListener('mouseenter', () => {
            isInSlideshow = true;
            nav.style.backgroundColor = 'rgba(1, 37, 96, 0)'; // Fully transparent
            nav.style.transition = 'background-color 0.3s ease';
        });

        slideshowContainer.addEventListener('mouseleave', () => {
            isInSlideshow = false;
            nav.style.backgroundColor = '#012560'; // Fully opaque
            nav.style.transition = 'background-color 0.3s ease';
        });

        nav.addEventListener('mouseenter', () => {
            if (isInSlideshow) {
                nav.style.backgroundColor = 'rgba(1, 37, 96, 0)'; // Keep transparent
            }
        });

        nav.addEventListener('mouseleave', () => {
            if (!isInSlideshow) {
                nav.style.backgroundColor = '#012560'; // Only become opaque if not in slideshow
            }
        });

        // Courses slideshow
        let courseSlideIndex = 1;
        const courseSlides = document.querySelectorAll('.course-slide');
        const courseDots = document.querySelectorAll('.course-dot');

        function showCourseSlide(n) {
            if (n > courseSlides.length) {
                courseSlideIndex = 1;
            }
            if (n < 1) {
                courseSlideIndex = courseSlides.length;
            }
            
            courseSlides.forEach(slide => {
                slide.classList.remove('active', 'prev', 'next');
            });
            
            courseDots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Set active slide
            courseSlides[courseSlideIndex - 1].classList.add('active');
            
            // Set prev slide
            let prevIndex = courseSlideIndex - 2;
            if (prevIndex < 0) prevIndex = courseSlides.length - 1;
            courseSlides[prevIndex].classList.add('prev');
            
            // Set next slide
            let nextIndex = courseSlideIndex % courseSlides.length;
            courseSlides[nextIndex].classList.add('next');
            
            courseDots[courseSlideIndex - 1].classList.add('active');
        }

        function changeCourseSlide(n) {
            showCourseSlide(courseSlideIndex += n);
        }

        function currentCourseSlide(n) {
            showCourseSlide(courseSlideIndex = n);
        }

        // Show initial course slide
        showCourseSlide(courseSlideIndex);

        // Auto advance course slides
        setInterval(() => {
            changeCourseSlide(1);
        }, 8000);
    </script>
</body>
</html>