-- Get all active members
SELECT * FROM members WHERE status = 'active' ORDER BY last_name, first_name;

-- Get member by ID
SELECT * FROM members WHERE member_id = ?;

-- Search members by name
SELECT * FROM members 
WHERE first_name LIKE ? OR last_name LIKE ? 
ORDER BY last_name, first_name;

-- Get members by membership type
SELECT * FROM members WHERE membership_type = ? AND status = 'active';

-- Get all members (for admin view)
SELECT member_id, first_name, last_name, email, phone, membership_type, join_date, status 
FROM members 
ORDER BY join_date DESC;

-- Add new member
INSERT INTO members (first_name, last_name, date_of_birth, gender, phone, email, address, membership_type) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?);

-- Update member information
UPDATE members 
SET first_name = ?, last_name = ?, phone = ?, email = ?, address = ?, membership_type = ? 
WHERE member_id = ?;

-- Deactivate member (instead of deleting)
UPDATE members SET status = 'inactive' WHERE member_id = ?;

------------------------------------------------------------------------------------------------------