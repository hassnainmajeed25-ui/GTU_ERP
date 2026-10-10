<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_scholarships.php");
    exit;

}

$id=(int)$_GET['id'];

$delete=$conn->prepare("

DELETE FROM scholarships

WHERE id=?

LIMIT 1

");

$delete->bind_param("i",$id);

$delete->execute();

header("Location: manage_scholarships.php");

exit;

?>