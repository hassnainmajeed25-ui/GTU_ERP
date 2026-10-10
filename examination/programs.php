<?php

require_once '../config/config.php';
require_once '../config/database.php';

$faculty_id = intval($_GET['faculty_id']);

$query = $conn->prepare("

SELECT

id,
program_name

FROM programs

WHERE faculty_id=?

AND status='Active'

ORDER BY program_name ASC

");

$query->bind_param("i",$faculty_id);

$query->execute();

$result = $query->get_result();

echo "<option value=''>Select Program</option>";

while($row=$result->fetch_assoc()){

    echo "<option value='".$row['id']."'>".$row['program_name']."</option>";

}

?>