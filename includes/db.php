<?php

$host = 'localhost:3380';
$dbname = 'attendance';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

function getCoursesForUser($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT courses.name FROM courses
                           JOIN students ON students.course_id = courses.id
                           WHERE students.university_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAttendanceForUser($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT courses.name AS course_name, attendance.attendance_date FROM attendance
                           JOIN students ON students.id = attendance.student_id
                           JOIN courses ON courses.id = students.course_id
                           WHERE students.university_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
