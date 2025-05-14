<?php
session_start();
error_reporting(0);
include('include/config.php');

if (isset($_POST['change'])) {
    $id = trim($_POST['id']);
    $adminname = trim($_POST['adminname']);
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);

    // Check if the passwords match
    if ($password !== $confirmpassword) {
        $_SESSION['errmsg'] = "Passwords do not match!";
        header("Location: forgot-password.php");
        exit();
    }

    // Enforce a stronger password policy if needed
    if (strlen($password) < 8) {
        $_SESSION['errmsg'] = "Password must be at least 8 characters long!";
        header("Location: forgot-password.php");
        exit();
    }

    // Hash the password (using md5 for backward compatibility, but `password_hash()` is recommended)
    $hashed_password = md5($password);
    $updationDate = date("Y-m-d H:i:s");  // Current timestamp for updationDate

    // Use prepared statements to avoid SQL injection
    $stmt = $con->prepare("SELECT * FROM admin WHERE id=? AND username=?");
    $stmt->bind_param("ss", $id, $adminname);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();

    if ($admin_data) {
        $update_stmt = $con->prepare("UPDATE admin SET password=?, updationDate=? WHERE id=? AND username=?");
        $update_stmt->bind_param("ssss", $hashed_password, $updationDate, $id, $adminname);
        if ($update_stmt->execute()) {
            $_SESSION['successmsg'] = "Password changed successfully!";
            header("Location: forgot-password.php");
            exit();
        } else {
            $_SESSION['errmsg'] = "Error updating password. Please try again!";
            header("Location: forgot-password.php");
            exit();
        }
    } else {
        $_SESSION['errmsg'] = "Invalid admin ID or admin name!";
        header("Location: forgot-password.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Portal | Admin login</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    
   
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
/* Styling for <i> and <a> elements */
.sidebar a, .sidebar i {
    color: white !important; /* Ensures the text color is white */
}

.sidebar a:hover, .sidebar i:hover {
    color: #ccc!important; /* Light brown color on hover (use any shade you prefer) */
}
</style>
<style>
/* Navbar styling */

/* Hover effect */
.nav.pull-right li a:hover {
    background-color: #3b141c; /* Slightly darker background color */
    
   
}
.navbar .navbar-inner {
    background-color: #3b141c; /* Ensure inner part is consistent */
}

.navbar .brand {
    color: white !important;
    font-weight: bold;
    font-size: 18px;
    padding-left: 10px;
    text-transform: uppercase;
}

.navbar .btn-navbar {
    color: white !important;
    font-size: 20px;
    padding: 10px;
}

.navbar .nav > li > a {
    color: white !important;
    
    text-decoration: none;
}

</style>

<style>
   
/* Ensure the navbar is always visible */
.navbar .nav-collapse {
    display: flex !important; /* Ensures nav items are shown */
    justify-content: flex-end; /* Align nav items to the right */
}

.navbar .nav {
    display: flex; /* Keeps nav items in a row */
    align-items: center;
}

.navbar .nav-user.dropdown {
    position: relative;
}

.navbar .dropdown-toggle {
    display: flex;
    align-items: center;
    color: white !important;
    text-decoration: none;
}

.navbar .nav-avatar {
    width: 40px; /* Size of avatar */
    height: 40px;
    border-radius: 50%; /* Circular avatar */
    margin-right: 8px;
}

.navbar .dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0; /* Align dropdown to the right */
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    min-width: 200px;
    display: none; /* Initially hidden */
    z-index: 999;
}

.navbar .dropdown:hover .dropdown-menu,
.navbar .dropdown-menu:hover {
    display: block !important; /* Show the dropdown on hover */
}

.navbar .dropdown-menu a {
    color: #333;
    padding: 10px 15px;
    display: block;
    text-decoration: none;
}

.navbar .dropdown-menu a:hover {
    background-color: #f1f1f1;
    color: #333;
}

.navbar .divider {
    height: 1px;
    margin: 5px 0;
    background-color:#e5e5e5;
}
</style>
<style>

.panel-heading .panel-title {
    font-size: 20px; /* Optional: adjust the font size */
    font-weight: bold;
    margin: 0; /* Ensures no extra margin */
}

.panel-heading i {
    margin-right: 8px; /* Adds some space between the icon and the text */
}
</style>

</head>
<body>
<div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <i class="icon-reorder shaded"></i>
                </a>

                <a class="brand" href="index.html">
                    Shopping Portal | Admin
                </a>

                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-right">
                        <li><a href="http://localhost/Mix-Go'Biten/">Back to Portal</a></li>
                    </ul>
                </div><!-- /.nav-collapse -->
            </div>
        </div><!-- /navbar-inner -->
    </div><!-- /navbar -->

<div class="container">
    
        <div class="row">
        <a href="index.php">Back to login</a>
            
                <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="module module-login span4 offset4" style="margin-top: -20px;">
                    
                        <div class="module-head" >
                            <h3>Back Up Your Password</h3>
                        </div>
                <form class="register-form outer-top-xs" method="post">
                    
                    <div class="module-body">
                        <!-- Display the message -->
                        <?php if (isset($_SESSION['errmsg']) && !empty($_SESSION['errmsg'])): ?>
    <div class="alert alert-danger text-center">
        <?php echo $_SESSION['errmsg']; ?>
    </div>
    <?php unset($_SESSION['errmsg']); // Clear the message after displaying ?>
<?php elseif (isset($_SESSION['successmsg']) && !empty($_SESSION['successmsg'])): ?>
    <div class="alert alert-success text-center">
        <?php echo $_SESSION['successmsg']; ?>
    </div>
    <?php unset($_SESSION['successmsg']); // Clear the message after displaying ?>
<?php endif; ?>

                            <div class="control-group">
                                <div class="controls row-fluid">
                        <label class="info-title" for="adminID">Admin ID <span>*</span></label>
                        <input type="text" name="id" class="form-control unicase-form-control text-input" required>
                    </div>
                    <div class="module-body">
                            <div class="control-group">
                                <div class="controls row-fluid">
                        <label class="info-title" for="adminname">Admin Name<span>*</span></label>
                        <input type="text" name="adminname" class="form-control unicase-form-control text-input" required>
                    </div>
                    <div class="module-body">
                            <div class="control-group">
                                <div class="controls row-fluid">
                        <label class="info-title" for="password">Password<span>*</span></label>
                        <input type="password" name="password" class="form-control unicase-form-control text-input" required>
                    </div>
                    <div class="module-body">
                            <div class="control-group">
                                <div class="controls row-fluid">
                        <label class="info-title" for="confirmpassword">Confirm Password<span>*</span></label>
                        <input type="password" name="confirmpassword" class="form-control unicase-form-control text-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right" name="change">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
