<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=GTU_Finance_Report.xls");

?>

<table border="1">

<tr style="background:#0B1F52;color:white;">

<th>#</th>
<th>Application ID</th>
<th>Student</th>
<th>Faculty</th>
<th>Program</th>
<th>Amount</th>
<th>Payment Date</th>
<th>Status</th>

</tr>

<?php

$sql="

SELECT

application_payments.processing_fee,
application_payments.payment_date,
application_payments.verification_status,

applications.application_id,

students.first_name,
students.last_name,

faculties.faculty_name,

programs.program_name

FROM application_payments

INNER JOIN applications
ON applications.id=application_payments.application_id

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

ORDER BY application_payments.created_at DESC

";

$result=$conn->query($sql);

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo $row['application_id']; ?></td>

<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>

<td><?php echo $row['faculty_name']; ?></td>

<td><?php echo $row['program_name']; ?></td>

<td><?php echo $row['processing_fee']; ?></td>

<td><?php echo $row['payment_date']; ?></td>

<td><?php echo $row['verification_status']; ?></td>

</tr>

<?php

}

?>

</table>