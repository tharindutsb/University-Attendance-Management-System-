<?php
session_start();

// Set the time zone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Check if the student is not logged in, redirect to index.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get the student ID from the session
$studentId = $_SESSION['user_id'];

// Database connection details
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

// Fetch modules for the current time
$currentDateTime = date('Y-m-d H:i:s');
$stmtModules = $pdo->prepare("SELECT id, name FROM modules WHERE :currentDateTime BETWEEN CONCAT(module_date, ' ', module_start_time) AND CONCAT(module_date, ' ', module_end_time)");
$stmtModules->bindParam(':currentDateTime', $currentDateTime);
$stmtModules->execute();
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleId = $_POST['module_id'];
    $username = $_SESSION['user_id'];
    $attendanceDate = date('Y-m-d');
    $attendanceTimestamp = date('Y-m-d H:i:s');
    $attendanceStatus = 'Present'; // You can customize this based on your logic

    // Check if the student has already marked attendance for this module on the same day
    $stmtCheckAttendance = $pdo->prepare("SELECT id FROM attendance WHERE student_id = :username AND module_id = :moduleId AND attendance_date = :attendanceDate");
    $stmtCheckAttendance->bindParam(':username', $username);
    $stmtCheckAttendance->bindParam(':moduleId', $moduleId);
    $stmtCheckAttendance->bindParam(':attendanceDate', $attendanceDate);
    $stmtCheckAttendance->execute();

    if ($stmtCheckAttendance->rowCount() === 0) {
        // Student hasn't marked attendance for this module on the same day, proceed with marking
        $stmtInsertAttendance = $pdo->prepare("INSERT INTO attendance (student_id, module_id, attendance_date, attendance_timestamp, attendance_status) VALUES (:username, :moduleId, :attendanceDate, :attendanceTimestamp, :attendanceStatus)");
        $stmtInsertAttendance->bindParam(':username', $username);
        $stmtInsertAttendance->bindParam(':moduleId', $moduleId);
        $stmtInsertAttendance->bindParam(':attendanceDate', $attendanceDate);
        $stmtInsertAttendance->bindParam(':attendanceTimestamp', $attendanceTimestamp);
        $stmtInsertAttendance->bindParam(':attendanceStatus', $attendanceStatus);
        $stmtInsertAttendance->execute();
        $_SESSION['attendance_marked'] = true;
        // Echo JavaScript function to show pop-up
        echo '<script>showPopupMessage("Attendance marked successfully!");</script>';

        // Redirect to prevent form resubmission
        header('Location: student_dashboard.php');
        exit();
    } else {
        // Student has already marked attendance for this module on the same day
        $_SESSION['attendance_marked'] = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
<!-- Add this script in the head section of your HTML -->
<script>
    // Check if the session variable is set and display the corresponding pop-up
    document.addEventListener('DOMContentLoaded', function () {
        <?php
        if (isset($_SESSION['attendance_marked']) && $_SESSION['attendance_marked'] === true) {
            echo 'alert("Attendance marked successfully!");';
        } elseif (isset($_SESSION['attendance_marked']) && $_SESSION['attendance_marked'] === false) {
            echo 'alert("You have already marked attendance for this module today.");';
        }
        // Clear the session variable
        unset($_SESSION['attendance_marked']);
        ?>
    });
</script>

    <style>
        /* Add styles for the pop-up message */
        .popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #28a745; /* Green background color */
    color: white; /* White text color */
    padding: 16px; /* Some padding */
    border: none; /* No borders */
    border-radius: 8px; /* Rounded corners */
    z-index: 1; /* Sit on top */
    min-width: 250px; /* Set a minimum width */
    text-align: center; /* Centered text */
}

        .popup.show {
            opacity: 1;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Student Dashboard</h2>

    <?php if (count($modules) > 0): ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="module_id">Select Module:</label>
                <select class="form-control" id="module_id" name="module_id" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>">
                            <?php echo $module['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Mark Attendance</button>
        </form>
    <?php else: ?>
        <p class="text-center">No modules available for attendance at the current time.</p>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
