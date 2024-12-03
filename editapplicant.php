<?php
require_once './core/dbConfig.php';
require_once './core/models.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid request.");
}

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
    $result = updateApplicant($pdo, $id, $data);
    echo "<script>alert('" . $result['message'] . "'); window.location = 'index.php';</script>";
}

$applicant = fetchApplicants($pdo, null)['querySet'][0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
</head>
<body>
    <h1>Edit Applicant</h1>
    <form method="POST" action="">
        <input type="text" name="first_name" value="<?= $applicant['first_name'] ?>" required>
        <input type="text" name="last_name" value="<?= $applicant['last_name'] ?>" required>
        <input type="email" name="email" value="<?= $applicant['email'] ?>" required>
        <input type="text" name="phone" value="<?= $applicant['phone'] ?>" required>
        <textarea name="address" required><?= $applicant['address'] ?></textarea>
        <input type="text" name="job_title" value="<?= $applicant['job_title'] ?>" required>
        <textarea name="skills" required><?= $applicant['skills'] ?></textarea>
        <select name="status">
            <option <?= $applicant['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option <?= $applicant['status'] === 'Shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
            <option <?= $applicant['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            <option <?= $applicant['status'] === 'Hired' ? 'selected' : '' ?>>Hired</option>
        </select>
        <button type="submit">Update</button>
    </form>
</body>
</html>