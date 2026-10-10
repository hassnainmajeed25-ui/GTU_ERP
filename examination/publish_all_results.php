<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD']!="POST"){
    exit("Invalid Request");
}

$program_id=(int)$_POST['program_id'];
$semester=(int)$_POST['semester'];

/*=========================================
CHECK VERIFIED RESULTS
=========================================*/

$check=$conn->prepare("

SELECT COUNT(*) total

FROM semester_results

WHERE

program_id=?
AND semester=?
AND result_status='Generated'

");

$check->bind_param(
"ii",
$program_id,
$semester
);

$check->execute();

$data=$check->get_result()->fetch_assoc();

if($data['total']==0){

$_SESSION['error']="No Results Available For Publishing.";

header("Location: publish_results.php");

exit;

}

/*=========================================
PUBLISH SEMESTER RESULTS
=========================================*/

$publish=$conn->prepare("

UPDATE semester_results

SET result_status='Published'

WHERE

program_id=?
AND semester=?

");

$publish->bind_param(
"ii",
$program_id,
$semester
);

$publish->execute();

/*=========================================
PUBLISH FINAL RESULTS
=========================================*/

$publish2 = $conn->prepare("

UPDATE results r

INNER JOIN students s
ON r.student_id = s.id

SET r.published_at = NOW()

WHERE

r.semester = ?
AND s.program_id = ?

");

$publish2->bind_param(

"ii",

$semester,
$program_id

);

/*=========================================
PUBLISH FINAL RESULTS
=========================================*/

$publish2 = $conn->prepare("

UPDATE results r

INNER JOIN students s
ON r.student_id = s.id

SET r.published_at = NOW()

WHERE

r.semester = ?
AND s.program_id = ?

");

$publish2->bind_param(
"ii",
$semester,
$program_id
);

$publish2->execute();

/*=========================================
SUCCESS
=========================================*/

$_SESSION['success']="Semester Result Published Successfully.";

header("Location: publish_results.php");

exit;

?>