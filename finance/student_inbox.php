<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}


$admin_id = $_SESSION['admin_id'];

$admin_name = $_SESSION['admin_name'];



/*=========================================
GET STUDENT MESSAGES
=========================================*/


$stmt = $conn->prepare("

SELECT

student_messages.*,

students.first_name,
students.last_name


FROM student_messages


INNER JOIN students

ON students.id = student_messages.student_id


WHERE

student_messages.admin_id=?


ORDER BY

student_messages.created_at DESC


");


$stmt->bind_param(

"i",

$admin_id

);


$stmt->execute();


$result=$stmt->get_result();



?>


<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

Student Inbox

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#edf2f7;

font-family:'Segoe UI',sans-serif;

}



/* HEADER */

.page-header{

background:linear-gradient(135deg,#000,#2563eb);

color:white;

padding:20px;

border-radius:15px;

margin-bottom:25px;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}



.page-header h3{

font-weight:700;

margin:0;

}



/* CARD */


.card{

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.12);

}



.card-header{

background:#0B1F52;

color:white;

font-size:20px;

font-weight:bold;

}



/* TABLE */


.table th{

background:#2563eb;

color:white;

text-align:center;

}


.table td{

vertical-align:middle;

text-align:center;

}



/* BUTTON */


.btn-open{

background:#2563eb;

color:white;

}


.btn-open:hover{

background:#0B1F52;

color:white;

}



</style>


</head>


<body>


<div class="container-fluid py-4">



<div class="page-header d-flex justify-content-between align-items-center">


<h3>

<i class="fas fa-envelope me-2"></i>

Student Messages Inbox

</h3>


<a href="dashboard.php" class="btn btn-warning">

<i class="fas fa-arrow-left"></i>

Dashboard

</a>


</div>

<!--=========================================
MESSAGE TABLE
=========================================-->


<div class="card">


<div class="card-header">

<i class="fas fa-inbox me-2"></i>

Student Inbox Messages

</div>



<div class="card-body">



<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead>


<tr>

<th width="50">#</th>

<th>Student</th>

<th>Subject</th>

<th>Date</th>

<th>Status</th>

<th width="120">Action</th>

</tr>


</thead>



<tbody>


<?php


if($result->num_rows > 0){


$count=1;



while($row=$result->fetch_assoc()){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>



<td>


<?php


echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);


?>


</td>



<td>


<?php


echo htmlspecialchars(

$row['subject']

);


?>


</td>




<td>


<?php


echo date(

"d M Y",

strtotime($row['created_at'])

);


?>


<br>


<small class="text-muted">


<?php


echo date(

"h:i A",

strtotime($row['created_at'])

);


?>


</small>


</td>




<td>



<?php


if($row['status']=="Pending"){


?>


<span class="badge bg-warning text-dark">


Pending


</span>


<?php


}else{


?>


<span class="badge bg-success">


Replied


</span>


<?php


}


?>



</td>




<td>


<a

href="student_reply.php?id=<?php echo $row['id']; ?>"

class="btn btn-open btn-sm">


<i class="fas fa-reply me-1"></i>


Reply


</a>



</td>


</tr>



<?php


}



}else{


?>



<tr>


<td colspan="6" class="text-center py-5">


<i class="fas fa-envelope-open-text"

style="font-size:60px;color:#ccc;"></i>



<h5 class="mt-3">


No Student Messages


</h5>


<p class="text-muted">


Currently no messages from students.


</p>


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

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>