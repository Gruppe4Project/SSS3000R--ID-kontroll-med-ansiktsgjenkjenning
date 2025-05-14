<?php
session_start();
error_reporting(0);
include("include/config.php");

if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashed_password = md5($password);
    
    // Use prepared statement to prevent SQL injection
    $stmt = $con->prepare("SELECT * FROM admin WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();
    $num = $result->num_rows;
    
    if($num > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['alogin'] = $username;
        $_SESSION['id'] = $row['id'];
        $extra = "dashboard.php";
        header("location: $extra");
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
        $extra = "index.php";
        header("location: $extra");
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
    <title> ID-Kontroll | Admin</title>
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
    background-color: #2b3b4c; /* Ensure inner part is consistent */
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

/* Hover effect */
.nav.pull-right li a:hover {
    background-color: #2b3b4c; /* Slightly darker background color */
    
   
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

                <a class="brand" href="index.php">
                ID-Kontroll | Admin
                </a>

                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-right"><li>
  <a href="">
    <img src="images/face-recognition.jpg" class="nav-avatar" style="width: 50px; height: 50px;" />
  </a>
</li>

                    </ul>
                </div><!-- /.nav-collapse -->
            </div>
        </div><!-- /navbar-inner -->
    </div><!-- /navbar -->

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="module module-login span4 offset4">
                    <form class="form-vertical" method="post">
                        <div class="module-head">
                            <h3>Sign In</h3>
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

                            <div class="control-group">
                                <div class="controls row-fluid">
                                    <input class="span12" type="text" id="inputEmail" name="username" placeholder="Username">
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls row-fluid">
                                    <input class="span12" type="password" id="inputPassword" name="password" placeholder="Password">
                                </div>
                            </div>
                        </div>
                        <div class="module-foot">
                            <div class="control-group">
                                <div class="controls clearfix">
                                
                                    <button type="submit" class="btn btn-primary pull-right" name="submit">Login</button>
                                    
                                </div>
                                
		 
                            </div>
                        </div>
                        <div class="radio outer-xs">
                        <a href="forgot-password.php" class="forgot-password pull-right" style="margin-right: 10px;">Forgot your Password? </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!--/.wrapper-->
    

    <div class="footer">
        <div class="container">
        <b class="copyright">&copy; 2025 ID-Kontroll Admin Portal </b> All rights reserved.
        </div>
    </div>
    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
