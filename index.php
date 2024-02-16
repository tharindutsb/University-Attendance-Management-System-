<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials based on user type
    if (isset($_POST['userType']) && $_POST['userType'] === 'student') {
        // Validate student credentials (replace with your actual student credentials)
        $validUsers = [
            'student1' => 'password123',
            'student2' => 'securepass',
        ];
        
        if (isset($validUsers[$username]) && $validUsers[$username] === $password) {
            // Set the correct student ID in the session
            $_SESSION['user_id'] = $username;
        
            // Debugging: Print the session information
            echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';
        
            header('Location: student_dashboard.php');
            exit();
        } else {
            $error = 'Invalid student username or password';
        }
    } elseif (isset($_POST['userType']) && $_POST['userType'] === 'admin') {
        // Validate admin credentials (replace with your actual admin credentials)
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin'] = true;
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = 'Invalid admin username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">University Attendance Management System</h2>
    <p class="lead text-center">Login to access your attendance information.</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="" class="mx-auto" style="max-width: 300px;">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="userType">Login As:</label>
            <select class="form-control" id="userType" name="userType" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <!-- <p class="mt-3 text-center">For demo purposes, use the following credentials:</p>
    <p class="text-center"><strong>Student:</strong> student1 | <strong>Password:</strong> password123</p>
    <p class="text-center"><strong>Admin:</strong>admin | <strong>Password:</strong>admin123</p> -->
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
