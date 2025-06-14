<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'index.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header("Location: index.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Penjual - BidQuity</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f7fa;
    }
    .sidebar {
      width: 250px;
      min-width: 250px;
      background-color: #ffffff;
      border-right: 1px solid #e0e0e0;
      display: flex;
      flex-direction: column;
    }
    .sidebar h5 {
        color: #4b2e2e; 
    }

    .sidebar .nav-link.active {
      background-color: #eef0ff;
      color: #966e3d;
      font-weight: 600;
    }
    .sidebar .nav-link {
      color: #333;
      padding: 10px 15px;
    }
    .sidebar .nav-link:hover {
      background-color: #f0f0f0;
    }
    .sidebar .logout {
      margin-top: auto;
    }
    .card-stat {
      border-radius: 1rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
  </style>
</head>
