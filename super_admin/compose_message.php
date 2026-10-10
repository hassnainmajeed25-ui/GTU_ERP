<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['super_admin_id'])){
    header("Location: login.php");
    exit;
}

$message="";

/*=========================================
SEND MESSAGE
=========================================*/

if(isset($_POST['send'])){

    $receiver_id=(int)$_POST['receiver_id'];

    $subject=trim($_POST['subject']);

    $message_text=trim($_POST['message']);

    $sender_role="Super Admin";

    $sender_id=$_SESSION['super_admin_id'];

    /* Get Receiver Department */

    $getAdmin=$conn->prepare("

    SELECT department

    FROM admins

    WHERE id=?

    LIMIT 1

    ");

    $getAdmin->bind_param("i",$receiver_id);

    $getAdmin->execute();

    $adminResult=$getAdmin->get_result();

    if($adminResult->num_rows>0){

        $adminData=$adminResult->fetch_assoc();

        $receiver_role=$adminData['department'];

    }else{

        die("Receiver Admin Not Found.");

    }

    /* Insert Message */

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
    (?,?,?,?,?,?)

    ");

    $insert->bind_param(

        "sissss",

        $sender_role,

        $sender_id,

        $receiver_role,

        $receiver_id,

        $subject,

        $message_text

    );

    if($insert->execute()){

        $message="<div class='alert alert-success'>
        Message sent successfully.
        </div>";

    }else{

        $message="<div class='alert alert-danger'>
        Unable to send message.
        </div>";

    }

}

/*=========================================
LOAD ADMINS
=========================================*/

$admins=$conn->query("

SELECT
id,
full_name,
department

FROM admins

WHERE status='Active'

ORDER BY department,full_name

");

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

background:#f4f6f9;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B5ED7;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control,
.form-select{

height:50px;
border-radius:10px;

}

textarea{

border-radius:10px;

resize:none;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</div>

<div class="card-body">

<?php echo $message; ?>

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

<?php

echo $admin['department']." - ".$admin['full_name'];

?>

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

required>

</textarea>

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