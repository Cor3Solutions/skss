-- Oriental Muayboran Academy Database Setup
-- Run this SQL script to create the necessary database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS muayboran_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE muayboran_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    role ENUM('student', 'coach', 'teacher', 'referee', 'admin') DEFAULT 'student',
    khan_level VARCHAR(50),
    profile_image VARCHAR(255),
    bio TEXT,
    date_of_birth DATE,
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    emergency_contact VARCHAR(255),
    emergency_phone VARCHAR(50),
    membership_status ENUM('active', 'inactive', 'pending', 'expired') DEFAULT 'pending',
    membership_type VARCHAR(50),
    membership_start_date DATE,
    membership_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_membership_status (membership_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    course_type ENUM('student', 'coach', 'teacher', 'referee') NOT NULL,
    level VARCHAR(50),
    duration VARCHAR(100),
    price DECIMAL(10, 2),
    thumbnail VARCHAR(255),
    status ENUM('active', 'draft', 'archived') DEFAULT 'active',
    syllabus TEXT,
    requirements TEXT,
    learning_outcomes TEXT,
    instructor_id INT,
    max_students INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_course_type (course_type),
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course modules table (for course content management)
CREATE TABLE IF NOT EXISTS course_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content_type ENUM('video', 'youtube', 'pdf', 'image', 'text') NOT NULL,
    content_url TEXT,
    order_number INT DEFAULT 0,
    duration VARCHAR(50),
    is_free BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Khan gradings table
CREATE TABLE IF NOT EXISTS khan_gradings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    khan_level INT NOT NULL,
    examiner_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    grading_date TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (examiner_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course enrollments table
CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    progress DECIMAL(5, 2) DEFAULT 0.00,
    status ENUM('enrolled', 'in_progress', 'completed', 'dropped') DEFAULT 'enrolled',
    grade VARCHAR(10),
    certificate_issued BOOLEAN DEFAULT FALSE,
    certificate_number VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course modules table
CREATE TABLE IF NOT EXISTS course_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_number INT NOT NULL,
    content TEXT,
    video_url VARCHAR(500),
    documents TEXT,
    duration VARCHAR(50),
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Khan grading records table
CREATE TABLE IF NOT EXISTS khan_gradings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    khan_level VARCHAR(50) NOT NULL,
    grading_date DATE NOT NULL,
    examiner_id INT,
    location VARCHAR(255),
    certificate_number VARCHAR(100),
    notes TEXT,
    status ENUM('passed', 'failed', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (examiner_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_khan_level (khan_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events and seminars table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_type ENUM('seminar', 'workshop', 'competition', 'grading', 'training') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    location VARCHAR(255),
    max_participants INT,
    current_participants INT DEFAULT 0,
    fee DECIMAL(10, 2),
    instructor_id INT,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_type (event_type),
    INDEX idx_start_date (start_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event registrations table
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    attendance_status ENUM('registered', 'attended', 'absent', 'cancelled') DEFAULT 'registered',
    notes TEXT,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (event_id, user_id),
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT,
    target_audience ENUM('all', 'students', 'coaches', 'teachers', 'referees') DEFAULT 'all',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    is_published BOOLEAN DEFAULT FALSE,
    publish_date TIMESTAMP NULL,
    expiry_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_target_audience (target_audience),
    INDEX idx_publish_date (publish_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Password is: admin123 (all lowercase)
INSERT INTO users (name, email, password, role, membership_status) 
VALUES (
    'Administrator',
    'admin@orientalmuayboran.com',
    '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
    'admin',
    'active'
);

-- Sample course types
INSERT INTO courses (title, slug, description, course_type, level, duration, price, status) VALUES
('Student Foundation Course', 'student-foundation', 'Introduction to Muayboran fundamentals and basic techniques', 'student', 'Beginner', '12 weeks', 299.00, 'active'),
('Coach Certification Program', 'coach-certification', 'Comprehensive training for aspiring Muayboran coaches', 'coach', 'Intermediate', '6 months', 899.00, 'active'),
('Teacher Master Course', 'teacher-master', 'Advanced teacher training and certification program', 'teacher', 'Advanced', '1 year', 1499.00, 'active'),
('Referee Training Course', 'referee-training', 'Official referee certification for Muayboran competitions', 'referee', 'Intermediate', '8 weeks', 599.00, 'active');
