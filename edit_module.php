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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedModule'])) {
    // Handle form submission to edit an existing module
    $moduleId = $_POST['selectedModule'];

    // Fetch the details of the selected module
    $stmt = $pdo->prepare("SELECT id, name, module_date, module_start_time,module_end_time FROM modules WHERE id = :moduleId");
    $stmt->bindParam(':moduleId', $moduleId);
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$module) {
        echo '<p>Module not found</p>';
    } else {
        // Display the form for editing the selected module
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Module - University Attendance Management System</title>
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
            <h2>Edit Module</h2>

            <form method="post" action="">
                <input type="hidden" name="module_id" value="<?php echo $module['id']; ?>">

                <div class="form-group">
                    <label for="moduleName">Module Name:</label>
                    <input type="text" class="form-control" id="moduleName" name="module_name" value="<?php echo $module['name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="moduleDate">Module Date:</label>
                    <input type="date" class="form-control" id="moduleDate" name="module_date" value="<?php echo $module['module_date']; ?>" required>
                </div>

                <div class="form-group">
    <label for="moduleStartTime">Module Start Time:</label>
    <input type="time" class="form-control" id="moduleStartTime" name="module_Start_time" value="<?php echo date('H:i', strtotime($module['module_start_time'])); ?>" required>
</div>

<div class="form-group">
    <label for="moduleEndTime">Module End Time:</label>
    <input type="time" class="form-control" id="moduleEndTime" name="module_End_time" value="<?php echo date('H:i', strtotime($module['module_end_time'])); ?>" required>
</div>




                <button type="submit" class="btn btn-primary">Update Module</button>
            </form>
        </div>

        <?php include('includes/footer.php'); ?>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['module_id'])) {
// Handle form submission to update an existing module
$moduleId = $_POST['module_id'];
$moduleName = $_POST['module_name'];
$moduleDate = $_POST['module_date'];
$moduleStartTime = date('H:i:s', strtotime($_POST['module_Start_time']));
$moduleEndTime = date('H:i:s', strtotime($_POST['module_End_time']));

// Update the module in the 'modules' table
$stmt = $pdo->prepare("UPDATE modules SET name = :moduleName, module_date = :moduleDate, module_start_time = :moduleStartTime, module_end_time = :moduleEndTime WHERE id = :moduleId");
$stmt->bindParam(':moduleId', $moduleId);
$stmt->bindParam(':moduleName', $moduleName);
$stmt->bindParam(':moduleDate', $moduleDate);
$stmt->bindParam(':moduleStartTime', $moduleStartTime);
$stmt->bindParam(':moduleEndTime', $moduleEndTime);
$stmt->execute();

// Redirect back to admin dashboard or any other page
header('Location: admin_dashboard.php');
exit();

} else {
    // Fetch all modules for selection
    $stmtModules = $pdo->prepare("SELECT id, name FROM modules");
    $stmtModules->execute();
    $modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- HTML form for selecting a module to edit -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="stylesheet" href="css/style.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Module - University Attendance Management System</title>
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
        <h2>Edit Module</h2>

        <form method="post" action="">
            <div class="form-group">
                <label for="selectedModule">Select Module:</label>
                <select class="form-control" id="selectedModule" name="selectedModule" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo $module['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Select Module</button>
        </form>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}

?>
