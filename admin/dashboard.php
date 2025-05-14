<?php

session_start();
include('include/config.php');
if(strlen($_SESSION['alogin']) == 0) {	
    header('location:index.php');
}
else {
    date_default_timezone_set('Asia/Kolkata'); // change according to timezone
    $currentTime = date('d-m-Y h:i:s A', time());
// Dashboard Data
$get_users = "SELECT COUNT(*) AS total_users FROM users";
$run_users = mysqli_query($con, $get_users);
$row_users = mysqli_fetch_assoc($run_users);
$count_users = $row_users['total_users'];

$get_logs = "SELECT COUNT(*) AS total_logs FROM logs";
$run_logs = mysqli_query($con, $get_logs);
$row_logs = mysqli_fetch_assoc($run_logs);
$count_logs = $row_logs['total_logs'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href= "images/face-recognition.jpg">
	<title>Admin| Dashboard</title>
    <link type="text/css" href="css/style.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/bootstrap.min.css" rel="stylesheet">
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
    background-color: #2b3b4c; /* Slightly darker background color */
    
   
}

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

            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include('include/sidebar.php'); ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="content">
                    <div class="module">
                        <div class="module-head">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Dashboard</h3>
                                </div>
                            </div>
                        </div>

                        <div class="module-body">
                            <div class="row">

                                <!-- Users Panel -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="panel panel-green">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-user fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?php echo $count_users; ?></div>
                                                    <div>Registered Users</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="manage-users.php">
                                            <div class="panel-footer">
                                                <span class="pull-left">View Details</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- User Logs Panel -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="panel panel-red">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-file-text fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?php echo $count_logs; ?></div>
                                                    <div>Activity Logs</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="manage-logs.php">
                                            <div class="panel-footer">
                                                <span class="pull-left">View Details</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div> <!-- End Row -->
                        </div> <!-- End Module Body -->

                    </div> <!-- End Module -->
                </div> <!-- End Content -->
            </div> <!-- End Main Content -->

        </div> <!-- End Row -->
    </div> <!-- End Container -->
</div> <!-- End Wrapper -->

<?php include('include/footer.php'); ?>

     
	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>

</html>
<?php
}  // <-- This is the closing brace for "else" block
?>
