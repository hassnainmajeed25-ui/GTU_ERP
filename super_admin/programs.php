<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    exit;
}

if(!isset($_GET['faculty_id'])){
    exit;
}

$faculty_id=(int)$_GET['faculty_id'];

$stmt=$conn->prepare("

SELECT

id,

program_name

FROM programs

WHERE faculty_id=?

AND status='Active'

ORDER BY program_name ASC

");

$stmt->bind_param("i",$faculty_id);

$stmt->execute();

$result=$stmt->get_result();

echo '<option value="">Select Program</option>';

while($row=$result->fetch_assoc()){

echo '<option value="'.$row['id'].'">'.$row['program_name'].'</option>';

}

?>