<?php

session_start();

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['admin_id'])){
    exit("Login Required");
}

$program_id = (int)$_POST['program_id'];
$semester   = (int)$_POST['semester'];

$sql = "

SELECT

students.id,
students.roll_no,
students.registration_no,
students.first_name,
students.last_name,

final_results.cgpa,
final_results.percentage,
final_results.result_status,
final_results.is_published,
final_results.published_date

FROM final_results

INNER JOIN students
ON students.id = final_results.student_id

WHERE

final_results.program_id=?
AND final_results.semester=?

ORDER BY students.roll_no ASC

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ii",
$program_id,
$semester
);

$stmt->execute();

$result = $stmt->get_result();

?>

<div class="section-title">

Semester Final Results

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>
<th>Roll No</th>
<th>Registration No</th>
<th>Student Name</th>
<th>Percentage</th>
<th>CGPA</th>
<th>Result</th>
<th>Published</th>
<th width="160">Action</th>

</tr>

</thead>

<tbody>

<?php

$sr = 1;

while($row = $result->fetch_assoc()){
?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo htmlspecialchars($row['roll_no']); ?></td>

<td><?php echo htmlspecialchars($row['registration_no']); ?></td>

<td>
<?php
echo htmlspecialchars(
$row['first_name']." ".$row['last_name']
);
?>
</td>

<td>
<?php echo number_format($row['percentage'],2); ?>%
</td>

<td>
<?php echo number_format($row['cgpa'],2); ?>
</td>

<td>

<?php

if($row['result_status']=="Pass"){

    echo '<span class="badge bg-success">Pass</span>';

}else{

    echo '<span class="badge bg-danger">Fail</span>';

}

?>

</td>

<td>

<?php

if($row['is_published']=="Yes"){

    echo '<span class="badge bg-success">Published</span>';

}else{

    echo '<span class="badge bg-warning text-dark">Pending</span>';

}

?>

</td>

<td>

<?php

if($row['is_published']=="No"){

?>

<button

type="button"

class="btn btn-success btn-sm"

onclick="publishResult(

<?php echo $row['id']; ?>,

<?php echo $semester; ?>

)">

<i class="fas fa-upload me-1"></i>

Publish

</button>

<?php

}else{

?>

<span class="text-success fw-bold">

<i class="fas fa-check-circle"></i>

Published

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

<script>

function publishResult(student_id,semester){

if(!confirm("Are you sure you want to publish this result?")){
return;
}

fetch("../verify_single_result.php",{
method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:
"student_id="+student_id+
"&semester="+semester

})

.then(response=>response.json())

.then(data=>{

alert(data.message);

if(data.status=="success"){

location.reload();

}

});

}

</script>