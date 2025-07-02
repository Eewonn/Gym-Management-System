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


