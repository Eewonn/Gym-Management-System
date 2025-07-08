<?php
//Include database connection file
require_once __DIR__ . '/../../db/db.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_trainer'])) {
        //Add new trainer
        try {
            $stmt = $pdo->prepare("INSERT INTO trainers (first_name, last_name, email, phone, specialization, experience_years, certification, hourly_rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['specialization'],
                $_POST['experience_years'],
                $_POST['certification'],
                $_POST['hourly_rate']
            ]);
            $success_message = "Trainer added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding trainer: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['add_class'])) {
        //Add new training class
        try {
            $stmt = $pdo->prepare("INSERT INTO training_classes (class_name, description, trainer_id, day_of_week, start_time, end_time, max_capacity, class_type, equipment_needed, difficulty_level, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['class_name'],
                $_POST['description'],
                $_POST['trainer_id'],
                $_POST['day_of_week'],
                $_POST['start_time'],
                $_POST['end_time'],
                $_POST['max_capacity'],
                $_POST['class_type'],
                $_POST['equipment_needed'],
                $_POST['difficulty_level'],
                $_POST['price']
            ]);
            $success_message = "Training class added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding class: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['book_class'])) {
        //Book a class for member
        try {
            $stmt = $pdo->prepare("INSERT INTO class_bookings (class_id, member_id, class_date, notes) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['class_id'],
                $_POST['member_id'],
                $_POST['class_date'],
                $_POST['notes']
            ]);
            $success_message = "Class booked successfully!";
        } catch(PDOException $e) {
            $error_message = "Error booking class: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['schedule_personal'])) {
        //Schedule personal training session
        try {
            $stmt = $pdo->prepare("INSERT INTO personal_training (trainer_id, member_id, session_date, start_time, end_time, session_type, notes, session_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['trainer_id'],
                $_POST['member_id'],
                $_POST['session_date'],
                $_POST['start_time'],
                $_POST['end_time'],
                $_POST['session_type'],
                $_POST['notes'],
                $_POST['session_price']
            ]);
            $success_message = "Personal training session scheduled successfully!";
        } catch(PDOException $e) {
            $error_message = "Error scheduling session: " . $e->getMessage();
        }
    }
}

//Get all trainers
$trainers = [];
try {
    $stmt = $pdo->query("SELECT * FROM trainers WHERE status = 'active' ORDER BY first_name, last_name");
    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching trainers: " . $e->getMessage();
}

//Get all members for booking
$members = [];
try {
    $stmt = $pdo->query("SELECT member_id, first_name, last_name, email FROM members WHERE status = 'active' ORDER BY first_name, last_name");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching members: " . $e->getMessage();
}

//Get all training classes with trainer info
$training_classes = [];
try {
    $stmt = $pdo->query("SELECT tc.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name 
                        FROM training_classes tc 
                        LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id 
                        WHERE tc.status = 'active' 
                        ORDER BY tc.day_of_week, tc.start_time");
    $training_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching classes: " . $e->getMessage();
}

//Get today's schedule
$today = date('Y-m-d');
$today_day = date('l', strtotime($today));
$todays_classes = [];
try {
    $stmt = $pdo->prepare("SELECT tc.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name,
                          (SELECT COUNT(*) FROM class_bookings cb WHERE cb.class_id = tc.class_id AND cb.class_date = ? AND cb.status = 'confirmed') as current_bookings
                          FROM training_classes tc 
                          LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id 
                          WHERE tc.day_of_week = ? AND tc.status = 'active' 
                          ORDER BY tc.start_time");
    $stmt->execute([$today, $today_day]);
    $todays_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching today's classes: " . $e->getMessage();
}

//Get personal training sessions for today
$todays_personal_sessions = [];
try {
    $stmt = $pdo->prepare("SELECT pt.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name,
                          m.first_name as member_first_name, m.last_name as member_last_name
                          FROM personal_training pt
                          JOIN trainers t ON pt.trainer_id = t.trainer_id
                          JOIN members m ON pt.member_id = m.member_id
                          WHERE pt.session_date = ? AND pt.status = 'scheduled'
                          ORDER BY pt.start_time");
    $stmt->execute([$today]);
    $todays_personal_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching personal sessions: " . $e->getMessage();
}

//Get upcoming bookings
$upcoming_bookings = [];
try {
    $stmt = $pdo->prepare("SELECT cb.*, tc.class_name, tc.start_time, tc.end_time, tc.day_of_week,
                          m.first_name as member_first_name, m.last_name as member_last_name,
                          t.first_name as trainer_first_name, t.last_name as trainer_last_name
                          FROM class_bookings cb
                          JOIN training_classes tc ON cb.class_id = tc.class_id
                          JOIN members m ON cb.member_id = m.member_id
                          LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id
                          WHERE cb.class_date >= ? AND cb.status = 'confirmed'
                          ORDER BY cb.class_date, tc.start_time
                          LIMIT 20");
    $stmt->execute([$today]);
    $upcoming_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching bookings: " . $e->getMessage();
}

$days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Schedule</title>
</head>
<body>
    <h1>Training Schedule Management</h1>
    
    <?php if (isset($success_message)): ?>
        <div>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div>
        <button onclick="showSection('schedule')" style="border: 1px solid #000; padding: 8px 12px; margin: 5px;">Today's Schedule</button>
        <button onclick="showSection('classes')" style="border: 1px solid #000; padding: 8px 12px; margin: 5px;">Manage Classes</button>
        <button onclick="showSection('trainers')" style="border: 1px solid #000; padding: 8px 12px; margin: 5px;">Manage Trainers</button>
        <button onclick="showSection('bookings')" style="border: 1px solid #000; padding: 8px 12px; margin: 5px;">Book Classes</button>
        <button onclick="showSection('personal')" style="border: 1px solid #000; padding: 8px 12px; margin: 5px;">Personal Training</button>
    </div>

    <!-- Today's Schedule Section -->
    <div id="schedule">
        <h2>Today's Schedule (<?= date('F j, Y') ?> - <?= $today_day ?>)</h2>
        
        <h3>Group Classes</h3>
        <?php if (!empty($todays_classes)): ?>
            <table border="1">
                <tr>
                    <th>Class Name</th>
                    <th>Trainer</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Bookings</th>
                    <th>Difficulty</th>
                </tr>
                <?php foreach ($todays_classes as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['class_name']) ?></td>
                    <td><?= htmlspecialchars($class['trainer_first_name'] . ' ' . $class['trainer_last_name']) ?></td>
                    <td><?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?></td>
                    <td><?= htmlspecialchars($class['class_type']) ?></td>
                    <td><?= $class['current_bookings'] ?>/<?= $class['max_capacity'] ?></td>
                    <td><?= ucfirst($class['difficulty_level']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No classes scheduled for today.</p>
        <?php endif; ?>

        <h3>Personal Training Sessions</h3>
        <?php if (!empty($todays_personal_sessions)): ?>
            <table border="1">
                <tr>
                    <th>Session Type</th>
                    <th>Trainer</th>
                    <th>Member</th>
                    <th>Time</th>
                    <th>Price</th>
                </tr>
                <?php foreach ($todays_personal_sessions as $session): ?>
                <tr>
                    <td><?= htmlspecialchars($session['session_type']) ?></td>
                    <td><?= htmlspecialchars($session['trainer_first_name'] . ' ' . $session['trainer_last_name']) ?></td>
                    <td><?= htmlspecialchars($session['member_first_name'] . ' ' . $session['member_last_name']) ?></td>
                    <td><?= date('g:i A', strtotime($session['start_time'])) ?> - <?= date('g:i A', strtotime($session['end_time'])) ?></td>
                    <td>$<?= number_format($session['session_price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No personal training sessions today.</p>
        <?php endif; ?>
    </div>

    <!-- Manage Classes Section -->
    <div id="classes" style="display: none;">
        <h2>Manage Training Classes</h2>
        
        <h3>Add New Class</h3>
        <form method="POST">
            <table>
                <tr>
                    <td>Class Name:</td>
                    <td><input type="text" name="class_name" placeholder="Enter class name" required></td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea name="description" rows="3" placeholder="Class description..."></textarea></td>
                </tr>
                <tr>
                    <td>Trainer:</td>
                    <td>
                        <select name="trainer_id" required>
                            <option value="">Select Trainer</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['trainer_id'] ?>">
                                    <?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Day of Week:</td>
                    <td>
                        <select name="day_of_week" required>
                            <option value="">Select Day</option>
                            <?php foreach ($days_of_week as $day): ?>
                                <option value="<?= $day ?>"><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Start Time:</td>
                    <td><input type="time" name="start_time" required></td>
                </tr>
                <tr>
                    <td>End Time:</td>
                    <td><input type="time" name="end_time" required></td>
                </tr>
                <tr>
                    <td>Max Capacity:</td>
                    <td><input type="number" name="max_capacity" value="20" min="1" max="50" required></td>
                </tr>
                <tr>
                    <td>Class Type:</td>
                    <td>
                        <select name="class_type" required>
                            <option value="">Select Type</option>
                            <option value="Cardio">Cardio</option>
                            <option value="Strength">Strength</option>
                            <option value="Yoga">Yoga</option>
                            <option value="Pilates">Pilates</option>
                            <option value="HIIT">HIIT</option>
                            <option value="CrossFit">CrossFit</option>
                            <option value="Zumba">Zumba</option>
                            <option value="Spinning">Spinning</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Difficulty:</td>
                    <td>
                        <select name="difficulty_level" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Price ($):</td>
                    <td><input type="number" name="price" step="0.01" min="0" value="15.00"></td>
                </tr>
                <tr>
                    <td>Equipment Needed:</td>
                    <td><input type="text" name="equipment_needed" placeholder="e.g. Yoga mats, dumbbells"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="add_class" value="Add Class" style="border: 1px solid #000; padding: 5px 10px;"></td>
                </tr>
            </table>
        </form>

        <h3>Current Classes</h3>
        <?php if (!empty($training_classes)): ?>
            <table border="1">
                <tr>
                    <th>Class Name</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Trainer</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Difficulty</th>
                </tr>
                <?php foreach ($training_classes as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['class_name']) ?></td>
                    <td><?= $class['day_of_week'] ?></td>
                    <td><?= date('g:i A', strtotime($class['start_time'])) ?>-<?= date('g:i A', strtotime($class['end_time'])) ?></td>
                    <td><?= htmlspecialchars($class['trainer_first_name'] . ' ' . $class['trainer_last_name']) ?></td>
                    <td><?= htmlspecialchars($class['class_type']) ?></td>
                    <td><?= $class['max_capacity'] ?></td>
                    <td>$<?= number_format($class['price'], 2) ?></td>
                    <td><?= ucfirst($class['difficulty_level']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No classes available.</p>
        <?php endif; ?>
    </div>

    <!-- Manage Trainers Section -->
    <div id="trainers" style="display: none;">
        <h2>Manage Trainers</h2>
        
        <h3>Add New Trainer</h3>
        <form method="POST">
            <table>
                <tr>
                    <td>First Name:</td>
                    <td><input type="text" name="first_name" placeholder="First name" required></td>
                </tr>
                <tr>
                    <td>Last Name:</td>
                    <td><input type="text" name="last_name" placeholder="Last name" required></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="email" name="email" placeholder="email@example.com" required></td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td><input type="tel" name="phone" placeholder="Phone number"></td>
                </tr>
                <tr>
                    <td>Specialization:</td>
                    <td><textarea name="specialization" rows="2" cols="30" placeholder="e.g. Strength Training, Yoga"></textarea></td>
                </tr>
                <tr>
                    <td>Experience (Years):</td>
                    <td><input type="number" name="experience_years" min="0" value="0"></td>
                </tr>
                <tr>
                    <td>Certification:</td>
                    <td><input type="text" name="certification" placeholder="e.g. NASM, ACE, ACSM"></td>
                </tr>
                <tr>
                    <td>Hourly Rate ($):</td>
                    <td><input type="number" name="hourly_rate" step="0.01" min="0" value="50.00" placeholder="50.00"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="add_trainer" value="Add Trainer" style="border: 1px solid #000; padding: 5px 10px;"></td>
                </tr>
            </table>
        </form>

        <h3>Current Trainers</h3>
        <?php if (!empty($trainers)): ?>
            <table border="1">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Experience</th>
                    <th>Rate/Hour</th>
                    <th>Specialization</th>
                </tr>
                <?php foreach ($trainers as $trainer): ?>
                <tr>
                    <td><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></td>
                    <td><?= htmlspecialchars($trainer['email']) ?></td>
                    <td><?= $trainer['experience_years'] ?> years</td>
                    <td>$<?= number_format($trainer['hourly_rate'], 2) ?></td>
                    <td><?= htmlspecialchars($trainer['specialization']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No trainers available.</p>
        <?php endif; ?>
    </div>

    <!-- Book Classes Section -->
    <div id="bookings" style="display: none;">
        <h2>Book Classes</h2>
        
        <h3>Book Class for Member</h3>
        <form method="POST">
            <table>
                <tr>
                    <td>Member:</td>
                    <td>
                        <select name="member_id" required>
                            <option value="">Select Member</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= $member['member_id'] ?>">
                                    <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name'] . ' (' . $member['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Class:</td>
                    <td>
                        <select name="class_id" required>
                            <option value="">Select Class</option>
                            <?php foreach ($training_classes as $class): ?>
                                <option value="<?= $class['class_id'] ?>">
                                    <?= htmlspecialchars($class['class_name']) ?> - <?= $class['day_of_week'] ?> <?= date('g:i A', strtotime($class['start_time'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Class Date:</td>
                    <td><input type="date" name="class_date" min="<?= $today ?>" required></td>
                </tr>
                <tr>
                    <td>Notes:</td>
                    <td><textarea name="notes" rows="3" cols="30" placeholder="Optional notes..."></textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="book_class" value="Book Class" style="border: 1px solid #000; padding: 5px 10px;"></td>
                </tr>
            </table>
        </form>

        <h3>Upcoming Bookings</h3>
        <?php if (!empty($upcoming_bookings)): ?>
            <table border="1">
                <tr>
                    <th>Class</th>
                    <th>Member</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Trainer</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($upcoming_bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['class_name']) ?></td>
                    <td><?= htmlspecialchars($booking['member_first_name'] . ' ' . $booking['member_last_name']) ?></td>
                    <td><?= date('M j, Y', strtotime($booking['class_date'])) ?></td>
                    <td><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></td>
                    <td><?= htmlspecialchars($booking['trainer_first_name'] . ' ' . $booking['trainer_last_name']) ?></td>
                    <td><?= ucfirst($booking['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No upcoming bookings.</p>
        <?php endif; ?>
    </div>

    <!-- Personal Training Section -->
    <div id="personal" style="display: none;">
        <h2>Personal Training</h2>
        
        <h3>Schedule Personal Training Session</h3>
        <form method="POST">
            <table>
                <tr>
                    <td>Trainer:</td>
                    <td>
                        <select name="trainer_id" required>
                            <option value="">Select Trainer</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['trainer_id'] ?>">
                                    <?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?> - $<?= number_format($trainer['hourly_rate'], 2) ?>/hr
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Member:</td>
                    <td>
                        <select name="member_id" required>
                            <option value="">Select Member</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= $member['member_id'] ?>">
                                    <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Session Date:</td>
                    <td><input type="date" name="session_date" min="<?= $today ?>" required></td>
                </tr>
                <tr>
                    <td>Start Time:</td>
                    <td><input type="time" name="start_time" required></td>
                </tr>
                <tr>
                    <td>End Time:</td>
                    <td><input type="time" name="end_time" required></td>
                </tr>
                <tr>
                    <td>Session Type:</td>
                    <td>
                        <select name="session_type" required>
                            <option value="personal">Personal Training</option>
                            <option value="nutritional">Nutritional Consultation</option>
                            <option value="assessment">Fitness Assessment</option>
                            <option value="rehabilitation">Rehabilitation</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Session Price ($):</td>
                    <td><input type="number" name="session_price" step="0.01" min="0" value="75.00" placeholder="75.00" required></td>
                </tr>
                <tr>
                    <td>Notes:</td>
                    <td><textarea name="notes" rows="4" cols="30" placeholder="Session goals, special requirements..."></textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="schedule_personal" value="Schedule Session" style="border: 1px solid #000; padding: 5px 10px;"></td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        function showSection(sectionName) {
            // Hide all sections
            const sections = ['schedule', 'classes', 'trainers', 'bookings', 'personal'];
            sections.forEach(section => {
                document.getElementById(section).style.display = 'none';
            });
            
            // Show selected section
            document.getElementById(sectionName).style.display = 'block';
        }
        
        // Show schedule section by default
        document.addEventListener('DOMContentLoaded', function() {
            showSection('schedule');
        });
    </script>
</body>
</html>

