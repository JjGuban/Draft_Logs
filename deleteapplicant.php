<?php
require_once './core/dbConfig.php';
require_once './core/models.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = deleteApplicant($pdo, $id);
    echo "<script>alert('" . $result['message'] . "'); window.location = 'index.php';</script>";
}
?>