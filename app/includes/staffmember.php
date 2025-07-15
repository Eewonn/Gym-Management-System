
<?php
require_once __DIR__ . '/../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff_member'])) {
    // Basic validation
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $hire_date = date('Y-m-d');
    if ($first_name && $last_name && $email && $phone && $position &&  $hire_date) {
        $stmt = $pdo->prepare("INSERT INTO staff (first_name, last_name, email, phone, position, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$first_name, $last_name, $email, $phone, $position, $hire_date]);
    }

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $staff_id = $_POST['position'] ?? '';
    $date = $_POST['attendance_date'] ?? '';
    $work_hours = $_POST['work_hours'] ?? 0;
    $status = $_POST['status'] ?? '';
    $notes = $_POST['remarks'] ?? '';

    if ($staff_id && $date && $status) {
        $stmt = $pdo->prepare("
            INSERT INTO staff_attendance (staff_id, date, work_hours, notes, status)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                work_hours = VALUES(work_hours),
                notes = VALUES(notes),
                status = VALUES(status)
        ");
        $stmt->execute([$staff_id, $date, $work_hours, $notes, $status]);
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$staffs_stmt = $pdo->query("SELECT staff_id, first_name, last_name FROM staff WHERE status = 'active' ORDER BY first_name");
$staffs = $staffs_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div>
    
    <div class="flex p-8 gap-8 items-stretch h-1/3">

        <div class="w-1/2 border p-4 bg-[#101010] rounded-md flex flex-col">
            <h2 class="text-xl font-bold mb-2 text-white">Add New Staff Member</h2>
            <form action="" method="POST"> 
                <div class="flex gap-4 mt-4 mb-4">
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                    </div>
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                    </div>
                </div>

                <label class="mb-1 text-base font-medium text-gray-200" for="email">Email</label>
                <input type="email" id="email" name="email" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>

                <div class="flex gap-4 mt-4 mb-4">
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                    </div>
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="position">Position:</label>
                        <select id="position" name="position" class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                            <option value="">Select Position</option>
                            <option value="Manager">Manager</option>
                            <option value="Trainer">Trainer</option>
                            <option value="Receptionist">Receptionist</option>
                            <option value="Cleaner">Cleaner</option>
                        </select>
                    </div>    
                </div>
                <div class="mt-4">
                    
                    <button type="submit" 
                        name="add_staff_member" 
                        class="w-full bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-2 px-6 rounded shadow transition duration-200">
                        Add Staff Member
                    </button>
                </div>
            </form>
        </div>


        <!-- mark attendance -->
         
        <div class="w-1/2 border p-4 bg-[#101010] rounded-md flex flex-col">
            <h2 class="text-xl font-bold mb-2 text-white">Mark Attendance</h1>
            <form action="" method="POST">
                <div class="flex mt-4 mb-4">
                    <div class="flex flex-col w-full">
                        <label class="mb-1 text-base font-medium text-gray-200" for="position">Staff Member:</label>
                        <select id="position" name="position" class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                            <option value="">Select Staff Member</option>
                            <?php foreach ($staffs as $staff): ?>
                                <option value="<?= $staff['staff_id'] ?>">
                                    <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>    
                </div>
                <div class="flex gap-4 mt-4 mb-4">
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="attendance_date">Date</label>
                        <input type="date" id="attendance_date" name="attendance_date" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                    </div>
                    <div class="flex flex-col w-1/2">
                        <label class="mb-1 text-base font-medium text-gray-200" for="work_hours">Work Hours</label>
                        <input type="number" id="work_hours" name="work_hours" min="0" step="0.1" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                    </div>
                </div>

                <div class="flex mt-4 mb-4">
                    <div class="flex flex-col w-full">
                        <label class="mb-1 text-base font-medium text-gray-200" for="status">Status:</label>
                        <select id="status" name="status" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
                            <option value="">Select Status</option>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>    
                </div>

                <div class="flex flex-col mt-4 mb-4">
                    <label class="mb-1 text-base font-medium text-gray-200" for="remarks">Remarks:</label>
                    <textarea id="remarks" name="remarks" rows="3" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600"></textarea>
                </div>

                
                <div class="mt-4">
                    <button type="submit" 
                    name="mark_attendance" 
                    class="w-full bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-2 px-6 rounded shadow transition duration-200"
                    onclick="return confirm('Are you sure you want to mark attendance?')">
                        Mark Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
    

    <!-- todats attendance -->
    <div>

    </div>

    <!-- attendance history -->

    <div>
        
    </div>


</div>