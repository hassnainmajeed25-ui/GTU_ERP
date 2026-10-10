<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id=$_SESSION['student_id'];

/*=========================================
GET ALL ADMINS
(EXCEPT SUPER ADMIN)
=========================================*/

$admins=$conn->prepare("

SELECT

id,
full_name,
department

FROM admins

WHERE

status='Active'

AND department IN
(

'Admission Office',
'Finance Office',
'Examination Office',
'Student Affairs',
'Academic Office'

)

ORDER BY department ASC

");

$admins->execute();

$adminResult=$admins->get_result();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Compose Message

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#edf2f7;

font-family:Arial,Helvetica,sans-serif;

}

/*=========================================
PAGE HEADER
=========================================*/

.page-header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

color:#fff;

padding:20px;

border-radius:15px;

margin-bottom:25px;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}

.page-header h3{

margin:0;

font-weight:bold;

}

/*=========================================
CARD
=========================================*/

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

/*=========================================
CARD HEADER
=========================================*/

.card-header{

background:#0B1F52;

color:#fff;

font-size:20px;

font-weight:bold;

}

/*=========================================
LABEL
=========================================*/

.form-label{

font-weight:bold;

color:#444;

}

/*=========================================
INPUT
=========================================*/

.form-control,
.form-select{

border-radius:10px;

padding:12px;

}

/*=========================================
TEXTAREA
=========================================*/

textarea{

resize:none;

}

/*=========================================
BUTTONS
=========================================*/

.btn-send{

background:#2563eb;

color:#fff;

font-weight:bold;

border:none;

padding:12px 30px;

border-radius:10px;

}

.btn-send:hover{

background:#0B1F52;

color:#fff;

}

.btn-back{

background:#6c757d;

color:#fff;

font-weight:bold;

padding:12px 30px;

border-radius:10px;

}

.btn-back:hover{

background:#495057;

color:#fff;

}

</style>

</head>

<body>

<div class="container py-4">

<div class="page-header d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</h3>

<a
href="dashboard.php"
class="btn btn-light">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<?php

if(isset($_SESSION['success'])){

?>

<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle me-2"></i>

<?php

echo $_SESSION['success'];

unset($_SESSION['success']);

?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

}

?>

<?php

if(isset($_SESSION['error'])){

?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-times-circle me-2"></i>

<?php

echo $_SESSION['error'];

unset($_SESSION['error']);

?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

}

?>

<div class="card">

<div class="card-header">

<i class="fas fa-envelope me-2"></i>

New Message

</div>

<div class="card-body">

<form

method="POST"

action="send_message.php"

>

<div class="mb-3">

<label class="form-label">

Select Department

</label>

<select

name="receiver_id"

class="form-select"

required

>

<option value="">

Choose Department

</option>

<?php

while($admin=$adminResult->fetch_assoc()){

?>

<option

value="<?php echo $admin['id']; ?>"

>

<?php

echo htmlspecialchars($admin['department']);

?>

-

<?php

echo htmlspecialchars($admin['full_name']);

?>

</option>

<?php

}

?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Subject

</label>

<input

type="text"

name="subject"

class="form-control"

maxlength="255"

placeholder="Enter Subject"

required

>

</div>

<div class="mb-3">

<label class="form-label">

Message

</label>

<textarea

name="message"

class="form-control"

rows="8"

placeholder="Write your message here..."

required

></textarea>

</div>

<div class="d-flex justify-content-between">

<a

href="dashboard.php"

class="btn btn-back">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

class="btn btn-send">

<i class="fas fa-paper-plane me-2"></i>

Send Message

</button>

</div>

</form>

</div>

</div>

<div class="card mt-4">

<div class="card-body">

<h5 class="text-primary">

<i class="fas fa-info-circle me-2"></i>

Instructions

</h5>

<ul class="mb-0">

<li>

You can send messages only to University Offices.

</li>

<li>

Super Admin cannot receive messages.

</li>

<li>

Teachers cannot receive messages.

</li>

<li>

Wait for the department to reply.

</li>

<li>

You can view replies from the Sent Messages page.

</li>

</ul>

</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

</script>

</body>

</html>