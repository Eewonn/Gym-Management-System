CREATE TABLE members (
    member_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    date_of_birth DATE,
    gender TEXT,
    phone TEXT,
    email TEXT UNIQUE,
    address TEXT,
    join_date DATE DEFAULT (CURDATE()),
    membership_type TEXT,
    status TEXT DEFAULT 'active'
);


CREATE TABLE payments (
    payment_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    member_id INTEGER,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE DEFAULT (CURDATE()),
    payment_type TEXT,
    status TEXT DEFAULT 'PAID',
    FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE trainers (
    trainer_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    specialization TEXT,
    experience_years INTEGER DEFAULT 0,
    certification TEXT,
    hourly_rate DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'active',
    hire_date DATE DEFAULT (CURDATE())
);

CREATE TABLE training_classes (
    class_id INTEGER PRIMARY KEY AUTO_INCREMENT,
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
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
);

CREATE TABLE class_bookings (
    booking_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    class_id INTEGER,
    member_id INTEGER,
    booking_date DATE DEFAULT (CURDATE()),
    class_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'confirmed',
    payment_status VARCHAR(20) DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (class_id) REFERENCES training_classes(class_id),
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    UNIQUE KEY unique_booking (class_id, member_id, class_date)
);

CREATE TABLE personal_training (
    session_id INTEGER PRIMARY KEY AUTO_INCREMENT,
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
    FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE staff (
    staff_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    position VARCHAR(100),
    hire_date DATE DEFAULT (CURDATE()),
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE staff_attendance (
    attendance_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    staff_id INTEGER,
    date DATE DEFAULT (CURDATE()),
    time_in TIME,
    time_out TIME,
    break_start TIME,
    break_end TIME,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'present',
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id),
    UNIQUE KEY unique_staff_date (staff_id, date)
);

