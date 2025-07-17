<?php
require_once __DIR__ . '/../../db/db.php';

try {
    // Fetch staff
    $stmtStaff = $pdo->query("
        SELECT 
            staff_id AS id,
            first_name,
            last_name,
            email,
            phone,
            position,
            status,
            'staff' AS role
        FROM staff
    ");
    $staff = $stmtStaff->fetchAll(PDO::FETCH_ASSOC);

    // Fetch trainers
    $stmtTrainers = $pdo->query("
        SELECT 
            trainer_id AS id,
            first_name,
            last_name,
            email,
            phone,
            specialization,
            status,
            'trainer' AS role
        FROM trainers
    ");
    $trainers = $stmtTrainers->fetchAll(PDO::FETCH_ASSOC);

    // Combine both results into one list
    $staffList = array_merge($staff, $trainers);

} catch (PDOException $e) {
    echo "<p class='text-red-500'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $staffList = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $deleteId = $_POST['delete_id'];
    $role = $_POST['role'];

    if ($role === 'staff') {
        $stmtDelete = $pdo->prepare("DELETE FROM staff WHERE staff_id = ?");
    } elseif ($role === 'trainer') {
        $stmtDelete = $pdo->prepare("DELETE FROM trainers WHERE trainer_id = ?");
    }

    if (isset($stmtDelete)) {
        $stmtDelete->execute([$deleteId]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
</head>
<body>
    <h1 class="text-4xl font-bold">Staff Attendance</h1>
    <?php include 'app/includes/staffmember.php'; ?>

    <h1 class="text-4xl font-bold mb-4 text-white">Staff List</h1>
    <?php if (!empty($staffList)): ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-700 text-white">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-700 px-4 py-2">ID</th>
                        <th class="border border-gray-700 px-4 py-2">Name</th>
                        <th class="border border-gray-700 px-4 py-2">Email</th>
                        <th class="border border-gray-700 px-4 py-2">Phone</th>
                        <th class="border border-gray-700 px-4 py-2">Role</th>
                        <th class="border border-gray-700 px-4 py-2">Position/Specialization</th>
                        <th class="border border-gray-700 px-4 py-2">Status</th>
                        <th class="border border-gray-700 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffList as $person): ?>
                    <tr class="hover:bg-gray-700">
                        <td class="border border-gray-700 px-4 py-2"><?= $person['id'] ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= $person['first_name'] . ' ' . $person['last_name'] ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= $person['email'] ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= $person['phone'] ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= ucfirst($person['role']) ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= $person['position'] ?? $person['specialization'] ?></td>
                        <td class="border border-gray-700 px-4 py-2"><?= $person['status'] ?></td>
                        <td class="border border-gray-700 px-4 py-2">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                <input type="hidden" name="delete_id" value="<?= $person['id'] ?>">
                                <input type="hidden" name="role" value="<?= $person['role'] ?>">
                                <button type="submit" name="delete" style="background-color: #722323;"  
                                        class="bg-[#722323] cursor-pointer text-white font-bold px-4 py-1 rounded shadow transition">
                                    Delete
                                </button>
                            </form>
                        </td>   

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-400">No staff or trainers found.</p>
    <?php endif; ?>

</body>
</html>