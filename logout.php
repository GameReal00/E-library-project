<?php
session_start();
session_destroy();
header("Location: indeex.php");
exit();
?>