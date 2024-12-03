<?php
require_once './core/dbConfig.php';
require_once './core/models.php';
require_once 'core/handleForms.php'; // Added this line to include handleForms.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
 
        'job_title' => $_POST['job_title'],
        'skills' => $_POST['skills'],
        'status' => $_POST['status'],
        'added_by' => 'Admin'
    ];
    $result = insertApplicant($pdo, $data);
    echo "<script>alert('" . $result['message'] . "'); window.location = 'index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Applicant</title>
    <link rel="stylesheet" href="styles.css"> <!-- Added stylesheet link -->
</head>
<body>
    <h1>Add New Applicant</h1>
    <form method="POST" action="">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <textarea name="address" placeholder="Address" required></textarea>
        <input type="text" name="job_title" placeholder="Job Title" required>
        <textarea name="skills" placeholder="Skills (e.g., PHP, SQL, JavaScript)" required></textarea>
        <select name="status">
            <option value="Pending">Pending</option>
            <option value="Shortlisted">Shortlisted</option>
            <option value="Rejected">Rejected</option>
            <option value="Hired">Hired</option>
        </select>
        <button type="submit">Add Applicant</button>
    </form>

    <!-- Back button form -->
    <form action="index.php" method="POST">
        <button type="submit">Back</button>
    </form>
</body>
</html>
