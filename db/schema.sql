CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE members (
    member_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    phone TEXT,
    email TEXT UNIQUE,
    join_date DATE DEFAULT (CURDATE()),
    membership_type TEXT,
    status TEXT DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE payments (
    payment_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    member_id INTEGER,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE DEFAULT (CURDATE()),
    payment_type TEXT,
    status TEXT DEFAULT 'PAID',
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE trainers (
    trainer_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    specialization TEXT,
    experience_years INTEGER DEFAULT 0,
    certification TEXT,
    hourly_rate DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'active',
    hire_date DATE DEFAULT (CURDATE()),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE training_classes (
    class_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    class_name VARCHAR(100) NOT NULL,
    description TEXT,
    trainer_id INTEGER,
    day_of_week VARCHAR(20) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_capacity INTEGER DEFAULT 20,
    class_type VARCHAR(50),
    equipment_needed TEXT,
    difficulty_level VARCHAR(20) DEFAULT 'beginner',
    price DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE class_bookings (
    booking_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    class_id INTEGER,
    member_id INTEGER,
    booking_date DATE DEFAULT (CURDATE()),
    class_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'confirmed',
    payment_status VARCHAR(20) DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (class_id) REFERENCES training_classes(class_id),
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_booking (class_id, member_id, class_date)
);

CREATE TABLE personal_training (
    session_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    trainer_id INTEGER,
    member_id INTEGER,
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    session_type VARCHAR(50) DEFAULT 'personal',
    notes TEXT,
    status VARCHAR(20) DEFAULT 'scheduled',
    payment_status VARCHAR(20) DEFAULT 'pending',
    session_price DECIMAL(10,2),
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id),
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE staff (
    staff_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    position VARCHAR(100),
    hire_date DATE DEFAULT (CURDATE()),
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE staff_attendance (
    attendance_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    staff_id INTEGER,
    date DATE DEFAULT CURDATE(),
    work_hours INTEGER DEFAULT 0,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'present',
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_staff_date (staff_id, date)
);
