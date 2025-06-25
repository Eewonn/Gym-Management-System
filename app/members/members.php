<?php
session_start();
//Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

//Include database connection file
require_once '../../db/db.php'; 

//Handle form submissions
if ($_POST) {
    if (isset($_POST['add_member'])) {
        //Add new member
        $stmt = $pdo->prepare("INSERT INTO members (first_name, last_name, date_of_birth, gender, phone, email, address, membership_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'], 
            $_POST['date_of_birth'],
            $_POST['gender'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['address'],
            $_POST['membership_type']
        ]);
        $success_message = "Member added successfully!";
    }
}

//Handle search
$search_query = "";
$members = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM members WHERE first_name LIKE ? OR last_name LIKE ? ORDER BY last_name, first_name");
    $stmt->execute(["%$search_query%", "%$search_query%"]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    //Get all members
    $stmt = $pdo->query("SELECT member_id, first_name, last_name, email, phone, membership_type, join_date, status FROM members ORDER BY join_date DESC");
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
    <h1>Members Management</h1>
    
    <p><a href="../dashboard/dashboard.php">← Back to Dashboard</a></p>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>

    <h2>Add New Member</h2>
    <form method="POST">
        <p>
            <label>First Name:</label><br>
            <input type="text" name="first_name" required>
        </p>
        <p>
            <label>Last Name:</label><br>
            <input type="text" name="last_name" required>
        </p>
        <p>
            <label>Email:</label><br>
            <input type="email" name="email">
        </p>
        <p>
            <label>Phone:</label><br>
            <input type="text" name="phone">
        </p>
        <p>
            <label>Membership Type:</label><br>
            <select name="membership_type" required>
                <option value="">Select Type</option>
                <option value="Basic">Basic</option>
                <option value="Standard">Standard</option>
                <option value="Premium">Premium</option>
            </select>
        </p>
        <p>
            <button type="submit" name="add_member">Add Member</button>
        </p>
    </form>

    <h2>Search Members</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">Search</button>
        <a href="members.php">Clear</a>
    </form>

    <h2>Members List</h2>
    <?php if (!empty($members)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
            <?php foreach ($members as $member): ?>
            <tr>
                <td><?= $member['member_id'] ?></td>
                <td><?= $member['first_name'] . ' ' . $member['last_name'] ?></td>
                <td><?= $member['membership_type'] ?></td>
                <td><?= $member['email'] ?></td>
                <td><?= $member['phone'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No members found.</p>
    <?php endif; ?>
</body>
</html>