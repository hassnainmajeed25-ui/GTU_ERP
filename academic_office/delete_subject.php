<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


// =============================
// Check ID
// =============================

if(!isset($_GET['id']))
{
    header("Location: manage_subjects.php");
    exit;
}


$id = $_GET['id'];



// =============================
// Delete Subject
// =============================

$delete = $conn->prepare(
"
DELETE FROM subjects
WHERE id=?
"
);


$delete->bind_param(
"i",
$id
);



if($delete->execute())
{


echo "

<script>

alert('Subject Deleted Successfully');

window.location='manage_subjects.php';

</script>

";


}

else
{


echo "

<script>

alert('Unable to Delete Subject');

window.location='manage_subjects.php';

</script>

";


}


?>