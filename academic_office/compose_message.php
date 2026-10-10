<?php

session_start();


require_once '../config/config.php';
require_once '../config/database.php';



if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}



if($_SESSION['department']!="Academic Office"){

    header("Location: ../admins/login.php");
    exit;

}



$page_title="Compose Message";



?>



<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">



<title>

<?php echo $page_title; ?>

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}



.card{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.header-title{

color:#0d6efd;

font-size:28px;

font-weight:700;

}



</style>


</head>



<body>



<div class="container py-4">



<div class="card">



<div class="card-header bg-primary text-white">


<h4>

<i class="fa-solid fa-paper-plane"></i>

Compose New Message

</h4>


</div>



<div class="card-body">



<form id="messageForm">



<input type="hidden"
name="sender_department"
value="Academic Office">





<div class="mb-3">


<label class="fw-bold">

Send To

</label>



<select 
class="form-select"
id="receiver_type"
name="receiver_type">


<option value="">

Select Receiver

</option>


<option value="Super Admin">

Super Admin

</option>


<option value="Teacher">

Teacher

</option>



</select>


</div>





<div class="mb-3"
id="teacher_box"
style="display:none;">



<label class="fw-bold">

Select Teacher

</label>


<select 
class="form-select"
id="teacher_id"
name="receiver_id">


<option value="">

Select Teacher

</option>



</select>



</div>





<div class="mb-3">


<label class="fw-bold">

Subject

</label>


<input type="text"
class="form-control"
name="subject"
placeholder="Enter Subject">


</div>





<div class="mb-3">


<label class="fw-bold">

Message

</label>


<textarea 
class="form-control"
name="message"
rows="6"
placeholder="Write Message"></textarea>


</div>





<button 
type="submit"
class="btn btn-success">


<i class="fa-solid fa-paper-plane"></i>

Send Message


</button>



</form>



</div>


</div>


</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<script>


/*=========================================
LOAD TEACHERS
=========================================*/


$("#receiver_type").change(function(){


let type=$(this).val();


if(type=="Teacher"){


$("#teacher_box").show();

$.ajax({


url:"ajax/get_all_teachers.php",

type:"POST",

dataType:"json",



success:function(response){
$("#teacher_id").html(

'<option value="">Select Teacher</option>'

);
if(response.status=="success"){
$.each(response.data,function(i,teacher){
$("#teacher_id").append(


'<option value="'+teacher.id+'">'+


teacher.employee_id+

' | '+
teacher.teacher_name+


'</option>'

);
});
}

}

});



}else{



$("#teacher_box").hide();


$("#teacher_id").html(

'<option value="">Select Teacher</option>'

);



}



});







/*=========================================
SEND MESSAGE
=========================================*/


$("#messageForm").submit(function(e){


e.preventDefault();



let receiver_type=$("#receiver_type").val();



let receiver_id=0;



if(receiver_type=="Teacher"){


receiver_id=$("#teacher_id").val();


if(receiver_id==""){


alert("Please Select Teacher");

return;


}



}



$.ajax({


url:"ajax/send_message.php",

type:"POST",


data:$(this).serialize()+"&receiver_id="+receiver_id,


dataType:"json",



success:function(response){



if(response.status=="success"){


alert(response.message);



$("#messageForm")[0].reset();


$("#teacher_box").hide();



}else{


alert(response.message);



}



},



error:function(xhr){


console.log(xhr.responseText);

alert("AJAX Error");

}



});



});



</script>



</body>


</html>