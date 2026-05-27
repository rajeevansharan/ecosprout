<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Check if staff is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - EcoSprout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .navbar-staff {
            background-color: #2e7d32; /* Beautiful nature dark green */
        }
        .navbar-staff .nav-link {
            color: rgba(255, 255, 255, 0.85);
        }
        .navbar-staff .nav-link:hover {
            color: #ffffff;
        }
        .navbar-staff .navbar-brand {
            color: #ffffff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-staff mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">🌱 Staff Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#staffNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="staffNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="plants.php">Manage Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="inquiries.php">Customer Inquiries</a></li>
                    <li class="nav-item"><a class="nav-link" href="workshops.php">Workshop Schedules</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Gardening Services</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-light fw-bold" href="../index.php" target="_blank">View Website</a></li>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container pb-5">
