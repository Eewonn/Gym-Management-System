INSERT INTO users (username, password) VALUES
('admin', 'test123'),
('john', 'pass456'),
('jane', 'secret789'),
('zoe', 'abc123'),
('mark', 'qwerty');

INSERT INTO members (user_id, first_name, last_name, phone, email, membership_type) VALUES
(1, 'Alice', 'Walker', '09171234567', 'alice@example.com', 'Premium'),
(1, 'Brian', 'Lee', '09181112222', 'brian@example.com', 'Basic'),
(2, 'Carla', 'Reyes', '09192223333', 'carla@example.com', 'Standard'),
(3, 'Daniel', 'Santos', '09201114444', 'daniel@example.com', 'Premium'),
(4, 'Erika', 'Lopez', '09221115555', 'erika@example.com', 'Basic'),
(5, 'Frank', 'Tan', '09231116666', 'frank@example.com', 'Standard');

INSERT INTO payments (user_id, member_id, amount, payment_type) VALUES
(1, 1, 100.00, 'Credit Card'),
(1, 2, 50.00, 'Cash'),
(2, 3, 75.00, 'Debit Card'),
(3, 4, 100.00, 'Credit Card'),
(4, 5, 60.00, 'Cash');

