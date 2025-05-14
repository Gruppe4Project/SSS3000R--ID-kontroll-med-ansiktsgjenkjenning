<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit; // Added exit to stop further script execution after header redirection
} else {
    date_default_timezone_set('Asia/Kolkata'); // change according timezone
    $currentTime = date('d-m-Y h:i:s A', time());

    if (isset($_GET['del']) && isset($_GET['id'])) {
        // Sanitize the input to avoid SQL injection
        $id = mysqli_real_escape_string($con, $_GET['id']);
        
        // Check if ID exists before deletion to avoid errors
        $query = mysqli_query($con, "SELECT * FROM users WHERE id = '$id'");
        if (mysqli_num_rows($query) > 0) {
            mysqli_query($con, "DELETE FROM users WHERE id = '$id'");
            $_SESSION['delmsg'] = "User deleted !!";
        } else {
            $_SESSION['delmsg'] = "No such user found!";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href= "images/face-recognition.jpg">
    <title>Admin | Manage Users</title>
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
                <?php include('include/sidebar.php');?>                
                <div class="span9">
                    <div class="content">

                        <div class="module">
                            <div class="module-head">
                                <h3>Manage Users</h3>
                            </div>
                           <div class="module-body table" >

                                <?php if (isset($_SESSION['delmsg'])) { ?>
                                    <div class="alert alert-error">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                        <?php $_SESSION['delmsg'] = ""; ?>
                                    </div>
                                <?php } ?>

                                <br />
<div class="table-responsive">
    <table class="datatable-1 table table-bordered table-striped display">
        <thead>
            <tr>
                <th>#</th>
                <th>Person Number</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>Address</th>
                <th>Email</th>
                <th>Reg. Date</th>
                <th>Police Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $query = mysqli_query($con, "SELECT * FROM users");
            $cnt = 1;
            while ($row = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td><?php echo htmlentities($cnt); ?></td>
                    <td><?php echo htmlentities($row['personNumber']); ?></td>
                    <td><?php echo htmlentities($row['fullname']); ?></td>
                    <td><?php echo htmlentities($row['date_of_birth']); ?></td>
                    <td><?php echo htmlentities($row['adresse']); ?></td>
                    <td><?php echo htmlentities($row['email']); ?></td>
                    <td><?php echo htmlentities($row['created_at']); ?></td>
                    <td><?php echo htmlentities($row['politi_status']); ?></td>
                    <td>
                        <a href="edit-user.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="manage-users.php?id=<?php echo $row['id']; ?>&del=delete" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete?');">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php $cnt++; } ?>
        </tbody>
    </table>
</div>

                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include('include/footer.php');?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-1').dataTable();
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
            $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
            $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
        });
    </script>
</body>
<?php } ?>
