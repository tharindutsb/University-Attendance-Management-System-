<?php
session_start();

// Check if the admin is not logged in, redirect to admin_login.php
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
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

// Fetch modules for selection
$stmtModules = $pdo->prepare("SELECT id, name, module_start_time, module_end_time FROM modules");
$stmtModules->execute();
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);


// Check if a date is already selected from a previous submission
$selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : (isset($_SESSION['selectedDate']) ? $_SESSION['selectedDate'] : '');
$selectedModule = isset($_POST['selectedModule']) ? $_POST['selectedModule'] : (isset($_SESSION['selectedModule']) ? $_SESSION['selectedModule'] : '');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : '';
    $selectedModule = isset($_POST['selectedModule']) ? $_POST['selectedModule'] : '';

    // Store selected values in session variables
    $_SESSION['selectedDate'] = $selectedDate;
    $_SESSION['selectedModule'] = $selectedModule;

    // Validate and process form data (add your own validation and query logic)
    if (!empty($selectedDate)) {
        // Fetch modules for the selected date
        $stmtModulesForDate = $pdo->prepare("SELECT id, name, module_start_time, module_end_time FROM modules WHERE module_date = :selectedDate");
        $stmtModulesForDate->bindParam(':selectedDate', $selectedDate);
        $stmtModulesForDate->execute();
        $modulesForDate = $stmtModulesForDate->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch student attendance for the selected module
    if (!empty($selectedModule)) {
        $stmtAttendance = $pdo->prepare("SELECT students.name AS student_name, modules.name AS module_name, attendance.attendance_date, attendance.attendance_status
                                        FROM attendance
                                        JOIN students ON attendance.student_id = students.id
                                        JOIN modules ON attendance.module_id = modules.id
                                        WHERE attendance.module_id = :selectedModule
                                        ORDER BY students.name ASC");
        $stmtAttendance->bindParam(':selectedModule', $selectedModule);
        $stmtAttendance->execute();
        $attendanceRecords = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table th, .table td {
            text-align: center;
        }

        .course-management-container {
            margin-top: 20px;
        }

        .course-management-container ul {
            list-style: none;
            padding: 0;
        }

        .course-management-container li {
            margin-bottom: 10px;
        }

        .course-management-container a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .course-management-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>

        <form method="post" action="">
            <div class="form-group">
                <label for="selectedDate">Select Date:</label>
                <input type="date" class="form-control" id="selectedDate" name="selectedDate" value="<?php echo $selectedDate; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Show Modules</button>
        </form>

        <!-- Modules for Selected Date -->
        <?php if (isset($modulesForDate)): ?>
            <div class="course-management-container">
                <h3>Modules for Selected Date</h3>
                <?php if (count($modulesForDate) > 0): ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="selectedModule">Select Module:</label>
                            <select class="form-control" id="selectedModule" name="selectedModule" required>
                                <option value="" selected disabled>Select a Module</option>
                                <?php foreach ($modulesForDate as $module): ?>
                                    <option value="<?php echo $module['id']; ?>" <?php echo ($selectedModule == $module['id']) ? 'selected' : ''; ?>><?php echo $module['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">View Attendance</button>
                    </form>
                <?php else: ?>
                    <p>No modules available for the selected date.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Attendance Records for Selected Module -->
        <?php if (isset($attendanceRecords)): ?>
            <div class="course-management-container">
                <h3>Attendance Records for Selected Module</h3>
                <?php if (count($attendanceRecords) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Module Name</th>
                                <th>Date</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceRecords as $record): ?>
                                <tr>
                                    <td><?php echo $record['student_name']; ?></td>
                                    <td><?php echo $record['module_name']; ?></td>
                                    <td><?php echo $record['attendance_date']; ?></td>
                                    <td><?php echo $record['attendance_status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No attendance records available for the selected module.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-container course-management-container">
        <h2>Course Management</h2>
        <ul>
            <li><a href="admin_view_attendance.php">View Attendance</a></li>
            <li><a href="admin_timetable.php">Timetable</a></li>
            <li><a href="add_module.php">Add Module</a></li>
            <li><a href="edit_module.php">Edit Module</a></li>
            <li><a href="delete_module.php">Delete Module</a></li>
        </ul>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
