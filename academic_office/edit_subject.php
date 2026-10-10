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
// Get Subject Data
// =============================

$result = $conn->query(
"
SELECT *
FROM subjects
WHERE id='$id'
"
);


$subject = $result->fetch_assoc();



if(!$subject)
{
    echo "Subject Not Found";
    exit;
}


// =============================
// Get Programs
// =============================

$programs = $conn->query(
"
SELECT id, program_name
FROM programs
ORDER BY program_name ASC
"
);

// =============================
// Get Faculties
// =============================

$faculties = $conn->query(
"
SELECT id, faculty_name
FROM faculties
ORDER BY faculty_name ASC
"
);



// =============================
// Update Subject
// =============================

if(isset($_POST['update_subject']))
{


$subject_code = $_POST['subject_code'];

$subject_name = $_POST['subject_name'];

$credit_hours = $_POST['credit_hours'];

$faculty_id = $_POST['faculty_id'];

$program_id = $_POST['program_id'];

$semester = $_POST['semester'];

$status = $_POST['status'];



$update = $conn->prepare(
"
UPDATE subjects SET

subject_code=?,
subject_name=?,
credit_hours=?,
faculty_id=?,
program_id=?,
semester=?,
status=?

WHERE id=?

"
);



$update->bind_param(
"ssiiiisi",
$subject_code,
$subject_name,
$credit_hours,
$faculty_id,
$program_id,
$semester,
$status,
$id
);



if($update->execute())
{


echo "

<script>

alert('Subject Updated Successfully');

window.location='manage_subjects.php';

</script>

";


}


}



?>

<!DOCTYPE html>

<html>

<head>

<title>
Edit Subject
</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>


<body>


<div class="container mt-4">


<h2>

<i class="fas fa-edit"></i>

Edit Subject

</h2>



<div class="card mt-4">


<div class="card-body">


<form method="POST">


<div class="row">



<div class="col-md-6 mb-3">


<label>
Subject Code
</label>


<input type="text"
name="subject_code"
class="form-control"

value="<?php echo $subject['subject_code']; ?>"

required>


</div>




<div class="col-md-6 mb-3">


<label>
Subject Name
</label>


<input type="text"
name="subject_name"
class="form-control"

value="<?php echo $subject['subject_name']; ?>"

required>


</div>





<div class="col-md-4 mb-3">


<label>
Credit Hours
</label>


<input type="number"
name="credit_hours"
class="form-control"

value="<?php echo $subject['credit_hours']; ?>"

required>


</div>

<div class="col-md-4 mb-3">


<label>
Faculty
</label>


<select name="faculty_id" class="form-control" required>


<option value="">
Select Faculty
</option>


<?php

while($faculty = $faculties->fetch_assoc())

{

?>


<option value="<?php echo $faculty['id']; ?>"

<?php

if($subject['faculty_id']==$faculty['id'])
{
echo "selected";
}

?>

>


<?php echo $faculty['faculty_name']; ?>


</option>


<?php

}

?>


</select>


</div>

<div class="col-md-4 mb-3">


<label>
Program
</label>


<select name="program_id" class="form-control" required>



<?php

while($program = $programs->fetch_assoc())

{

?>


<option value="<?php echo $program['id']; ?>"

<?php

if($subject['program_id']==$program['id'])
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





<div class="col-md-4 mb-3">


<label>
Semester
</label>


<select name="semester" class="form-control">


<?php

for($i=1;$i<=8;$i++)

{

?>


<option value="<?php echo $i; ?>"

<?php

if($subject['semester']==$i)
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


<label>
Status
</label>


<select name="status" class="form-control">



<option value="Active"

<?php

if($subject['status']=="Active")
{
echo "selected";
}

?>

>

Active

</option>



<option value="Inactive"

<?php

if($subject['status']=="Inactive")
{
echo "selected";
}

?>

>

Inactive

</option>



</select>


</div>


</div>




<button type="submit"
name="update_subject"
class="btn btn-success">


<i class="fas fa-save"></i>

Update Subject


</button>



<a href="manage_subjects.php"
class="btn btn-secondary">

Back

</a>



</form>


</div>


</div>


</div>


</body>

</html>