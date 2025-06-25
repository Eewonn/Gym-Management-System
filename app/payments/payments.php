<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

// Include database connection file
require_once '../../db/db.php'; 

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_payment'])) {
        // Add new payment
        $stmt = $pdo->prepare("INSERT INTO payments (member_id, amount, payment_type, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['member_id'],
            $_POST['amount'], 
            $_POST['payment_type'],
            $_POST['status']
        ]);
        $success_message = "Payment added successfully!";
    }
}

// Handle search
$search_query = "";
$payments = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $pdo->prepare("
        SELECT p.*, m.first_name, m.last_name 
        FROM payments p 
        JOIN members m ON p.member_id = m.member_id 
        WHERE m.first_name LIKE ? OR m.last_name LIKE ? 
        ORDER BY p.payment_date DESC
    ");
    $stmt->execute(["%$search_query%", "%$search_query%"]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Get all payments with member names
    $stmt = $pdo->query("
        SELECT p.*, m.first_name, m.last_name 
        FROM payments p 
        JOIN members m ON p.member_id = m.member_id 
        ORDER BY p.payment_date DESC
    ");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all members for dropdown
$members_stmt = $pdo->query("SELECT member_id, first_name, last_name FROM members WHERE status = 'active' ORDER BY first_name");
$members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management</title>
</head>
<body>
    <h1>Payments Management</h1>
    
    <p><a href="../dashboard/dashboard.php">← Back to Dashboard</a></p>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>

    <div>
        <button onclick="showAddForm()">Add Payment</button>
        <button onclick="showViewPayments()">View Payment</button>
    </div>

    <!-- Add Payment Form -->
    <div id="addPaymentForm" style="display: none;">
        <h2>Add New Payment</h2>
        <form method="POST">
            <p>
                <label>Member:</label><br>
                <select name="member_id" required>
                    <option value="">Select Member</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['member_id'] ?>">
                            <?= $member['first_name'] . ' ' . $member['last_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label>Amount:</label><br>
                <input type="number" name="amount" step="0.01" required>
            </p>
            <p>
                <label>Payment Type:</label><br>
                <select name="payment_type" required>
                    <option value="">Select Type</option>
                    <option value="Monthly">Monthly</option>
                    <option value="Daily">Daily</option>
                    <option value="Annual">Annual</option>
                </select>
            </p>
            <p>
                <label>Status:</label><br>
                <select name="status" required>
                    <option value="PAID">PAID</option>
                    <option value="PENDING">PENDING</option>
                </select>
            </p>
            <p>
                <button type="submit" name="add_payment">Add Payment</button>
                <button type="button" onclick="hideAddForm()">Cancel</button>
            </p>
        </form>
    </div>

    <!-- View Payments Section -->
    <div id="viewPayments">
        <h2>Search Payments</h2>
        <form method="GET">
            <input type="text" name="search" placeholder="Search by member name..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
            <a href="payments.php">Clear</a>
        </form>

        <h2>Payments List</h2>
        <?php if (!empty($payments)): ?>
            <table border="1">
                <tr>
                    <th>NAME</th>
                    <th>TYPE</th>
                    <th>AMOUNT</th>
                    <th>STATUS</th>
                </tr>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment['first_name'] . ' ' . $payment['last_name'] ?></td>
                    <td><?= $payment['payment_type'] ?></td>
                    <td><?= $payment['amount'] ?></td>
                    <td><?= $payment['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No payments found.</p>
        <?php endif; ?>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addPaymentForm').style.display = 'block';
            document.getElementById('viewPayments').style.display = 'none';
        }

        function showViewPayments() {
            document.getElementById('addPaymentForm').style.display = 'none';
            document.getElementById('viewPayments').style.display = 'block';
        }

        function hideAddForm() {
            document.getElementById('addPaymentForm').style.display = 'none';
            document.getElementById('viewPayments').style.display = 'block';
        }

        // Show view payments by default
        showViewPayments();
    </script>
</body>
</html>
