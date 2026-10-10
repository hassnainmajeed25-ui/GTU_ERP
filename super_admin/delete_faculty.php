<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: manage_faculties.php");
    exit;
}

$id=(int)$_GET['id'];

$conn->query("DELETE FROM faculties WHERE id=$id");

header("Location: manage_faculties.php");

exit;

?>