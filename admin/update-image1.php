<?php
session_start();
include('include/config.php');

if (strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit();
} else {
    $pid = intval($_GET['id']); // User ID

    if (isset($_POST['submit'])) {
        $username = $_POST['fullname'];
        $userimage = $_FILES["user_profil_image"];

        // Ensure the upload directory exists
        $upload_dir = "profilimages/$pid/";
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0775, true)) {
                $_SESSION['msg'] = "Failed to create upload directory!";
                header("Location: ".$_SERVER['PHP_SELF']."?id=$pid");
                exit();
            }
        }

        // Get file extension and validate type
        $imageFileType = strtolower(pathinfo($userimage["name"], PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png"];

        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['msg'] = "Invalid file format! Only JPG, PNG, JPEG allowed.";
        } else {
            // Generate unique filename
            $newFileName = "profile_" . $pid . "_" . time() . "." . $imageFileType;
            $target_file = $upload_dir . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($userimage["tmp_name"], $target_file)) {
                // Update database with the correct image name
                $sql = mysqli_query($con, "UPDATE users SET user_profil_image='$newFileName' WHERE id='$pid'");

                if ($sql) {
                    $_SESSION['msg'] = "User Image Updated Successfully!";
                } else {
                    $_SESSION['msg'] = "Database update failed: " . mysqli_error($con);
                }
            } else {
                $_SESSION['msg'] = "Failed to move uploaded file!";
            }
        }

        header("Location: ".$_SERVER['PHP_SELF']."?id=$pid");
        exit();
    }

    // Fetch user details
    $query = mysqli_query($con, "SELECT fullname, user_profil_image FROM users WHERE id='$pid'");
    $row = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Update User Image</title>
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
                            <h3>Update User Profile Image</h3>
                        </div>
                        <div class="module-body">
                            <?php if(isset($_SESSION['msg']) && $_SESSION['msg'] != "") { ?>
                                <div class="alert alert-warning">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong><?php echo htmlentities($_SESSION['msg']); ?></strong>
                                    <?php $_SESSION['msg']=""; ?>
                                </div>
                            <?php } ?>

                            <br />

                            <form class="form-horizontal row-fluid" method="post" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Full Name</label>
                                    <div class="controls">
                                        <input type="text" name="fullname" value="<?php echo htmlentities($row['fullname']); ?>" class="span8 tip" readonly>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Current Profile Image</label>
                                    <div class="controls">
                                        <?php 
                                            $image_path = "profilimages/$pid/" . htmlentities($row['user_profil_image']);
                                            if (!empty($row['user_profil_image']) && file_exists($image_path)) {
                                                echo '<img src="'.$image_path.'" width="200" height="200">';
                                            } else {
                                                echo '<img src="images/default-avatar.png" width="200" height="200">';
                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">New Profile Image</label>
                                    <div class="controls">
                                        <input type="file" name="user_profil_image" id="user_profil_image" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
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

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
<?php } ?>
