<?php
require_once __DIR__ . '/../../db/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Unauthorized access.");
}

$userId = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_POST) {
    try {
        if (isset($_POST['add_trainer'])) {
            $stmt = $pdo->prepare("
                INSERT INTO trainers (user_id, first_name, last_name, email, phone, specialization, experience_years, certification, hourly_rate)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
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
        }

        if (isset($_POST['add_class'])) {
            $stmt = $pdo->prepare("
                INSERT INTO training_classes (user_id, class_name, description, trainer_id, day_of_week, start_time, end_time, max_capacity, class_type, equipment_needed, difficulty_level, price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
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
        }

        if (isset($_POST['book_class'])) {
            $stmt = $pdo->prepare("
                INSERT INTO class_bookings (class_id, member_id, class_date, notes)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['class_id'],
                $_POST['member_id'],
                $_POST['class_date'],
                $_POST['notes']
            ]);
            $success_message = "Class booked successfully!";
        }

        if (isset($_POST['schedule_personal'])) {
            $stmt = $pdo->prepare("
                INSERT INTO personal_training (trainer_id, member_id, session_date, start_time, end_time, session_type, notes, session_price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
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
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Today's date
$today = date('Y-m-d');
$today_day = date('l', strtotime($today));

// Get trainers
$trainers = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE user_id = ? AND status = 'active' ORDER BY first_name, last_name");
    $stmt->execute([$userId]);
    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching trainers: " . $e->getMessage();
}

// Get active members
$members = [];
try {
    $stmt = $pdo->prepare("SELECT member_id, first_name, last_name, email FROM members WHERE user_id = ? AND status = 'active' ORDER BY first_name, last_name");
    $stmt->execute([$userId]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching members: " . $e->getMessage();
}

// Get training classes
$training_classes = [];
try {
    $stmt = $pdo->prepare("
        SELECT tc.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name 
        FROM training_classes tc 
        LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id 
        WHERE tc.user_id = ? AND tc.status = 'active'
        ORDER BY tc.day_of_week, tc.start_time
    ");
    $stmt->execute([$userId]);
    $training_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching classes: " . $e->getMessage();
}

// Get today's scheduled classes
$todays_classes = [];
try {
    $stmt = $pdo->prepare("
        SELECT tc.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name,
        (SELECT COUNT(*) FROM class_bookings cb WHERE cb.class_id = tc.class_id AND cb.class_date = ? AND cb.status = 'confirmed') as current_bookings
        FROM training_classes tc 
        LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id 
        WHERE tc.user_id = ? AND tc.day_of_week = ? AND tc.status = 'active'
        ORDER BY tc.start_time
    ");
    $stmt->execute([$today, $userId, $today_day]);
    $todays_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching today's classes: " . $e->getMessage();
}

// Get personal training sessions for today
$todays_personal_sessions = [];
try {
    $stmt = $pdo->prepare("
        SELECT pt.*, t.first_name as trainer_first_name, t.last_name as trainer_last_name,
        m.first_name as member_first_name, m.last_name as member_last_name
        FROM personal_training pt
        JOIN trainers t ON pt.trainer_id = t.trainer_id
        JOIN members m ON pt.member_id = m.member_id
        WHERE pt.session_date = ? AND pt.status = 'scheduled' AND t.user_id = ? AND m.user_id = ?
        ORDER BY pt.start_time
    ");
    $stmt->execute([$today, $userId, $userId]);
    $todays_personal_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching personal sessions: " . $e->getMessage();
}

// Get upcoming bookings
$upcoming_bookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT cb.*, tc.class_name, tc.start_time, tc.end_time, tc.day_of_week,
        m.first_name as member_first_name, m.last_name as member_last_name,
        t.first_name as trainer_first_name, t.last_name as trainer_last_name
        FROM class_bookings cb
        JOIN training_classes tc ON cb.class_id = tc.class_id
        JOIN members m ON cb.member_id = m.member_id
        LEFT JOIN trainers t ON tc.trainer_id = t.trainer_id
        WHERE cb.class_date >= ? AND cb.status = 'confirmed' AND tc.user_id = ? AND m.user_id = ?
        ORDER BY cb.class_date, tc.start_time
        LIMIT 20
    ");
    $stmt->execute([$today, $userId, $userId]);
    $upcoming_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching bookings: " . $e->getMessage();
}

// For dropdowns or displays
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
    <h1 class="text-4xl font-bold">Training Schedule Management</h1>
    
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
    <div class="flex gap-4 my-6 mt-4 mb-4">
        <button onclick="showSection('schedule')" class="bg-[#800080] font-semibold hover:bg-[#690069] text-white px-4 py-2 rounded shadow cursor-pointer transition">Today's Schedule</button>
        <button onclick="showSection('classes')" class="bg-[#800080] font-semibold hover:bg-[#690069] text-white px-4 py-2 rounded shadow cursor-pointer transition">Manage Classes</button>
        <button onclick="showSection('trainers')" class="bg-[#800080] font-semibold hover:bg-[#690069] text-white px-4 py-2 rounded shadow cursor-pointer transition">Manage Trainers</button>
        <button onclick="showSection('bookings')" class="bg-[#800080] font-semibold hover:bg-[#690069] text-white px-4 py-2 rounded shadow cursor-pointer transition">Book Classes</button>
        <button onclick="showSection('personal')" class="bg-[#800080] font-semibold hover:bg-[#690069] text-white px-4 py-2 rounded shadow cursor-pointer transition">Personal Training</button>
    </div>

    <!-- Today's Schedule Section -->
    <div id="schedule" class="block bg-[#101010] rounded-2xl p-6 border border-[#585757]">
        <h2 class="text-2xl font-bold mb-2">
            Today's Schedule (<?= date('F j, Y') ?> - <?= $today_day ?>)
        </h2>
        
        <h3 class="mt-6 mb-2 text-lg font-semibold">Group Classes</h3>
        <?php if (!empty($todays_classes)): ?>
            <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Class Name</th>
                        <th class="px-4 py-2 text-left">Trainer</th>
                        <th class="px-4 py-2 text-left">Time</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Bookings</th>
                        <th class="px-4 py-2 text-left">Difficulty</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($todays_classes as $class): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= htmlspecialchars($class['class_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($class['trainer_first_name'] . ' ' . $class['trainer_last_name']) ?></td>
                        <td class="px-4 py-2"><?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($class['class_type']) ?></td>
                        <td class="px-4 py-2"><?= $class['current_bookings'] ?>/<?= $class['max_capacity'] ?></td>
                        <td class="px-4 py-2"><?= ucfirst($class['difficulty_level']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500">No classes scheduled for today.</p>
        <?php endif; ?>

        <h3 class="mt-6 mb-2 text-lg font-semibold">Personal Training Sessions</h3>
        <?php if (!empty($todays_personal_sessions)): ?>
            <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Session Type</th>
                        <th class="px-4 py-2 text-left">Trainer</th>
                        <th class="px-4 py-2 text-left">Member</th>
                        <th class="px-4 py-2 text-left">Time</th>
                        <th class="px-4 py-2 text-left">Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($todays_personal_sessions as $session): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= htmlspecialchars($session['session_type']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($session['trainer_first_name'] . ' ' . $session['trainer_last_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($session['member_first_name'] . ' ' . $session['member_last_name']) ?></td>
                        <td class="px-4 py-2"><?= date('g:i A', strtotime($session['start_time'])) ?> - <?= date('g:i A', strtotime($session['end_time'])) ?></td>
                        <td class="px-4 py-2">$<?= number_format($session['session_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500">No personal training sessions today.</p>
        <?php endif; ?>
    </div>

    <!-- Manage Classes Section -->
    <div id="classes" class="bg-[#101010] rounded-2xl p-6 text-white border border-[#585757]">
        <h2 class="text-xl font-bold">Manage Training Classes</h2>

        <div class="mt-6">
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Class Name -->
                    <div class="space-y-2">
                        <label class="block mb-2 mt-2 font-semibold">Class Name:</label>
                        <input type="text" name="class_name" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Enter class name" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Description:</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Class description..."></textarea>
                    </div>

                    <!-- Trainer -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Trainer:</label>
                        <select name="trainer_id" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                            <option value="">Select Trainer</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['trainer_id'] ?>">
                                    <?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Day of Week -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Day of Week:</label>
                        <select name="day_of_week" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                            <option value="">Select Day</option>
                            <?php foreach ($days_of_week as $day): ?>
                                <option value="<?= $day ?>"><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Start Time:</label>
                        <input type="time" name="start_time" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">End Time:</label>
                        <input type="time" name="end_time" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                    </div>

                    <!-- Max Capacity -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Max Capacity:</label>
                        <input type="number" name="max_capacity" value="20" min="1" max="50" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                    </div>

                    <!-- Class Type -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Class Type:</label>
                        <select name="class_type" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
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
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Difficulty:</label>
                        <select name="difficulty_level" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Price (₱):</label>
                        <input type="number" name="price" step="0.01" min="0" value="15.00" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700">
                    </div>

                    <!-- Equipment -->
                    <div>
                        <label class="block mb-2 mt-2 font-semibold">Equipment Needed:</label>
                        <input type="text" name="equipment_needed" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="e.g. Yoga mats, dumbbells">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="add_class" class="bg-[#800080] font-semibold hover:bg-[#690069] cursor-pointer text-white font-semibold px-6 py-2 rounded-lg shadow">
                        Add Class
                    </button>
                </div>
            </form>
        </div>

        <div class="w-full h-px mt-8 my-8 bg-[#585757] rounded-2xl border border-[#585757]"></div>
        <div class="mt-8">
            <h3 class="text-lg font-semibold mt-2 mb-2">Current Classes</h3>

            <?php if (!empty($training_classes)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border border-gray-700 bg-gray-900 text-white rounded-lg">
                        <thead class="bg-gray-800 text-gray-300">
                            <tr>
                                <th class="px-4 py-2">Class Name</th>
                                <th class="px-4 py-2">Day</th>
                                <th class="px-4 py-2">Time</th>
                                <th class="px-4 py-2">Trainer</th>
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">Capacity</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Difficulty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($training_classes as $class): ?>
                            <tr class="border-t border-gray-700 hover:bg-gray-800">
                                <td class="px-4 py-2"><?= htmlspecialchars($class['class_name']) ?></td>
                                <td class="px-4 py-2"><?= $class['day_of_week'] ?></td>
                                <td class="px-4 py-2"><?= date('g:i A', strtotime($class['start_time'])) ?>–<?= date('g:i A', strtotime($class['end_time'])) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($class['trainer_first_name'] . ' ' . $class['trainer_last_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($class['class_type']) ?></td>
                                <td class="px-4 py-2"><?= $class['max_capacity'] ?></td>
                                <td class="px-4 py-2">$<?= number_format($class['price'], 2) ?></td>
                                <td class="px-4 py-2"><?= ucfirst($class['difficulty_level']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-400">No classes available.</p>
            <?php endif; ?>
        </div>
    </div>
 

    <!-- Manage Trainers Section -->
    <div id="trainers" class="bg-[#101010] rounded-2xl p-6 text-white border border-[#585757]" style="display: none;">
        <h2 class="text-xl font-bold mb-4">Manage Trainers</h2>
        
        <h3 class="text-lg font-semibold mb-2">Add New Trainer</h3>
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 font-semibold">First Name:</label>
                    <input type="text" name="first_name" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="First name" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Last Name:</label>
                    <input type="text" name="last_name" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Last name" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Email:</label>
                    <input type="email" name="email" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="email@example.com" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Phone:</label>
                    <input type="tel" name="phone" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="Phone number">
                </div>
                <div class="md:col-span-2">
                    <label class="block mb-2 font-semibold">Specialization:</label>
                    <textarea name="specialization" rows="2" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="e.g. Strength Training, Yoga"></textarea>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Experience (Years):</label>
                    <input type="number" name="experience_years" min="0" value="0" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700">
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Certification:</label>
                    <input type="text" name="certification" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="e.g. NASM, ACE, ACSM">
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Hourly Rate (₱):</label>
                    <input type="number" name="hourly_rate" step="0.01" min="0" value="50.00" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="50.00">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" name="add_trainer" class="bg-[#800080] font-semibold hover:bg-[#690069] cursor-pointer text-white px-6 py-2 rounded-lg shadow">
                    Add Trainer
                </button>
            </div>
        </form>

        <div class="w-full h-px mt-8 my-8 bg-[#585757] rounded-2xl border border-[#585757]"></div>
        <h3 class="text-lg font-semibold mt-2 mb-2">Current Trainers</h3>
        <?php if (!empty($trainers)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border border-gray-700 bg-gray-900 text-white rounded-lg">
                    <thead class="bg-gray-800 text-gray-300">
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Experience</th>
                            <th class="px-4 py-2">Rate/Hour</th>
                            <th class="px-4 py-2">Specialization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainers as $trainer): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-800">
                            <td class="px-4 py-2"><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($trainer['email']) ?></td>
                            <td class="px-4 py-2"><?= $trainer['experience_years'] ?> years</td>
                            <td class="px-4 py-2">$<?= number_format($trainer['hourly_rate'], 2) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($trainer['specialization']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-400">No trainers available.</p>
        <?php endif; ?>
    </div>

    <!-- Book Classes Section -->
    <div id="bookings" class="bg-[#101010] rounded-2xl p-6 text-white border border-[#585757]" style="display: none;">
        <h2 class="text-xl font-bold mb-4">Book Classes</h2>
        
        <h3 class="text-lg font-semibold mb-2">Book Class for Member</h3>
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 font-semibold">Member:</label>
                    <select name="member_id" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                        <option value="">Select Member</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member['member_id'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name'] . ' (' . $member['email'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Class:</label>
                    <select name="class_id" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                        <option value="">Select Class</option>
                        <?php foreach ($training_classes as $class): ?>
                            <option value="<?= $class['class_id'] ?>">
                                <?= htmlspecialchars($class['class_name']) ?> - <?= $class['day_of_week'] ?> <?= date('g:i A', strtotime($class['start_time'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Class Date:</label>
                    <input type="date" name="class_date" min="<?= $today ?>" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Notes:</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="Optional notes..."></textarea>
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" name="book_class" class="bg-[#800080] font-semibold hover:bg-[#690069] cursor-pointer text-white px-6 py-2 rounded-lg shadow">
                    Book Class
                </button>
            </div>
        </form>

        <div class="w-full h-px mt-8 my-8 bg-[#585757] rounded-2xl border border-[#585757]"></div>
        <h3 class="text-lg font-semibold mt-2 mb-2">Upcoming Bookings</h3>
        <?php if (!empty($upcoming_bookings)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border border-gray-700 bg-gray-900 text-white rounded-lg">
                    <thead class="bg-gray-800 text-gray-300">
                        <tr>
                            <th class="px-4 py-2">Class</th>
                            <th class="px-4 py-2">Member</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Time</th>
                            <th class="px-4 py-2">Trainer</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_bookings as $booking): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-800">
                            <td class="px-4 py-2"><?= htmlspecialchars($booking['class_name']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($booking['member_first_name'] . ' ' . $booking['member_last_name']) ?></td>
                            <td class="px-4 py-2"><?= date('M j, Y', strtotime($booking['class_date'])) ?></td>
                            <td class="px-4 py-2"><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($booking['trainer_first_name'] . ' ' . $booking['trainer_last_name']) ?></td>
                            <td class="px-4 py-2"><?= ucfirst($booking['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-400">No upcoming bookings.</p>
        <?php endif; ?>
    </div>

    <!-- Personal Training Section -->
    <div id="personal" class="bg-[#101010] rounded-2xl p-6 text-white border border-[#585757]" style="display: none;">
        <h2 class="text-xl font-bold mb-4">Personal Training</h2>
        
        <h3 class="text-lg font-semibold mb-2">Schedule Personal Training Session</h3>
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 font-semibold">Trainer:</label>
                    <select name="trainer_id" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                        <option value="">Select Trainer</option>
                        <?php foreach ($trainers as $trainer): ?>
                            <option value="<?= $trainer['trainer_id'] ?>">
                                <?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?> - $<?= number_format($trainer['hourly_rate'], 2) ?>/hr
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Member:</label>
                    <select name="member_id" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                        <option value="">Select Member</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member['member_id'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Session Date:</label>
                    <input type="date" name="session_date" min="<?= $today ?>" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Start Time:</label>
                    <input type="time" name="start_time" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">End Time:</label>
                    <input type="time" name="end_time" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Session Type:</label>
                    <select name="session_type" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                        <option value="personal">Personal Training</option>
                        <option value="nutritional">Nutritional Consultation</option>
                        <option value="assessment">Fitness Assessment</option>
                        <option value="rehabilitation">Rehabilitation</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Session Price (₱):</label>
                    <input type="number" name="session_price" step="0.01" min="0" value="75.00" placeholder="75.00" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block mb-2 font-semibold">Notes:</label>
                    <textarea name="notes" rows="4" class="w-full rounded-lg px-3 py-2 bg-gray-900 border border-gray-700" placeholder="Session goals, special requirements..."></textarea>
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" name="schedule_personal" class="bg-[#800080] font-semibold hover:bg-[#690069] cursor-pointer text-white px-6 py-2 rounded-lg shadow">
                    Schedule Session
                </button>
            </div>
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

