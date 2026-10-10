<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

header("Location: ../admins/login.php");
exit;

}

if($_SESSION['department']!="Academic Office"){

header("Location: ../admins/login.php");
exit;

}

/*=========================================
GET ID
=========================================*/

if(!isset($_GET['id'])){

header("Location: manage_announcements.php");
exit;

}

$id=intval($_GET['id']);

/*=========================================
LOAD ANNOUNCEMENT
=========================================*/

$sql=$conn->prepare("

SELECT

id,
title,
description,
department,
status

FROM announcements

WHERE id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

header("Location: manage_announcements.php");
exit;

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

Edit Teacher Announcement

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#edf2f7;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.12);

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header bg-warning">

<h3 class="mb-0">

<i class="fas fa-edit me-2"></i>

Edit Teacher Announcement

</h3>

</div>

<div class="card-body">

<form

method="POST"

action="update_announcement.php">

<input

type="hidden"

name="id"

value="<?php echo $row['id']; ?>">

<div class="mb-3">

<label class="form-label">

Announcement Title

</label>

<input

type="text"

name="title"

class="form-control"

required

value="<?php echo htmlspecialchars($row['title']); ?>">

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea

name="description"

class="form-control"

rows="8"

required><?php echo htmlspecialchars($row['description']); ?></textarea>

</div>

<div class="row">

<div class="col-md-6">

<label class="form-label">

Department

</label>

<select

name="department"

class="form-select">

<option value="Teacher"

<?php if($row['department']=="Teacher") echo "selected"; ?>>

Teacher

</option>

<option value="All"

<?php if($row['department']=="All") echo "selected"; ?>>

All Departments

</option>

</select>

</div>

<div class="col-md-6">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active"

<?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive"

<?php if($row['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

</div>

<hr>

<div class="text-end mt-4">

<button

type="submit"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Update Announcement

</button>

<a

href="manage_announcements.php"

class="btn btn-secondary ms-2">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>