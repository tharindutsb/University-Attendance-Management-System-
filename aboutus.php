<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - University Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-member img {
            width: 150px; 
            height: 150px; 
            border-radius: 50%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <h2 class="text-center">About Us</h2>

    <div class="team-member">
        <img src="Assests\images\tharindu_photo.jpg" alt="Tharindu">
        <!-- set you dp here  -->

        <h4>Tharindu</h4>
        <p>Role: Developer, Designer</p>
    </div>

    <div class="team-member">
        <img src="tharindu_photo.jpg" alt="Tharindu">
        <h4>Pamindu</h4>
        <p>Role: Tester</p>
    </div>

    <div class="team-member">
        <img src="tharindu_photo.jpg" alt="Tharindu">
        <h4>Gaveesha</h4>
        <p>Role: Tester</p>
    </div>

    <div class="team-member">
        <img src="tharindu_photo.jpg" alt="Tharindu">
        <h4>Sisara</h4>
        <p>Role: Tester</p>
    </div>

    <div class="team-member">
        <img src="tharindu_photo.jpg" alt="Tharindu">
        <h4>Shakuna</h4>
        <p>Role: Tester</p>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
