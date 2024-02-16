<?php
session_start();

// Check if the admin is not logged in, redirect to admin_login.php
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to add a new module
    // Validate and process form data (add your own validation and query logic)

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
    $moduleName = $_POST['module_name'];
    $moduleDate = $_POST['module_date'];
    $moduleStartTime = $_POST['module_start_time'];
    $moduleEndTime = $_POST['module_end_time'];

    // Insert the new module into the 'modules' table
    $stmt = $pdo->prepare("INSERT INTO modules (name, module_date, module_start_time, module_end_time) VALUES (:moduleName, :moduleDate, :moduleStartTime, :moduleEndTime)");
    $stmt->bindParam(':moduleName', $moduleName);
    $stmt->bindParam(':moduleDate', $moduleDate);
    $stmt->bindParam(':moduleStartTime', $moduleStartTime);
    $stmt->bindParam(':moduleEndTime', $moduleEndTime);
    $stmt->execute();

    // Redirect back to admin dashboard or any other page
    header('Location: admin_dashboard.php');
    exit();
}
?>

<!-- HTML form for adding a new module -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Module - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <h2>Add Module</h2>

    <form method="post" action="add_module.php">
        <div class="form-group">
            <label for="moduleName">Module Name:</label>
            <input type="text" class="form-control" id="moduleName" name="module_name" required>
        </div>

        <div class="form-group">
            <label for="moduleDate">Module Date:</label>
            <input type="date" class="form-control" id="moduleDate" name="module_date" required>
        </div>

        <div class="form-group">
            <label for="moduleStartTime">Module Start Time:</label>
            <input type="time" class="form-control" id="moduleStartTime" name="module_start_time" required>
        </div>

        <div class="form-group">
            <label for="moduleEndTime">Module End Time:</label>
            <input type="time" class="form-control" id="moduleEndTime" name="module_end_time" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Module</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
