<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_fee_structure.php");
    exit;

}

$id=(int)$_GET['id'];

/*
Future Safety:
Agar future me is Fee Structure se
students ya challans linked hon,
to delete se pehle yahan check laga sakte hain.
*/

$delete=$conn->prepare("

DELETE FROM fee_structure

WHERE id=?

");

$delete->bind_param("i",$id);

if($delete->execute()){

    header("Location: manage_fee_structure.php?deleted=1");

}else{

    header("Location: manage_fee_structure.php?deleted=0");

}

exit;

?>