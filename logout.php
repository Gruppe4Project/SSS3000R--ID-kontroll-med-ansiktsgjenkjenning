<?php
session_start();
$_SESSION['alogin']=="";
session_unset();
//session_destroy();
$_SESSION['successmsg']="You have successfully logout";
?>
<script language="javascript">
document.location="index.php";
</script>
