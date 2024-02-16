<?php
session_start();

// Check if the admin is not logged in, redirect to admin_login.php
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

// Connect to the database
$host = 'localhost:3380';
$dbname = 'attendance';
$user = 'root';
$password = ''; // No password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Fetch the timetable data from the modules table
$stmtTimetable = $pdo->prepare("SELECT id, name AS module_name, module_date, module_start_time, module_end_time  FROM modules");
$stmtTimetable->execute();
$timetable = $stmtTimetable->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Timetable - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #dee2e6;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Admin Timetable</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Module Name</th>
                <th>Module Date</th>
                <th>Module Start Time</th>
                <th>Module End Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timetable as $row): ?>
                <tr>
                    <td><?php echo $row['module_name']; ?></td>
                    <td><?php echo $row['module_date']; ?></td>
                    <td><?php echo $row['module_start_time']; ?></td>
                    <td><?php echo $row['module_end_time']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
