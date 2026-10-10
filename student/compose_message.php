<?php

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

$student_name=$_SESSION['student_name'];

$success="";
$error="";


/*=========================================
GET ACTIVE OFFICES
=========================================*/

$admins=$conn->query("

SELECT

id,
full_name,
department

FROM admins

WHERE status='Active'

ORDER BY department

");


/*=========================================
SEND MESSAGE
=========================================*/

if(isset($_POST['send'])){

    $subject=trim($_POST['subject']);

    $message=trim($_POST['message']);

    $receiver_id=(int)$_POST['receiver_id'];

    $insert=$conn->prepare("

    INSERT INTO messages

    (

    sender_role,

    sender_id,

    receiver_role,

    receiver_id,

    subject,

    message

    )

    VALUES
(
'Student',
?,
?,
?,
?,
?
)

    ");

    $getRole=$conn->prepare("

SELECT department

FROM admins

WHERE id=?

LIMIT 1

");

$getRole->bind_param("i",$receiver_id);

$getRole->execute();

$roleResult=$getRole->get_result();

$roleData=$roleResult->fetch_assoc();

$receiver_role=$roleData['department'];

    $insert->bind_param(

"isiss",

$student_id,

$receiver_role,

$receiver_id,

$subject,

$message

);

    if($insert->execute()){

        $success="Message sent successfully.";

    }else{

        $error="Unable to send message.";

    }

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Compose Message</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.card{

margin-top:40px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:white;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control{

height:50px;

border-radius:10px;

}

textarea{

resize:none;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card">

<div class="card-header">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</div>

<div class="card-body">
<?php

if($success!=""){

?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php

}

?>

<?php

if($error!=""){

?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php

}

?>
<form method="POST">

<div class="mb-3">

<label class="form-label">

Send To

</label>

<select
name="receiver_id"
class="form-select"
required>

<option value="">

Select Office

</option>

<?php while($admin=$admins->fetch_assoc()){ ?>

<option value="<?php echo $admin['id']; ?>">

<?php echo $admin['department']; ?>

</option>

<?php } ?>

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

placeholder="Enter Subject"

required>

</div>

<div class="mb-4">

<label class="form-label">

Message

</label>

<textarea

name="message"

rows="8"

class="form-control"

placeholder="Write your message here..."

required></textarea>

</div>

<div class="d-flex justify-content-between">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="send"

class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

Send Message

</button>

</div>

</form>
</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>