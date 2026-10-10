<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$message="";

/*==============================
LOAD SETTINGS
==============================*/

$sql=$conn->prepare("

SELECT *

FROM university_settings

LIMIT 1

");

$sql->execute();

$result=$sql->get_result();

$settings=$result->fetch_assoc();

/*==============================
UPDATE SETTINGS
==============================*/

if(isset($_POST['save_settings'])){

$university_name=trim($_POST['university_name']);

$short_name=trim($_POST['short_name']);

$tagline=trim($_POST['tagline']);

$phone=trim($_POST['phone']);

$mobile=trim($_POST['mobile']);

$email=trim($_POST['email']);

$website=trim($_POST['website']);

$address=trim($_POST['address']);

$city=trim($_POST['city']);

$province=trim($_POST['province']);

$country=trim($_POST['country']);

$facebook=trim($_POST['facebook']);

$instagram=trim($_POST['instagram']);

$linkedin=trim($_POST['linkedin']);

$youtube=trim($_POST['youtube']);

$twitter=trim($_POST['twitter']);

$bank_name=trim($_POST['bank_name']);

$account_title=trim($_POST['account_title']);

$account_number=trim($_POST['account_number']);

$iban=trim($_POST['iban']);

$branch_name=trim($_POST['branch_name']);

$branch_code=trim($_POST['branch_code']);

$update=$conn->prepare("

UPDATE university_settings

SET

university_name=?,

short_name=?,

tagline=?,

phone=?,

mobile=?,

email=?,

website=?,

address=?,

city=?,

province=?,

country=?,

facebook=?,

instagram=?,

linkedin=?,

youtube=?,

twitter=?,

bank_name=?,

account_title=?,

account_number=?,

iban=?,

branch_name=?,

branch_code=?

WHERE id=1

");

$update->bind_param(

"ssssssssssssssssssssss",

$university_name,

$short_name,

$tagline,

$phone,

$mobile,

$email,

$website,

$address,

$city,

$province,

$country,

$facebook,

$instagram,

$linkedin,

$youtube,

$twitter,

$bank_name,

$account_title,

$account_number,

$iban,

$branch_name,

$branch_code

);

if($update->execute()){

$message="University Settings Updated Successfully.";

$sql->execute();

$result=$sql->get_result();

$settings=$result->fetch_assoc();

}else{

$message="Unable to update settings.";

}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>University Settings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.container-box{

max-width:1100px;

margin:35px auto;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.section-title{

font-size:18px;

font-weight:bold;

margin-top:20px;

margin-bottom:15px;

color:#0B1F52;

border-left:5px solid #0B1F52;

padding-left:10px;

}

</style>

</head>

<body>

<div class="container container-box">

<div class="card">

<div class="card-header">

<i class="fas fa-university me-2"></i>

University Settings

</div>

<div class="card-body">

<?php

if($message!=""){

?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php

}

?>

<form method="POST">

<div class="section-title">

Basic Information

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

University Name

</label>

<input

type="text"

name="university_name"

class="form-control"

value="<?php echo htmlspecialchars($settings['university_name']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Short Name

</label>

<input

type="text"

name="short_name"

class="form-control"

value="<?php echo htmlspecialchars($settings['short_name']); ?>"

required>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Tagline

</label>

<input

type="text"

name="tagline"

class="form-control"

value="<?php echo htmlspecialchars($settings['tagline']); ?>">

</div>

</div>

<div class="section-title">

Contact Information

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Phone

</label>

<input

type="text"

name="phone"

class="form-control"

value="<?php echo htmlspecialchars($settings['phone']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Mobile

</label>

<input

type="text"

name="mobile"

class="form-control"

value="<?php echo htmlspecialchars($settings['mobile']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Email

</label>

<input

type="email"

name="email"

class="form-control"

value="<?php echo htmlspecialchars($settings['email']); ?>">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Website

</label>

<input

type="text"

name="website"

class="form-control"

value="<?php echo htmlspecialchars($settings['website']); ?>">

</div>

</div>

<div class="section-title">

Address

</div>

<div class="row">

<div class="col-md-12 mb-3">

<label class="form-label">

Complete Address

</label>

<textarea

name="address"

class="form-control"

rows="3"><?php echo htmlspecialchars($settings['address']); ?></textarea>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

City

</label>

<input

type="text"

name="city"

class="form-control"

value="<?php echo htmlspecialchars($settings['city']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Province

</label>

<input

type="text"

name="province"

class="form-control"

value="<?php echo htmlspecialchars($settings['province']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Country

</label>

<input

type="text"

name="country"

class="form-control"

value="<?php echo htmlspecialchars($settings['country']); ?>">
</div>

</div>
<div class="section-title">

Social Media

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Facebook

</label>

<input
type="text"
name="facebook"
class="form-control"
value="<?php echo htmlspecialchars($settings['facebook']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Instagram

</label>

<input
type="text"
name="instagram"
class="form-control"
value="<?php echo htmlspecialchars($settings['instagram']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

LinkedIn

</label>

<input
type="text"
name="linkedin"
class="form-control"
value="<?php echo htmlspecialchars($settings['linkedin']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

YouTube

</label>

<input
type="text"
name="youtube"
class="form-control"
value="<?php echo htmlspecialchars($settings['youtube']); ?>">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

X (Twitter)

</label>

<input
type="text"
name="twitter"
class="form-control"
value="<?php echo htmlspecialchars($settings['twitter']); ?>">

</div>

</div>

<div class="section-title">

Bank Information

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Bank Name

</label>

<input
type="text"
name="bank_name"
class="form-control"
value="<?php echo htmlspecialchars($settings['bank_name']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Account Title

</label>

<input
type="text"
name="account_title"
class="form-control"
value="<?php echo htmlspecialchars($settings['account_title']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Account Number

</label>

<input
type="text"
name="account_number"
class="form-control"
value="<?php echo htmlspecialchars($settings['account_number']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

IBAN

</label>

<input
type="text"
name="iban"
class="form-control"
value="<?php echo htmlspecialchars($settings['iban']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Branch Name

</label>

<input
type="text"
name="branch_name"
class="form-control"
value="<?php echo htmlspecialchars($settings['branch_name']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Branch Code

</label>

<input
type="text"
name="branch_code"
class="form-control"
value="<?php echo htmlspecialchars($settings['branch_code']); ?>">

</div>

</div>

<hr>

<div class="d-flex justify-content-between">

<a href="dashboard.php" class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back Dashboard

</a>

<button
type="submit"
name="save_settings"
class="btn btn-success">

<i class="fas fa-save me-2"></i>

Save Settings

</button>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>