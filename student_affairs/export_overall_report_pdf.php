<?php

require_once '../config/config.php';
require_once '../config/database.php';

require_once '../fpdf/fpdf.php';



/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}


if($_SESSION['department']!="Student Affairs"){

    header("Location: ../admins/login.php");
    exit;

}


$admin_name=$_SESSION['admin_name'];



/*=========================================
GET COUNTS
=========================================*/


$totalStudents=$conn->query("
SELECT COUNT(*) total 
FROM students
")->fetch_assoc()['total'];



$totalApplications=$conn->query("
SELECT COUNT(*) total 
FROM applications
")->fetch_assoc()['total'];



$totalCertificates=$conn->query("
SELECT COUNT(*) total 
FROM certificate_requests
")->fetch_assoc()['total'];



$totalAnnouncements=$conn->query("
SELECT COUNT(*) total 
FROM announcements
")->fetch_assoc()['total'];



$totalMessages=$conn->query("
SELECT COUNT(*) total 
FROM messages
")->fetch_assoc()['total'];



$totalComplaints=$conn->query("
SELECT COUNT(*) total 
FROM complaints
")->fetch_assoc()['total'];



$totalDocuments=$conn->query("
SELECT COUNT(*) total 
FROM application_documents
")->fetch_assoc()['total'];




/*=========================================
CREATE PDF
=========================================*/


$pdf=new FPDF('P','mm','A4');


$pdf->SetTitle("GTU Overall Report");


$pdf->SetAuthor("Global Tech University ERP");


$pdf->AddPage();



$pdf->SetAutoPageBreak(true,15);




/*=========================================
HEADER
=========================================*/


$pdf->SetFont('Arial','B',18);


$pdf->Cell(
0,
10,
'GLOBAL TECH UNIVERSITY',
0,
1,
'C'
);



$pdf->SetFont('Arial','',13);


$pdf->Cell(
0,
8,
'Student Affairs Office',
0,
1,
'C'
);



$pdf->Cell(
0,
8,
'Overall Performance Report',
0,
1,
'C'
);



$pdf->Ln(8);



$pdf->SetFont('Arial','',10);



$pdf->Cell(
95,
8,
'Generated Date : '.date("d-m-Y"),
0,
0
);



$pdf->Cell(
95,
8,
'Time : '.date("h:i A"),
0,
1,
'R'
);



$pdf->Ln(10);
//=========================================
// REPORT STATISTICS TABLE
//=========================================


$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Overall Statistics Summary',
0,
1
);


$pdf->Ln(3);



$pdf->SetFillColor(11,31,82);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',11);



$pdf->Cell(
80,
10,
'Module',
1,
0,
'C',
true
);


$pdf->Cell(
50,
10,
'Total Records',
1,
1,
'C',
true
);





// Reset Color

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',11);




// Students

$pdf->Cell(
80,
10,
'Total Students',
1,
0
);


$pdf->Cell(
50,
10,
$totalStudents,
1,
1,
'C'
);




// Applications

$pdf->Cell(
80,
10,
'Admission Applications',
1,
0
);


$pdf->Cell(
50,
10,
$totalApplications,
1,
1,
'C'
);




// Certificates

$pdf->Cell(
80,
10,
'Certificate Requests',
1,
0
);


$pdf->Cell(
50,
10,
$totalCertificates,
1,
1,
'C'
);




// Announcements

$pdf->Cell(
80,
10,
'Announcements',
1,
0
);


$pdf->Cell(
50,
10,
$totalAnnouncements,
1,
1,
'C'
);




// Messages

$pdf->Cell(
80,
10,
'Messages',
1,
0
);


$pdf->Cell(
50,
10,
$totalMessages,
1,
1,
'C'
);




// Complaints

$pdf->Cell(
80,
10,
'Complaints',
1,
0
);


$pdf->Cell(
50,
10,
$totalComplaints,
1,
1,
'C'
);




// Documents

$pdf->Cell(
80,
10,
'Application Documents',
1,
0
);


$pdf->Cell(
50,
10,
$totalDocuments,
1,
1,
'C'
);



$pdf->Ln(10);





//=========================================
// PERFORMANCE SUMMARY
//=========================================



$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Department Performance Summary',
0,
1
);



$pdf->SetFont('Arial','',11);



$pdf->MultiCell(
0,
8,
"Global Tech University Student Affairs Department manages complete student records, admission applications, certificate requests, communication records and academic documents through ERP system."
);



$pdf->Ln(5);



$pdf->SetFont('Arial','B',12);


$pdf->Cell(
0,
8,
'Current System Status : Active',
0,
1
);



$pdf->SetFont('Arial','',11);


$pdf->Cell(
0,
8,
'Academic Year : '.date("Y"),
0,
1
);



$pdf->Ln(8);


//=========================================
// LATEST STUDENTS
//=========================================


$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Latest Students',
0,
1
);



$pdf->SetFillColor(11,31,82);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',9);



$pdf->Cell(
10,
8,
'#',
1,
0,
'C',
true
);


$pdf->Cell(
45,
8,
'Name',
1,
0,
'C',
true
);


$pdf->Cell(
65,
8,
'Email',
1,
0,
'C',
true
);


$pdf->Cell(
35,
8,
'Mobile',
1,
0,
'C',
true
);


$pdf->Cell(
25,
8,
'Status',
1,
1,
'C',
true
);




$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',8);



$students=$conn->query("
SELECT *
FROM students
ORDER BY id DESC
LIMIT 5
");



$sr=1;


if($students && $students->num_rows>0){


while($row=$students->fetch_assoc()){



$name=$row['first_name']." ".$row['last_name'];


$pdf->Cell(
10,
8,
$sr++,
1,
0,
'C'
);



$pdf->Cell(
45,
8,
substr($name,0,25),
1,
0
);



$pdf->Cell(
65,
8,
substr($row['email'],0,35),
1,
0
);



$pdf->Cell(
35,
8,
$row['mobile'],
1,
0
);



$pdf->Cell(
25,
8,
$row['status'],
1,
1,
'C'
);



}


}else{


$pdf->Cell(
180,
8,
'No Student Records Found',
1,
1,
'C'
);


}



$pdf->Ln(10);




//=========================================
// LATEST ANNOUNCEMENTS
//=========================================


$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Latest Announcements',
0,
1
);



$pdf->SetFillColor(245,158,11);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',9);



$pdf->Cell(
10,
8,
'#',
1,
0,
'C',
true
);


$pdf->Cell(
80,
8,
'Title',
1,
0,
'C',
true
);


$pdf->Cell(
45,
8,
'Department',
1,
0,
'C',
true
);


$pdf->Cell(
45,
8,
'Date',
1,
1,
'C',
true
);



$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',8);



$announcements=$conn->query("
SELECT *
FROM announcements
ORDER BY id DESC
LIMIT 5
");



$sr=1;


if($announcements && $announcements->num_rows>0){


while($row=$announcements->fetch_assoc()){



$pdf->Cell(
10,
8,
$sr++,
1,
0,
'C'
);



$pdf->Cell(
80,
8,
substr($row['title'],0,35),
1,
0
);



$pdf->Cell(
45,
8,
$row['department'],
1,
0
);



$pdf->Cell(
45,
8,
date("d-m-Y",strtotime($row['created_at'])),
1,
1,
'C'
);



}



}else{


$pdf->Cell(
180,
8,
'No Announcement Found',
1,
1,
'C'
);


}



$pdf->Ln(10);


//=========================================
// LATEST MESSAGES
//=========================================


$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Latest Messages',
0,
1
);



$pdf->SetFillColor(124,58,237);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',9);



$pdf->Cell(
10,
8,
'#',
1,
0,
'C',
true
);


$pdf->Cell(
70,
8,
'Subject',
1,
0,
'C',
true
);


$pdf->Cell(
35,
8,
'Sender',
1,
0,
'C',
true
);


$pdf->Cell(
35,
8,
'Receiver',
1,
0,
'C',
true
);


$pdf->Cell(
30,
8,
'Status',
1,
1,
'C',
true
);



$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',8);



$messages=$conn->query("
SELECT *
FROM messages
ORDER BY id DESC
LIMIT 5
");



$sr=1;


if($messages && $messages->num_rows>0){


while($row=$messages->fetch_assoc()){



$pdf->Cell(
10,
8,
$sr++,
1,
0,
'C'
);



$pdf->Cell(
70,
8,
substr($row['subject'],0,35),
1,
0
);



$pdf->Cell(
35,
8,
$row['sender_role'],
1,
0
);



$pdf->Cell(
35,
8,
$row['receiver_role'],
1,
0
);



$status=($row['is_read']==1)?"Read":"Unread";


$pdf->Cell(
30,
8,
$status,
1,
1,
'C'
);



}



}else{


$pdf->Cell(
180,
8,
'No Messages Found',
1,
1,
'C'
);


}



$pdf->Ln(10);





//=========================================
// LATEST COMPLAINTS
//=========================================


$pdf->SetFont('Arial','B',14);


$pdf->Cell(
0,
10,
'Latest Complaints',
0,
1
);



$pdf->SetFillColor(220,38,38);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',9);



$pdf->Cell(
10,
8,
'#',
1,
0,
'C',
true
);


$pdf->Cell(
90,
8,
'Subject',
1,
0,
'C',
true
);


$pdf->Cell(
40,
8,
'Status',
1,
0,
'C',
true
);


$pdf->Cell(
40,
8,
'Date',
1,
1,
'C',
true
);



$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',8);



$complaints=$conn->query("
SELECT *
FROM complaints
ORDER BY id DESC
LIMIT 5
");



$sr=1;


if($complaints && $complaints->num_rows>0){


while($row=$complaints->fetch_assoc()){



$pdf->Cell(
10,
8,
$sr++,
1,
0,
'C'
);



$pdf->Cell(
90,
8,
substr($row['subject'],0,40),
1,
0
);



$pdf->Cell(
40,
8,
$row['status'],
1,
0,
'C'
);



$pdf->Cell(
40,
8,
date("d-m-Y",strtotime($row['created_at'])),
1,
1,
'C'
);



}



}else{


$pdf->Cell(
180,
8,
'No Complaints Found',
1,
1,
'C'
);


}



$pdf->Ln(12);




//=========================================
// GENERATED INFORMATION
//=========================================


$pdf->SetFont('Arial','B',12);


$pdf->Cell(
0,
8,
'Report Information',
0,
1
);



$pdf->SetFont('Arial','',10);



$pdf->Cell(
90,
8,
'Generated By : '.$admin_name,
0,
0
);



$pdf->Cell(
90,
8,
'Date : '.date("d-m-Y"),
0,
1
);



$pdf->Cell(
90,
8,
'Department : Student Affairs',
0,
0
);



$pdf->Cell(
90,
8,
'Time : '.date("h:i A"),
0,
1
);



$pdf->Ln(15);




//=========================================
// SIGNATURE AREA
//=========================================


$pdf->Line(
25,
$pdf->GetY(),
80,
$pdf->GetY()
);


$pdf->Line(
130,
$pdf->GetY(),
185,
$pdf->GetY()
);



$pdf->Ln(5);



$pdf->Cell(
55,
8,
'Prepared By',
0,
0,
'C'
);



$pdf->Cell(
50,
8,
'',
0,
0
);



$pdf->Cell(
55,
8,
'Student Affairs Officer',
0,
1,
'C'
);



$pdf->Ln(15);




//=========================================
// FOOTER
//=========================================


$pdf->SetFont('Arial','I',9);

$pdf->SetTextColor(100,100,100);



$pdf->Cell(
0,
6,
'Global Tech University ERP System',
0,
1,
'C'
);



$pdf->Cell(
0,
6,
'Student Affairs Office - Overall Report',
0,
1,
'C'
);



$pdf->Cell(
0,
6,
'© '.date("Y").' GTU. All Rights Reserved.',
0,
1,
'C'
);




//=========================================
// DOWNLOAD PDF
//=========================================


$fileName="GTU_Overall_Report_".date("Ymd_His").".pdf";


$pdf->Output(
"D",
$fileName
);


exit;


?>