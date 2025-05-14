<?php
session_start();
include('include/config.php');

// Check if user is logged in, otherwise redirect to login page
if (strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Kolkata'); // Set timezone
    $currentTime = date('d-m-Y h:i:s A', time());

    if (isset($_POST['submit'])) {
        $newPassword = $_POST['newpassword'];
        $confirmPassword = $_POST['confirmpassword'];

        // Server-side validation for password length
        if (strlen($newPassword) < 8) {
            $_SESSION['errmsg'] = "New Password must be at least 8 characters long!";
        } else if ($newPassword !== $confirmPassword) {
            $_SESSION['errmsg'] = "New Password and Confirm Password do not match!";
        } else {
            // Validate the current password
            $sql = mysqli_query($con, "SELECT password FROM admin WHERE password='" . md5($_POST['password']) . "' AND username='" . $_SESSION['alogin'] . "'");
            $num = mysqli_fetch_array($sql);
            
            if ($num > 0) {
                // Password is correct, proceed with updating the password
                $update_sql = mysqli_query($con, "UPDATE admin SET password='" . md5($newPassword) . "', updationDate='$currentTime' WHERE username='" . $_SESSION['alogin'] . "'");
                
                // Set session message
                $_SESSION['successmsg'] = "Password Changed Successfully!";
                // For now, avoid redirection to see if the layout issue persists
                // header('Location: ' . $_SERVER['PHP_SELF']);
                // exit();
            } else {
                // Old password does not match
                $_SESSION['errmsg'] = "Old Password does not match!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href= "images/face-recognition.jpg">
    <title>Admin| Change Password</title>
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
<?php include('include/header.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php'); ?>
            <div class="span9">
                <div class="content">

                    <div class="module">
                        <div class="module-head">
                            <h3>Admin Change Password</h3>
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

                            <!-- Form to change password -->
                            <form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Current Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Enter your current Password" name="password" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">New Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Enter your new Password" name="newpassword" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Confirm Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Enter your new Password again" name="confirmpassword" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submit" class="btn" style="color:white;background:green;">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php'); ?>

<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>
</html>
