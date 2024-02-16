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

// Fetch modules for the filter dropdown
$stmtModules = $pdo->prepare("SELECT id, name FROM modules");
$stmtModules->execute();
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);

// Fetch students for the filter dropdown
$stmtStudents = $pdo->prepare("SELECT id, name FROM students");
$stmtStudents->execute();
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to filter attendance records
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleId = isset($_POST['module_id']) ? $_POST['module_id'] : null;
    $studentId = isset($_POST['student_id']) ? $_POST['student_id'] : null;

    // Fetch attendance records based on the selected module and student
    $attendanceRecords = getAttendanceRecords($moduleId, $studentId);
} else {
    // Fetch all attendance records if no filter is applied
    $attendanceRecords = getAttendanceRecords(null, null);
}

// Function to get attendance records based on module and student
function getAttendanceRecords($moduleId, $studentId) {
    global $pdo;

    // Modify this query based on your database structure
    $sql = "SELECT attendance.*, students.name AS student_name, modules.name AS module_name
            FROM attendance
            INNER JOIN students ON attendance.student_id = students.id
            INNER JOIN modules ON attendance.module_id = modules.id";

    if ($moduleId !== null || $studentId !== null) {
        $sql .= " WHERE";

        if ($moduleId !== null) {
            $sql .= " modules.id = :moduleId";
        }

        if ($moduleId !== null && $studentId !== null) {
            $sql .= " AND";
        }

        if ($studentId !== null) {
            $sql .= " students.id = :studentId";
        }
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);

    if ($moduleId !== null) {
        $stmt->bindParam(':moduleId', $moduleId);
    }

    if ($studentId !== null) {
        $stmt->bindParam(':studentId', $studentId);
    }

    $stmt->execute();

    // Fetch the attendance records
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Attendance - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Admin View Attendance</h2>

    <!-- Filter form -->
    <form method="post" action="">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="moduleSelect">Select Module:</label>
                <select class="form-control" id="moduleSelect" name="module_id">
                    <option value="">All Modules</option>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo $module['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="studentSelect">Select Student:</label>
                <select class="form-control" id="studentSelect" name="student_id">
                    <option value="">All Students</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <?php if (count($attendanceRecords) > 0): ?>
        <!-- Display attendance records -->
        <table class="table mt-3">
            <thead>
            <tr>
                <th>Student Name</th>
                <th>Module Name</th>
                <th>Attendance Date</th>
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
        <p class="text-center">No attendance records found.</p>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
