INSERT INTO users (username, password) VALUES
('admin', 'test123'),
('john', 'pass456'),
('jane', 'secret789'),
('zoe', 'abc123'),
('mark', 'qwerty');

INSERT INTO members (
  first_name, last_name, date_of_birth, gender, phone, email, address,
  join_date, membership_type, status
) VALUES
('Juan', 'Dela Cruz', '1995-06-15', 'Male', '+639171234567', 'juan.delacruz@gmail.com', 'Brgy. San Isidro, Makati City, Metro Manila', CURDATE(), 'Premium', 'active'),
('Maria', 'Santos', '2000-09-23', 'Female', '+639178765432', 'maria.santos@yahoo.com', 'Purok 3, Barangay Lahug, Cebu City, Cebu', CURDATE(), 'Standard', 'active'),
('Jose', 'Ramos', '1988-12-10', 'Male', '+639193456789', 'jose.ramos@outlook.com', 'Barangay San Miguel, Iloilo City', '2023-10-01', 'Basic', 'inactive'),
('Ana', 'Reyes', '1999-03-05', 'Female', '+639123456789', 'ana.reyes@gmail.com', 'Barangay Poblacion, Davao City, Davao del Sur', CURDATE(), 'Premium', 'active'),
('Carlos', 'Garcia', '1992-08-20', 'Male', '+639954321678', 'carlos.garcia@live.com', 'Barangay Balibago, Angeles City, Pampanga', '2024-06-01', 'Standard', 'active'),
('Liza', 'Manalo', '2003-01-14', 'Female', '+639750987654', 'liza.manalo@icloud.com', 'Sitio Baybay, Barangay Sabang, Puerto Princesa City', '2024-01-15', 'Basic', 'active'),
('Mark', 'Torres', '1997-04-30', 'Male', '+639985001234', 'mark.torres@ymail.com', 'Barangay Lagao, General Santos City', '2023-11-11', 'Premium', 'inactive'),
('Grace', 'Navarro', '1985-11-02', 'Female', '+639274567890', 'grace.navarro@gmail.com', 'Barangay Mabolo, Tacloban City, Leyte', CURDATE(), 'Standard', 'active');

INSERT INTO payments (member_id, amount, payment_date, payment_type, status) VALUES
(1, 1500.00, '2024-01-05', 'Cash', 'PAID'),
(2, 1200.00, '2024-02-10', 'Card', 'PAID'),
(4, 1800.00, '2024-03-15', 'GCash', 'PAID'),
(5, 1300.00, '2024-03-20', 'Cash', 'PAID'),
(6, 1000.00, '2024-04-01', 'Card', 'PAID'),
(1, 1500.00, '2024-04-15', 'Cash', 'PAID'),
(2, 1200.00, '2024-05-05', 'GCash', 'PAID'),
(4, 1800.00, '2024-06-10', 'Card', 'PAID'),
(5, 1300.00, '2024-06-15', 'Cash', 'PAID'),
(8, 1200.00, '2024-07-01', 'GCash', 'PAID');
