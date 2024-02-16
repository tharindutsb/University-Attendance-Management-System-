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
    // Handle form submission to delete the selected module
    $moduleId = $_POST['selectedModule'];

    // Delete the module from the 'modules' table
    $stmt = $pdo->prepare("DELETE FROM modules WHERE id = :moduleId");
    $stmt->bindParam(':moduleId', $moduleId);
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

    <!-- HTML form for selecting a module to delete -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Module - University Attendance Management System</title>
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
        <h2>Delete Module</h2>

        <form method="post" action="">
            <div class="form-group">
                <label for="selectedModule">Select Module:</label>
                <select class="form-control" id="selectedModule" name="selectedModule" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo $module['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-danger">Delete Module</button>
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
