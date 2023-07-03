<?php
session_start();
if (!isset($_SESSION['managerId'])) {
    header('location:login.php');
    exit; // Add an exit statement after redirecting
}

require 'assets/autoloader.php';
require 'assets/db.php';
require 'assets/function.php';

if (isset($_GET['delete'])) {
    if ($con->query("DELETE FROM useraccounts WHERE id = '$_GET[id]'")) {
        header("location:mindex.php");
        exit; // Add an exit statement after redirecting
    }
}

$row = null; // Initialize the $row variable

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM useraccounts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if (isset($_POST['send'])) {
    $notice = $_POST['notice'];
    $userId = $_POST['userId'];

    $stmt = $con->prepare("INSERT INTO notice (notice, userId) VALUES (?, ?)");
    $stmt->bind_param("si", $notice, $userId);
    if ($stmt->execute()) {
        $success_message = "Notice successfully sent.";
    } else {
        $error_message = "Failed to send the notice. Error: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Banking</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body style="background:#96D678;background-size: 100%">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">
        <img src="images/logo.jpeg" width="100" height="60" class="d-inline-block align-top" alt=""
             style="border-radius:25px">
        <!--  <i class="d-inline-block  fa fa-building fa-fw"></i> <?php echo bankName; ?>-->
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item ">
                <a class="nav-link active" href="mindex.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="maccounts.php">Accounts</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="maddnew.php">Add New Account</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="mfeedback.php">Feedback</a>
            </li>
        </ul>
        <?php include 'msideButton.php'; ?>
    </div>
</nav>
<br><br><br>
<div class="container">
    <div class="card w-100 text-center shadowBlue">
        <div class="card-header">
            Send Notice to <?php echo isset($row['name']) ?
$row['name'] : ''; ?>
</div>
<div class="card-body">
    <?php if (isset($success_message)) : ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="alert alert-success w-50 mx-auto">
            <h5>Write notice for <?php echo isset($row['name']) ? $row['name'] : ''; ?></h5>
            <input type="hidden" name="userId"
                   value="<?php echo isset($row['id']) ? $row['id'] : ''; ?>">
            <textarea class="form-control" name="notice" required
                      placeholder="Write your message"></textarea>
            <button type="submit" name="send" class="btn btn-primary btn-block btn-sm my-1">Send</button>
        </div>
    </form>
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
