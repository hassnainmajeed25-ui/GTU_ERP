<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

/*=========================================
CHECK REQUEST
=========================================*/

if(
    !isset($_POST['teacher_id']) ||
    !isset($_POST['subject_id']) ||
    !isset($_POST['program_id']) ||
    !isset($_POST['semester']) ||
    !isset($_POST['shift']) ||
    !isset($_POST['admin_id'])
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Required Data Missing"
    ]);

    exit;
}

$teacher_id = (int)$_POST['teacher_id'];
$subject_id = (int)$_POST['subject_id'];
$program_id = (int)$_POST['program_id'];
$semester   = (int)$_POST['semester'];
$shift      = $_POST['shift'];
$admin_id   = (int)$_POST['admin_id'];

/*=========================================
CHECK TEACHER
=========================================*/

$sql = "
SELECT id
FROM teachers
WHERE id=?
LIMIT 1
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $teacher_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    echo json_encode([
        "status"=>"error",
        "message"=>"Teacher Not Found"
    ]);

    exit;
}


/*=========================================
CHECK SUBJECT
=========================================*/

$sql = "
SELECT id
FROM subjects
WHERE id=?
LIMIT 1
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $subject_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    echo json_encode([
        "status"=>"error",
        "message"=>"Subject Not Found"
    ]);

    exit;
}


/*=========================================
DUPLICATE CHECK
=========================================*/
$sqlCheckTeacher = "

SELECT teacher_id

FROM teacher_subjects

WHERE teacher_id=?
AND subject_id=?
AND program_id=?
AND semester=?
AND shift=?
AND status='Active'

LIMIT 1

";

/*=========================================
CHECK SUBJECT ALREADY HAS ANOTHER TEACHER
=========================================*/

$sql = "

SELECT teacher_id
FROM teacher_subjects
WHERE subject_id=?
AND program_id=?
AND semester=?
AND shift=?
AND status='Active'
LIMIT 1

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "iiis",
    $subject_id,
    $program_id,
    $semester,
    $shift
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0){

    $row = mysqli_fetch_assoc($result);

    if($row['teacher_id'] != $teacher_id){

        echo json_encode([
            "status"=>"error",
            "message"=>"This subject is already assigned to another teacher."
        ]);

        exit;
    }
}

$sql = "

SELECT id

FROM teacher_subjects

WHERE teacher_id=?
AND subject_id=?
AND program_id=?
AND semester=?
AND shift=?
AND status='Active'

LIMIT 1

";
$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(

$stmt,

"iiiis",

$teacher_id,
$subject_id,
$program_id,
$semester,
$shift

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0){

    echo json_encode([
        "status"=>"duplicate",
        "message"=>"This Subject Already Assigned"
    ]);

    exit;
}

/*=========================================
CHECK SUBJECT BELONGS TO PROGRAM & SEMESTER
=========================================*/

$sql = "
SELECT id
FROM subjects
WHERE id=?
AND program_id=?
AND semester=?
LIMIT 1
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "iii",
    $subject_id,
    $program_id,
    $semester
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    echo json_encode([
        "status"=>"error",
        "message"=>"Selected Subject does not belong to selected Program or Semester."
    ]);

    exit;
}

/*=========================================
CHECK TEACHER FACULTY & SHIFT
=========================================*/

$sql = "
SELECT
    t.faculty_id,
    t.shift,
    p.faculty_id AS program_faculty
FROM teachers t
INNER JOIN programs p
ON p.id = ?
WHERE t.id = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $program_id,
    $teacher_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

if(!$row){

    echo json_encode([
        "status"=>"error",
        "message"=>"Teacher or Program not found."
    ]);

    exit;
}

/* Faculty Check */

if($row['faculty_id'] != $row['program_faculty']){

    echo json_encode([
        "status"=>"error",
        "message"=>"Teacher belongs to another Faculty."
    ]);

    exit;
}

/* Shift Check */

if($row['shift'] != $shift){

    echo json_encode([
        "status"=>"error",
        "message"=>"Teacher shift does not match selected shift."
    ]);

    exit;
}

/*=========================================
START TRANSACTION
=========================================*/

mysqli_begin_transaction($conn);

try{

    /*=========================================
    INSERT ASSIGNMENT
    =========================================*/

    $sql = "

    INSERT INTO teacher_subjects(

teacher_id,
subject_id,
program_id,
semester,
shift,
assigned_by,
status

)

VALUES(

?,
?,
?,
?,
?,
?,
'Active'

)

    ";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(

$stmt,

"iiiisi",

$teacher_id,
$subject_id,
$program_id,
$semester,
$shift,
$admin_id

);

    mysqli_stmt_execute($stmt);


    /*=========================================
    UPDATE SUBJECT TABLE
    =========================================*/

    $sql = "

    UPDATE subjects

    SET teacher_id=?

    WHERE id=?

    ";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(

        $stmt,

        "ii",

        $teacher_id,
        $subject_id

    );

    mysqli_stmt_execute($stmt);


    mysqli_commit($conn);

    echo json_encode([

        "status"=>"success",

        "message"=>"Subject Assigned Successfully"

    ]);

}
catch(Exception $e){

    mysqli_rollback($conn);

    echo json_encode([

        "status"=>"error",

        "message"=>$e->getMessage()

    ]);

}

?>