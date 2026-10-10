<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


// =============================
// Total Subjects Count
// =============================

$total_subjects_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM subjects"
);

$total_subjects = mysqli_fetch_assoc($total_subjects_query)['total'];

// =============================
// Dashboard Statistics
// =============================

// Total Programs

$total_programs = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT COUNT(*) AS total FROM programs"
)

)['total'];



// Active Subjects

$active_subjects = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT COUNT(*) AS total
FROM subjects
WHERE status='Active'"
)

)['total'];



// Inactive Subjects

$inactive_subjects = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT COUNT(*) AS total
FROM subjects
WHERE status='Inactive'"
)

)['total'];

// =============================
// Get Programs
// =============================

$programs = mysqli_query(
    $conn,
    "SELECT id, program_name 
     FROM programs 
     ORDER BY program_name ASC"
);


// =============================
// Filters
// =============================

$program_id = $_GET['program_id'] ?? '';

$semester = $_GET['semester'] ?? '';

$search = $_GET['search'] ?? '';

// =============================
// Subjects Query
// =============================

$sql = "

SELECT 
subjects.*,
programs.program_name

FROM subjects

INNER JOIN programs

ON subjects.program_id = programs.id

WHERE 1

";


// Program Filter

if($program_id != '')
{
    $sql .= " AND subjects.program_id='$program_id'";
}


// Semester Filter

if($semester != '')
{
    $sql .= " AND subjects.semester='$semester'";
}


if($search != '')
{

$sql .= "
AND
(
subjects.subject_code LIKE '%$search%'
OR
subjects.subject_name LIKE '%$search%'
)
";

}

$sql .= "

ORDER BY 
subjects.semester ASC,
subjects.id DESC

";


$subjects = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>
<html>

<head>

<title>
Manage Subjects - Academic Office
</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>


<body>


<div class="container mt-4">


<h2 class="mb-4">

<i class="fas fa-book"></i>

Manage Subjects

</h2>

<div class="row mb-4">


<div class="col-md-3">


<div class="card bg-primary text-white">


<div class="card-body">


<h5>Total Subjects</h5>

<h2>

<?php echo $total_subjects; ?>

</h2>


</div>

</div>

</div>




<div class="col-md-3">


<div class="card bg-success text-white">


<div class="card-body">


<h5>Active Subjects</h5>

<h2>

<?php echo $active_subjects; ?>

</h2>


</div>

</div>

</div>




<div class="col-md-3">


<div class="card bg-danger text-white">


<div class="card-body">


<h5>Inactive Subjects</h5>

<h2>

<?php echo $inactive_subjects; ?>

</h2>


</div>

</div>

</div>




<div class="col-md-3">


<div class="card bg-dark text-white">


<div class="card-body">


<h5>Total Programs</h5>

<h2>

<?php echo $total_programs; ?>

</h2>


</div>

</div>

</div>


</div>

<div class="card mb-4">

<div class="card-body">


<form method="GET">


<div class="row">


<!-- Program -->

<div class="col-md-5">

<label class="form-label">
Program
</label>


<select name="program_id" class="form-control">


<option value="">
All Programs
</option>


<?php

while($program = mysqli_fetch_assoc($programs))

{

?>


<option value="<?php echo $program['id']; ?>"

<?php

if($program_id == $program['id'])
{
echo "selected";
}

?>

>


<?php echo $program['program_name']; ?>


</option>


<?php

}

?>


</select>


</div>




<!-- Semester -->


<div class="col-md-3">


<label class="form-label">
Semester
</label>


<select name="semester" class="form-control">


<option value="">
All Semester
</option>


<?php

for($i=1;$i<=8;$i++)

{

?>


<option value="<?php echo $i; ?>"

<?php

if($semester==$i)
{
echo "selected";
}

?>

>

Semester <?php echo $i; ?>


</option>


<?php

}

?>


</select>


</div>


<div class="col-md-4 mb-3">


<label class="form-label">

Search Subject

</label>


<input type="text"

name="search"

class="form-control"

placeholder="Code or Name"

value="<?php echo $search; ?>">


</div>


<div class="col-md-2 mt-4">


<button class="btn btn-primary w-100">

<i class="fas fa-filter"></i>

Filter

</button>


</div>




<div class="col-md-2 mt-4">


<a href="add_subject.php" class="btn btn-success w-100">


<i class="fas fa-plus"></i>

Add Subject


</a>


</div>


</div>


</form>


</div>

</div>

<!-- ============================= -->
<!-- Subjects Table -->
<!-- ============================= -->


<div class="card">


<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-list"></i>

All Subjects

</h5>

</div>



<div class="card-body">


<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead class="table-dark">


<tr>

<th>#</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Credit Hours</th>

<th>Program</th>

<th>Semester</th>

<th>Status</th>

<th width="150">
Action
</th>


</tr>


</thead>



<tbody>


<?php


$count = 1;


if(mysqli_num_rows($subjects) > 0)

{


while($row = mysqli_fetch_assoc($subjects))


{


?>



<tr>


<td>

<?php echo $count++; ?>

</td>



<td>

<strong>

<?php echo htmlspecialchars($row['subject_code']); ?>

</strong>

</td>



<td>

<?php echo htmlspecialchars($row['subject_name']); ?>


</td>



<td>

<?php echo $row['credit_hours']; ?>

</td>



<td>

<?php echo htmlspecialchars($row['program_name']); ?>


</td>



<td>

<span class="badge bg-info">

Semester 
<?php echo $row['semester']; ?>


</span>


</td>



<td>


<?php

if($row['status']=="Active")

{

?>

<span class="badge bg-success">

Active

</span>


<?php

}

else

{

?>

<span class="badge bg-danger">

Inactive

</span>


<?php

}

?>



</td>




<td>


<a href="edit_subject.php?id=<?php echo $row['id']; ?>"


class="btn btn-warning btn-sm">


<i class="fas fa-edit"></i>


</a>




<a href="delete_subject.php?id=<?php echo $row['id']; ?>"


class="btn btn-danger btn-sm"


onclick="return confirm('Are you sure you want to delete this subject?');">


<i class="fas fa-trash"></i>


</a>



</td>


</tr>



<?php


}


}

else

{


?>


<tr>


<td colspan="8" class="text-center text-danger">


No Subjects Found


</td>


</tr>



<?php


}


?>


</tbody>



</table>


</div>


</div>


</div>


<br>



</div>


</body>


</html>