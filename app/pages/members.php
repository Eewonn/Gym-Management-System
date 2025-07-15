<?php
require_once __DIR__ . '/../../db/db.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Unauthorized access.");
}

$userId = $_SESSION['user_id'];
$success_message = "";

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_member'])) {
        // Add new member with user_id
        $stmt = $pdo->prepare("
            INSERT INTO members (
                user_id, first_name, last_name, phone, email, membership_type
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['membership_type']
        ]);
        $success_message = "Member added successfully!";
    }
}

// Handle search
$search_query = "";
$members = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $pdo->prepare("
        SELECT member_id, first_name, last_name, email, phone, membership_type, join_date, status 
        FROM members 
        WHERE user_id = ? AND (first_name LIKE ? OR last_name LIKE ?)
        ORDER BY last_name, first_name
    ");
    $stmt->execute([$userId, "%$search_query%", "%$search_query%"]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Get all members for this user
    $stmt = $pdo->prepare("
        SELECT member_id, first_name, last_name, email, phone, membership_type, join_date, status 
        FROM members 
        WHERE user_id = ?
        ORDER BY join_date DESC
    ");
    $stmt->execute([$userId]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management</title>
</head>
<body>
    <h1 class="text-4xl font-bold">Manage Members</h1>
    

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>

    
    <div class="flex min-h-screen p-8 gap-8">
        <!-- Members List (Left Side) -->
        <div class="w-2/3 border p-4 bg-[#222121] rounded-md">
            <h2 class="text-xl font-bold mb-4 text-white">Search Members</h2>
            <form method="GET" class="flex gap-4 mb-4">
                <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search_query) ?>" class="flex-1 px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                <button type="submit" class="bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-1 px-4 rounded">Search</button>
                <a href="members.php" class="cursor-pointer bg-gray-600 hover:bg-gray-700 text-white font-semibold py-1 px-4 rounded">Clear</a>
            </form>

            <h2 class="text-xl font-bold mb-4 text-white">Members List</h2>
            <?php if (!empty($members)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-700 text-white">
                        <thead>
                            <tr class="bg-gray-800">
                                <th class="border border-gray-700 px-4 py-2">ID</th>
                                <th class="border border-gray-700 px-4 py-2">Name</th>
                                <th class="border border-gray-700 px-4 py-2">Type</th>
                                <th class="border border-gray-700 px-4 py-2">Email</th>
                                <th class="border border-gray-700 px-4 py-2">Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                            <tr class="hover:bg-gray-700">
                                <td class="border border-gray-700 px-4 py-2"><?= $member['member_id'] ?></td>
                                <td class="border border-gray-700 px-4 py-2"><?= $member['first_name'] . ' ' . $member['last_name'] ?></td>
                                <td class="border border-gray-700 px-4 py-2"><?= $member['membership_type'] ?></td>
                                <td class="border border-gray-700 px-4 py-2"><?= $member['email'] ?></td>
                                <td class="border border-gray-700 px-4 py-2"><?= $member['phone'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-400">No members found.</p>
            <?php endif; ?>
        </div>

        <!-- Add New Member Form (Right Side) -->
        <div class="w-1/3 flex flex-col gap-8">
            
            <div class="h-full border p-4 bg-[#101010] rounded-md">
                <h2 class="text-xl font-bold mb-2 text-white">Add New Member</h2>
                <p class="text-sm text-gray-400 mb-4">Quickly add new member to the system</p>
                <form method="POST">
                    <div class="flex gap-4 mt-2 mb-4">
                        <div class="flex flex-col w-1/2">
                            <label class="mb-1 text-base font-medium text-gray-200">First Name:</label>
                            <input placeholder="First Name" type="text" name="first_name" required class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                        <div class="flex flex-col w-1/2">
                            <label class="mb-1 text-base font-medium text-gray-200">Last Name:</label>
                            <input placeholder="Last Name" type="text" name="last_name" required class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Email:</label>
                        <input placeholder="youremail@email.com" type="email" name="email" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Phone:</label>
                        <input placeholder="Contact Number" type="text" name="phone" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Membership Type:</label>
                        <select name="membership_type" required class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">Select Type</option>
                            <option value="Basic">Basic</option>
                            <option value="Standard">Standard</option>
                            <option value="Premium">Premium</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="add_member" class="w-full bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-2 px-6 rounded shadow transition duration-200">
                            Add Member
                        </button>
                    </div>
                </form>
            </div>

            <div class="h-full border p-4 bg-[#101010] rounded-md>
                <h2 class="text-xl font-bold mb-2 text-white">Edit Member</h2>
                <p class="text-sm text-gray-400 mb-4">Update member details</p>
                <form method="POST" action="app/includes/editmember.php">
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Member ID:</label>
                        <input type="text" name="member_id" placeholder="Enter Member ID" required class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">First Name:</label>
                        <input type="text" name="first_name" placeholder="First Name" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Last Name:</label>
                        <input type="text" name="last_name" placeholder="Last Name" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Email:</label>
                        <input type="email" name="email" placeholder="Email" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Phone:</label>
                        <input type="text" name="phone" placeholder="Phone" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 text-base font-medium text-gray-200">Membership Type:</label>
                        <select name="membership_type" class="w-full px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">Select Type</option>
                            <option value="Basic">Basic</option>
                            <option value="Standard">Standard</option>
                            <option value="Premium">Premium</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="edit_member" class="w-full bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-2 px-6 rounded shadow transition duration-200">
                            Update Member
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

    
    
</body>
</html>