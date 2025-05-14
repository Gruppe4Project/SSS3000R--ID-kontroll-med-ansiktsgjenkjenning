<?php
session_start();
error_reporting(E_ALL); // Show all errors for debugging
include('include/config.php');

// Code for Admin Registration
if (isset($_POST['submit'])) {
    // Get input values and sanitize them to prevent SQL injection
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password']; // Raw password
    $confirmpassword = $_POST['confirmpassword']; // Confirmed password

    // Check if the passwords match
    if ($password !== $confirmpassword) {
        $_SESSION['errmsg'] = "Passwords do not match!";
        header("Location: add-admin.php");
        exit();
    }

    // Enforce stronger password policy
    if (strlen($password) < 8) {
        $_SESSION['errmsg'] = "Password must be at least 8 characters long!";
        header("Location: add-admin.php");
        exit();
    }

    // Hash the password before saving (use password_hash() for better security in production)
    $hashed_password = md5($password); 

    // Ensure that the username does not already exist
    $query_check = mysqli_query($con, "SELECT * FROM admin WHERE username = '$username'");
    if (mysqli_num_rows($query_check) > 0) {
        $_SESSION['errmsg'] = "Username already exists!";
        header("Location: add-admin.php");
        exit();
    }

    // Insert into the database
    $query = mysqli_query($con, "INSERT INTO admin (username, password) VALUES ('$username', '$hashed_password')");

    // Check if the insertion was successful
    if ($query) {
        $_SESSION['successmsg'] = "You are successfully registered as an Admin!";
        header("Location: add-admin.php"); // Redirect after success
        exit();
    } else {
        $_SESSION['errmsg'] = "Registration failed. Something went wrong. Please try again!";
        header("Location: add-admin.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href= "images/face-recognition.jpg">
	<title>Add New| Admin</title>
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


.navbar .navbar-inner {
    background-color: #3b141c; /* Ensure inner part is consistent */
}
/* Hover effect */
.nav.pull-right li a:hover {
    background-color: #3b141c; /* Slightly darker background color */
    color:aqua;
   
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
<style>
    
/* Ensure the main container expands */
.container {
    width: 90% !important;
    max-width: 100% !important;
}
/* Ensure the content area expands */
.content {
    width: 100% !important;
    max-width: 100% !important;
}

/* Fix for .span9 if it's restricting width */
.span9 {
    width: 75% !important;
    max-width: 100% !important;
    flex-grow: 1; /* Ensures it stretches to available space */
}



/* Ensure the wrapper takes full width */
.wrapper {
    width: 100% !important;
    max-width: 100% !important;
}


</style>
</head>

<body>
    <?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php');?>                

                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Add New Admin</h3>
                            </div>
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

                                <form class="register-form outer-top-xs" method="post" name="register" onSubmit="return valid();">
                                    <div class="form-group">
                                        <label class="info-title" for="username">Username<span>*</span></label>
                                        <input type="text" id="username" name="username" class="form-control unicase-form-control text-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="info-title" for="password">Password<span>*</span></label>
                                        <input type="password" name="password" id="password" class="form-control unicase-form-control text-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="info-title" for="confirmpassword">Confirm Password<span>*</span></label>
                                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control unicase-form-control text-input" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="submit">Add Admin</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('include/footer.php');?>
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
