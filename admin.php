<?php
$conn = new mysqli("localhost", "root", "", "food_donation");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the delete parameter is set in the URL
if (isset($_GET['delete'])) {
    $donation_id = (int)$_GET['delete'];

    // SQL query to delete the record using prepared statement
    $delete_query = "DELETE FROM donations WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $donation_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Donation record deleted successfully.'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
    $stmt->close();
}

$sql = "SELECT * FROM donations ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donation Submissions - Food Donation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4bb543;
            --danger: #ff3333;
            --warning: #ffbe0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--dark);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        h1 {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        h1 i {
            color: var(--accent);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card p {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }
        
        th {
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .badge-pending {
            background-color: rgba(255, 190, 11, 0.2);
            color: #b38a00;
        }
        
        .badge-completed {
            background-color: rgba(75, 181, 67, 0.2);
            color: #2e7d32;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
        }
        
        .action-btn i {
            margin-right: 0.25rem;
        }
        
        .delete-btn {
            background-color: var(--danger);
        }
        
        .delete-btn:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-utensils"></i> Donation Submissions</h1>
            <div class="user-info">
                <span>Welcome, Admin</span>
                <i class="fas fa-user-circle" style="margin-left: 0.5rem; font-size: 1.2rem; color: var(--primary);"></i>
            </div>
        </header>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Donations</h3>
                <p><?= $result->num_rows ?></p>
            </div>
            <div class="stat-card">
                <h3>Today's Donations</h3>
                <p>12</p> <!-- You would replace this with actual dynamic data -->
            </div>
            <div class="stat-card">
                <h3>Pending Pickups</h3>
                <p>5</p> <!-- You would replace this with actual dynamic data -->
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <p>47</p> <!-- You would replace this with actual dynamic data -->
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Contact</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row["name"]) ?></strong><br>
                                    <small><?= htmlspecialchars($row["email"]) ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row["phone"]) ?><br>
                                    <small><?= htmlspecialchars(substr($row["address"], 0, 20)) ?>...</small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row["food_type"]) ?></strong><br>
                                    Qty: <?= htmlspecialchars($row["quantity"]) ?><br>
                                    BB: <?= htmlspecialchars($row["best_before"]) ?>
                                </td>
                                <td>
                                    <span class="badge badge-pending">Pending</span>
                                </td>
                                <td>
                                    <?= date('M j, Y', strtotime($row["created_at"])) ?><br>
                                    <small><?= date('g:i a', strtotime($row["created_at"])) ?></small>
                                </td>
                                <td>
                                    <button class="action-btn delete-btn" onclick="confirmDelete(<?= $row['id'] ?>)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: var(--accent); margin-bottom: 1rem;"></i>
                                <p>No donations yet!</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this donation?')) {
                window.location.href = 'admin.php?delete=' + id;
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>