<?php
session_start();
include('include/config.php');

if(strlen($_SESSION['alogin'])==0) {    
    header('location:index.php');
    exit();
} else {
    $uid = intval($_GET['id']); // Get user ID

    // Handle form submission
    if(isset($_POST['submit'])) {
        $fullname = $_POST['fullname'];
        $personNumber = $_POST['personNumber'];
        $date_of_birth = $_POST['date_of_birth'];
        $adresse = $_POST['adresse'];
        $email = $_POST['email'];
        $politi_status = $_POST['politi_status'];

        // Update user details
        $sql = mysqli_query($con, "UPDATE users SET fullname='$fullname', personNumber='$personNumber', date_of_birth='$date_of_birth', adresse='$adresse', email='$email', politi_status='$politi_status' WHERE id='$uid' ");
        $_SESSION['msg'] = "User Updated Successfully !!";
    }

    // Fetch user details
    $query = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
    $row = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href= "images/face-recognition.jpg">
    <title>Admin | Edit User</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    
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
    <?php include('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>              
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Edit User</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg']) && $_SESSION['msg'] != "") { ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Well done!</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                                        <?php $_SESSION['msg']=""; ?>
                                    </div>
                                <?php } ?>

                                <br />

                                <div class="form-container">
                                    <form class="form-horizontal row-fluid" name="edituser" method="post">
                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">User Image</label>
                                            <div class="controls">
                                                <?php 
                                                    $image_path = "profilimages/$uid/" . htmlentities($row['user_profil_image']);
                                                    
                                                    // Check if file exists
                                                    if (!empty($row['user_profil_image']) && file_exists($image_path)) {
                                                        echo '<img src="'.$image_path.'" width="200" height="200"> ';
                                                    } else {
                                                        echo '<img src="images/default-avatar.png" width="200" height="200">';
                                                    }
                                                ?>
                                                <a href="update-image1.php?id=<?php echo $row['id'];?>">Change Image</a>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Full Name</label>
                                            <div class="controls">
                                                <input type="text" name="fullname" value="<?php echo htmlentities($row['fullname']); ?>" class="span8" readonly>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Person Number</label>
                                            <div class="controls">
                                                <input type="text" name="personNumber" value="<?php echo htmlentities($row['personNumber']); ?>" class="span8" readonly>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Date of Birth</label>
                                            <div class="controls">
                                                <input type="date" name="date_of_birth" value="<?php echo htmlentities($row['date_of_birth']); ?>" class="span8" required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Address</label>
                                            <div class="controls">
                                                <input type="text" name="adresse" value="<?php echo htmlentities($row['adresse']); ?>" class="span8" required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Email</label>
                                            <div class="controls">
                                                <input type="email" name="email" value="<?php echo htmlentities($row['email']); ?>" class="span8" required>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="basicinput">Police Status</label>
                                            <div class="controls">
                                                <select name="politi_status" class="span8">
                                                    <option value="<?php echo htmlentities($row['politi_status']); ?>">
                                                        <?php echo htmlentities($row['politi_status']); ?>
                                                    </option>
                                                    <option value="">Ingen-merkning</option>
                                                    <option value="Bortvist">Bortvist</option>
                                                    <option value="Stengt">Stengt</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                                <a href="manage-users.php" class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>                        
                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
<?php } ?>
