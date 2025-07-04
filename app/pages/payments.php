<?php
// Include database connection file
require_once __DIR__ . '/../../db/db.php';

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
    <h1 class="text-4xl font-bold">Manage Payments</h1>
    <?php include 'app/includes/paymentcard.php'; ?>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php endif; ?>

    <div class="mt-4 mb-4 gap-4 w-full flex">
        <button onclick="showViewPayments()" class="w-1/2 py-4 bg-[#222121] hover:bg-[#800080] cursor-pointer text-white font-semibold px-4 rounded border border-[#585757]">View Payment</button>
        <button onclick="showAddForm()" class="w-1/2 py-4 bg-[#222121] hover:bg-[#800080] cursor-pointer text-white font-semibold px-4 rounded border border-[#585757]">Add Payment</button>
    </div>

    <!-- Add Payment Form -->
    <div id="addPaymentForm" class="mt-8 p-6 bg-[#101010] text-white rounded-lg shadow-lg w-full max-w-md mx-auto mb-4">
        <h2 class="text-2xl font-bold mb-4">Add New Payment</h2>
        <form method="POST" class="space-y-4">
            <!-- Member Selection -->
            <div>
                <label class="block mb-1 font-semibold">Member:</label>
                <select name="member_id" required class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Member</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['member_id'] ?>">
                            <?= $member['first_name'] . ' ' . $member['last_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Amount -->
            <div>
                <label class="block mb-1 font-semibold">Amount:</label>
                <input type="number" name="amount" step="0.01" required class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <!-- Payment Type -->
            <div>
                <label class="block mb-1 font-semibold">Payment Type:</label>
                <select name="payment_type" required class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Type</option>
                    <option value="Monthly">Monthly</option>
                    <option value="Daily">Daily</option>
                    <option value="Annual">Annual</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block mb-1 font-semibold">Status:</label>
                <select name="status" required class="w-full p-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="PAID">PAID</option>
                    <option value="PENDING">PENDING</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between gap-4 mt-6">
                <button type="submit" name="add_payment" class="w-full bg-[#800080] hover:bg-[#690069] text-white font-semibold py-2 px-4 rounded shadow transition duration-200 ease-in-out">
                    Add Payment
                </button>
                <button type="button" onclick="hideAddForm()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200 ease-in-out">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- View Payments Section -->
    <div id="viewPayments">
        <form method="GET">
            <input class="w-1/4 px-2 py-1 rounded bg-black text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="search" placeholder="Search by member name..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="ml-2 bg-[#800080] hover:bg-[#690069] cursor-pointer text-white font-semibold py-1 px-4 rounded" type="submit">Search</button>
            <button class="ml-2 cursor-pointer bg-gray-600 hover:bg-gray-700 text-white font-semibold py-1 px-4 rounded" type="submit">Clear</button>
        </form>

        <h2 class="mt-4 text-xl">Payments List</h2>
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
