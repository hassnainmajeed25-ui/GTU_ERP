<?php

session_start();

require_once '../../config/config.php';
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    exit("Login Required");
}

$program_id = (int)$_POST['program_id'];
$semester   = (int)$_POST['semester'];
$shift       = trim($_POST['shift']);
$subject_id  = (int)$_POST['subject_id'];

$sql = "

SELECT

students.id,
students.roll_no,
students.registration_no,
students.first_name,
students.last_name,

marks_details.quiz_marks,
marks_details.assignment_marks,
marks_details.presentation_marks,
marks_details.practical_marks,
marks_details.mid_marks,
marks_details.final_marks,
marks_details.obtained_marks,

mark_sessions.id AS session_id,
mark_sessions.status,
mark_sessions.result_status,

subjects.subject_name,
subjects.credit_hours

FROM marks_details

INNER JOIN students
ON students.id = marks_details.student_id

INNER JOIN mark_sessions
ON mark_sessions.id = marks_details.mark_session_id

INNER JOIN subjects
ON subjects.id = mark_sessions.subject_id

WHERE

students.program_id=?
AND students.semester=?
AND students.shift=?
AND mark_sessions.subject_id=?

ORDER BY students.roll_no ASC

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iisi",
    $program_id,
    $semester,
    $shift,
    $subject_id
);

$stmt->execute();

$result = $stmt->get_result();

?>

<div class="section-title">
Uploaded Marks
</div>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>
<th>Roll No</th>
<th>Student Name</th>
<th>Quiz</th>
<th>Assignment</th>
<th>Presentation</th>
<th>Practical</th>
<th>Mid</th>
<th>Final</th>
<th>Total</th>
<th>Status</th>
<th width="140">Action</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$result->fetch_assoc()){

$total =
$row['quiz_marks']
+
$row['assignment_marks']
+
$row['presentation_marks']
+
$row['practical_marks']
+
$row['mid_marks']
+
$row['final_marks'];

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo htmlspecialchars($row['roll_no']); ?></td>

<td>
<?php
echo htmlspecialchars(
$row['first_name']." ".$row['last_name']
);
?>
</td>

<td><?php echo $row['quiz_marks']; ?></td>

<td><?php echo $row['assignment_marks']; ?></td>

<td><?php echo $row['presentation_marks']; ?></td>

<td><?php echo $row['practical_marks']; ?></td>

<td><?php echo $row['mid_marks']; ?></td>

<td><?php echo $row['final_marks']; ?></td>

<td class="fw-bold text-primary">
<?php echo number_format($total,2); ?>
</td>

<td>

<?php

if($row['status']=="Verified"){

echo '<span class="badge bg-success">Verified</span>';

}else{

echo '<span class="badge bg-warning text-dark">Submitted</span>';

}

?>

</td>

<td>

<?php

if($row['status']=="Submitted"){

?>

<a
href="../verify_single_result.php?session_id=<?php echo $row['session_id']; ?>&student_id=<?php echo $row['id']; ?>"
class="btn btn-success btn-sm">

<i class="fas fa-check-circle"></i>
Verify

</a>

<?php

}else{

?>

<span class="text-success fw-bold">

<i class="fas fa-check-circle"></i>

Done

</span>

<?php

}

?>

</td>
</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

/*=========================================
CHECK IF ALL STUDENTS VERIFIED
=========================================*/

$allVerified = true;

$result->data_seek(0);

while($check = $result->fetch_assoc()){

    if($check['status'] != "Verified"){

        $allVerified = false;
        break;

    }

}

if($result->num_rows > 0 && $allVerified){

?>

<div class="text-end mt-4">

<a

href="../publish_results.php?program_id=<?php echo $program_id; ?>&semester=<?php echo $semester; ?>&shift=<?php echo urlencode($shift); ?>"

class="btn btn-primary btn-lg">

<i class="fas fa-upload me-2"></i>

Publish Semester Result

</a>

</div>

<?php

}

?>