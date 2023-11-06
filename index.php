<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title class="title">Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        body{ font: 14px sans-serif; text-align: center; }
        
        .btn:hover {
            /* font-size: 0.875rem; */
            line-height: 1;
            font-weight: 400;
            padding: .7rem 1.5rem;
            border-radius: 0.1275rem
        }
    </style>
</head>
<body>
    <h1 id="pagetitle" class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. Welcome to Grocery Shopper!</h1>
    <p>
        <a href="myLists.php" class="btn btn-primary">My Lists</a>
    </p>
    <p>
        <a href="settings.php" class="btn btn-primary">Settings</a>
    </p>
    <p>
        <a href="logout.php" class="btn btn-primary">Sign Out of Your Account</a>
    </p>
</body>
</html>
