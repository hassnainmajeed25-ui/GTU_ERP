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

$admin_id=$_SESSION['admin_id'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Teacher Announcement</title>

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

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-bullhorn me-2"></i>

Add Teacher Announcement

</h3>

</div>

<div class="card-body">

<form method="POST" action="save_announcement.php">

<div class="mb-3">

<label class="form-label">

Announcement Title

</label>

<input

type="text"

name="title"

class="form-control"

required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea

name="description"

class="form-control"

rows="8"

required></textarea>

</div>

<div class="row">

<div class="col-md-6">

<label class="form-label">

Department

</label>

<select

name="department"

class="form-select"

required>

<option value="Teacher" selected>

Teacher

</option>

<option value="All">

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

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

</div>

<hr>

<div class="text-end">

<button

type="submit"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Publish Announcement

</button>

<a

href="dashboard.php"

class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</body>

</html>