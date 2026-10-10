<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


// =============================
// Get Programs
// =============================

$programs = $conn->query(
    "SELECT id, program_name 
     FROM programs 
     ORDER BY program_name ASC"
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
// Add Subject
// =============================

if(isset($_POST['add_subject']))
{

    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];
    $credit_hours = $_POST['credit_hours'];
    $faculty_id   = $_POST['faculty_id'];
    $program_id   = $_POST['program_id'];
    $semester     = $_POST['semester'];
    $status       = $_POST['status'];


    $query = $conn->prepare(
    "INSERT INTO subjects
    (
        subject_code,
        subject_name,
        credit_hours,
        faculty_id,
        program_id,
        semester,
        status
    )

    VALUES (?,?,?,?,?,?,?)"
    );


    $query->bind_param(
        "ssiiiss",
        $subject_code,
        $subject_name,
        $credit_hours,
        $faculty_id,
        $program_id,
        $semester,
        $status
    );


    if($query->execute())
    {

        echo "
        <script>
        alert('Subject Added Successfully');
        window.location='manage_subjects.php';
        </script>
        ";

    }

    else
    {

        echo "Error: ".$conn->error;

    }


}


?>


<!DOCTYPE html>

<html>

<head>

<title>Add Subject</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


</head>


<body>


<div class="container mt-4">


<h2>

<i class="fas fa-plus"></i>

Add New Subject

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
required>

</div>



<div class="col-md-6 mb-3">

<label>
Subject Name
</label>

<input type="text"
name="subject_name"
class="form-control"
required>

</div>



<div class="col-md-4 mb-3">

<label>
Credit Hours
</label>

<input type="number"
name="credit_hours"
class="form-control"
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


<option value="<?php echo $faculty['id']; ?>">

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


<option value="">
Select Program
</option>


<?php

while($program = $programs->fetch_assoc())

{

?>


<option value="<?php echo $program['id']; ?>">

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


<select name="semester" class="form-control" required>


<option value="">
Select Semester
</option>


<?php

for($i=1;$i<=8;$i++)

{

?>


<option value="<?php echo $i; ?>">

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


<option value="Active">
Active
</option>


<option value="Inactive">
Inactive
</option>


</select>


</div>



</div>




<button type="submit"
name="add_subject"
class="btn btn-success">


<i class="fas fa-save"></i>

Save Subject


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