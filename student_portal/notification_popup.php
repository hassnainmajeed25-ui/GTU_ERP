<?php

/*=========================================
GET LATEST NOTIFICATION
=========================================*/

$notification=$conn->prepare("

SELECT *

FROM super_admin_notifications

WHERE status='Active'

ORDER BY id DESC

LIMIT 1

");

$notification->execute();

$notificationResult=$notification->get_result();

if($notificationResult->num_rows>0){

$row=$notificationResult->fetch_assoc();

?>

<style>

/*==============================
BACKGROUND
==============================*/

.notification-overlay{

position:fixed;

top:0;

left:0;

width:100%;

height:100%;

background:rgba(0,0,0,.65);

display:flex;

justify-content:center;

align-items:center;

z-index:99999;

backdrop-filter:blur(4px);

}

/*==============================
POPUP
==============================*/

.notification-popup{

width:600px;

max-width:92%;

background:white;

border-radius:22px;

overflow:hidden;

animation:popup .4s ease;

box-shadow:0 15px 40px rgba(0,0,0,.35);

}

@keyframes popup{

from{

transform:scale(.7);

opacity:0;

}

to{

transform:scale(1);

opacity:1;

}

}

/*==============================
HEADER
==============================*/

.popup-header{

background:linear-gradient(135deg,#000000,#0B3D91,#2563eb);

padding:20px 25px;

color:#fff;

display:flex;

justify-content:space-between;

align-items:center;

font-size:22px;

font-weight:bold;

}

.close-top-btn{

width:42px;

height:42px;

border:none;

border-radius:50%;

background:#FFD54F;

color:#000;

font-size:18px;

cursor:pointer;

display:flex;

justify-content:center;

align-items:center;

transition:.3s;

}

.close-top-btn:hover{

background:#FFC107;

transform:rotate(90deg);

box-shadow:0 5px 15px rgba(0,0,0,.25);

}

.popup-header i{

font-size:42px;

color:#FFD54F;

margin-bottom:10px;

}

.popup-header h3{

margin:0;

font-weight:700;

letter-spacing:.5px;

}

.close-top-btn i{

color:#000;

font-size:35px;

font-weight:bold;

}

/*==============================
BODY
==============================*/

.popup-body{

padding:25px;

background:#ffffff;

}

.notification-type{

display:inline-block;

background:#FFD54F;

color:#000;

font-weight:bold;

padding:8px 18px;

border-radius:30px;

margin-bottom:18px;

box-shadow:0 3px 8px rgba(0,0,0,.15);

}

.notification-title{

font-size:34px;

font-weight:900;

color:#0B3D91;

margin-bottom:20px;

line-height:1.3;

text-align:center;

text-transform:capitalize;

}

.notification-message{

font-size:17px;

line-height:1.9;

color:#444;

background:#f8f9fa;

padding:18px;

border-left:6px solid #0B3D91;

border-radius:12px;

white-space:pre-line;

}

/*==============================
FOOTER
==============================*/

.popup-footer{

background:#f5f5f5;

padding:18px 25px;

display:flex;

justify-content:space-between;

align-items:center;

border-top:1px solid #ddd;

}

.notification-date{

font-size:15px;

font-weight:600;

color:#666;

}

@media(max-width:768px){

.notification-popup{

width:95%;

}

.notification-title{

font-size:22px;

}

.notification-message{

font-size:15px;

}

}

</style>

<div class="notification-overlay" id="notificationPopup">

<div class="notification-popup">

<div class="popup-header">

<div>

<i class="fas fa-bell me-2"></i>

University Notification

</div>

<button
class="close-top-btn"
onclick="closeNotification()">

<i class="fas fa-times"></i>

</button>

</div>

<div class="popup-body">

<span class="notification-type">

<?php echo htmlspecialchars($row['notification_type']); ?>

</span>

<div class="notification-title">

<?php echo htmlspecialchars($row['title']); ?>

</div>

<div class="notification-message">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</div>

</div>

<div class="popup-footer">

<div class="notification-date">

<i class="fas fa-calendar-alt me-2"></i>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</div>

</div>

</div>

</div>

<script>

function closeNotification(){

document.getElementById("notificationPopup").style.display="none";

}

</script>

<?php

}

?>