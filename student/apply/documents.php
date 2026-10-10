<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$app = $conn->prepare("SELECT id FROM applications WHERE student_id=? LIMIT 1");
$app->bind_param("i",$student_id);
$app->execute();

$result = $app->get_result();

if($result->num_rows==0){
    header("Location: personal.php");
    exit;
}

$row = $result->fetch_assoc();
$app_id = $row['id'];

if(isset($_POST['save_continue'])){

    function uploadFile($field,$folder){

        if(isset($_FILES[$field]) && $_FILES[$field]['error']==0){

            $allowed = ['jpg','jpeg','png','pdf'];

            $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

            if(!in_array($extension,$allowed)){
                return "";
            }

            $newName = time().'_'.uniqid().'_'.$field.'.'.$extension;

            $target = "../../uploads/".$folder."/".$newName;

            if(move_uploaded_file($_FILES[$field]['tmp_name'],$target)){
                return $newName;
            }

        }

        return "";

    }

    $student_photo = uploadFile("student_photo","student_photos");

    $student_cnic = uploadFile("student_cnic","student_cnic");

    $father_cnic = uploadFile("father_cnic","father_cnic");

    $matric_result = uploadFile("matric_result","matric_result");

    $inter_result = uploadFile("inter_result","inter_result");

    $check = $conn->prepare("SELECT id FROM application_documents WHERE application_id=?");

    $check->bind_param("i",$app_id);

    $check->execute();

    $res = $check->get_result();

    if($res->num_rows>0){

        $update = $conn->prepare("UPDATE application_documents SET

        student_photo=?,
        student_cnic=?,
        father_cnic=?,
        matric_result_card=?,
        inter_result_card=?

        WHERE application_id=?");

        $update->bind_param(

        "sssssi",

        $student_photo,
        $student_cnic,
        $father_cnic,
        $matric_result,
        $inter_result,
        $app_id

        );

        $update->execute();

    }else{

        $insert = $conn->prepare("INSERT INTO application_documents(

        application_id,
        student_photo,
        student_cnic,
        father_cnic,
        matric_result_card,
        inter_result_card

        )

        VALUES(?,?,?,?,?,?)");

        $insert->bind_param(

        "isssss",

        $app_id,
        $student_photo,
        $student_cnic,
        $father_cnic,
        $matric_result,
        $inter_result

        );

        $insert->execute();

    }

    header("Location: review.php");
    exit;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admission | Upload Documents</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f4f7fb;
}

.topbar{
background:#0B1F52;
color:white;
padding:18px;
}

.progress{
height:10px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-header{
background:#0B1F52;
color:#fff;
font-size:22px;
font-weight:bold;
}

.upload-box{
border:2px dashed #ced4da;
border-radius:12px;
padding:20px;
margin-bottom:20px;
background:#fff;
}

.upload-box h5{
margin-bottom:15px;
color:#0B1F52;
}

.form-control{
height:50px;
}

.preview{
margin-top:10px;
max-width:180px;
display:none;
border-radius:10px;
border:1px solid #ddd;
}

.btn-next{
background:#FDB913;
border:none;
height:55px;
border-radius:50px;
font-weight:bold;
font-size:18px;
}

.btn-next:hover{
background:#e3a700;
}

</style>

</head>

<body>

<div class="topbar">

<div class="container">

<h3>Global Tech University (GTU)</h3>

</div>

</div>

<div class="container mt-4">

<div class="progress mb-4">

<div class="progress-bar bg-success" style="width:80%"></div>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-file-upload me-2"></i>

Upload Required Documents

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="upload-box">

<h5>Passport Size Photograph</h5>

<input
type="file"
name="student_photo"
class="form-control"
accept=".jpg,.jpeg,.png"
onchange="previewImage(this,'photoPreview')"
required>

<img id="photoPreview" class="preview">

</div>

<div class="upload-box">

<h5>Student CNIC / B-Form</h5>

<input
type="file"
name="student_cnic"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

</div>

<div class="upload-box">

<h5>Father CNIC</h5>

<input
type="file"
name="father_cnic"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

</div>

<div class="upload-box">

<h5>Matric Result Card</h5>

<input
type="file"
name="matric_result"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

</div>

<div class="upload-box">

<h5>Intermediate Result Card (1st Year)</h5>

<input
type="file"
name="inter_result"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

</div>

<div class="d-flex justify-content-between">

<a href="program.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Previous

</a>

<button
type="submit"
name="save_continue"
class="btn btn-next px-5">

Save & Continue

<i class="fas fa-arrow-right ms-2"></i>

</button>

</div>

</form>

</div>

</div>

</div>
<script>

function previewImage(input, previewId){

    const file = input.files[0];

    if(!file){
        return;
    }

    const allowed = ['image/jpeg','image/png','image/jpg'];

    if(!allowed.includes(file.type)){

        alert("Only JPG, JPEG and PNG images are allowed.");

        input.value="";

        return;

    }

    if(file.size > 2 * 1024 * 1024){

        alert("Image size must be less than 2 MB.");

        input.value="";

        return;

    }

    const reader = new FileReader();

    reader.onload=function(e){

        let img=document.getElementById(previewId);

        img.src=e.target.result;

        img.style.display="block";

    }

    reader.readAsDataURL(file);

}


/*=============================
PDF + IMAGE VALIDATION
=============================*/

document.querySelectorAll("input[type=file]").forEach(function(input){

input.addEventListener("change",function(){

if(this.files.length==0){

return;

}

let file=this.files[0];

let allowed=[

"image/jpeg",

"image/png",

"application/pdf"

];

if(!allowed.includes(file.type)){

alert("Only JPG, PNG and PDF files are allowed.");

this.value="";

return;

}

if(file.size > 2*1024*1024){

alert("Maximum file size is 2 MB.");

this.value="";

return;

}

});

});

</script>

</body>

</html>