<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================================
  LOGIN SECURITY
=========================================================*/

if (!isset($_SESSION['student_id'])) {

    header("Location: login.php");
    exit;

}

$student_id = (int) $_SESSION['student_id'];


/*=========================================================
  LOAD STUDENT INFORMATION
=========================================================*/

$sql = "

SELECT

    students.*,

    programs.program_name,
    programs.program_code,

    faculties.faculty_name

FROM students

LEFT JOIN programs
    ON students.program_id = programs.id

LEFT JOIN faculties
    ON programs.faculty_id = faculties.id

WHERE students.id = ?

LIMIT 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die("Student Query Error: " . $conn->error);

}

$stmt->bind_param("i", $student_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows == 0) {

    session_destroy();

    header("Location: login.php");
    exit;

}


$student = $result->fetch_assoc();

$page_title = "Attendance";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>

Attendance | GTU ERP

</title>


<!-- Bootstrap -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<!-- Font Awesome -->

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    rel="stylesheet"
>


<style>

/*=========================================================
  GLOBAL
=========================================================*/

* {

    margin: 0;
    padding: 0;
    box-sizing: border-box;

}


body {

    font-family: 'Segoe UI', sans-serif;

    background: #FFF7EE;

    overflow-x: hidden;

}


/*=========================================================
  SIDEBAR
=========================================================*/

.sidebar {

    position: fixed;

    left: 0;
    top: 0;

    width: 270px;
    height: 100vh;

    background: #5D4037;

    overflow-y: auto;

    box-shadow: 4px 0 20px rgba(0,0,0,.15);

}


.logo {

    padding: 25px;

    text-align: center;

    border-bottom: 1px solid rgba(255,255,255,.12);

}


.logo img {

    width: 80px;

    margin-bottom: 10px;

}


.logo h4 {

    color: #FFF8E1;

    font-weight: bold;

    margin: 0;

}


.logo small {

    color: #FFE082;

}


.sidebar ul {

    list-style: none;

    padding: 20px 0;

    margin: 0;

}


.sidebar ul li {

    margin-bottom: 5px;

}


.sidebar ul li a {

    display: block;

    padding: 15px 25px;

    text-decoration: none;

    color: #fff;

    transition: .3s;

    font-size: 15px;

}


.sidebar ul li a i {

    width: 28px;

}


.sidebar ul li a:hover {

    background: #FFD54F;

    color: #4E342E;

    padding-left: 35px;

}


.sidebar ul li a.active {

    background: #FFF59D;

    color: #4E342E;

    font-weight: bold;

}


/*=========================================================
  MAIN
=========================================================*/

.main {

    margin-left: 270px;

    padding: 30px;

}


/*=========================================================
  HEADER CARD
=========================================================*/

.header-card {

    background: linear-gradient(
        135deg,
        #FFE0B2,
        #FFF59D
    );

    border-radius: 18px;

    padding: 25px;

    box-shadow: 0 6px 18px rgba(0,0,0,.08);

    margin-bottom: 25px;

}


.header-card h2 {

    color: #5D4037;

    font-weight: bold;

    margin-bottom: 8px;

}


.header-card p {

    margin: 0;

    color: #6D4C41;

}


/*=========================================================
  SUBJECT CARD
=========================================================*/

.subject-card {

    background: #fff;

    border-radius: 16px;

    padding: 22px;

    box-shadow: 0 5px 18px rgba(0,0,0,.07);

    margin-bottom: 20px;

}


/*=========================================================
  TABLE
=========================================================*/

.table {

    margin-bottom: 0;

}


.table thead th {

    white-space: nowrap;

    font-size: 14px;

}


.table tbody td {

    font-size: 14px;

    vertical-align: middle;

}


/*=========================================================
  TEACHER
=========================================================*/

.teacher-box {

    display: flex;

    align-items: center;

    gap: 10px;

}


.teacher-box img {

    width: 45px;

    height: 45px;

    border-radius: 50%;

    object-fit: cover;

    border: 2px solid #eeeeee;

}


.teacher-box strong {

    color: #3E2723;

}


/*=========================================================
  RESPONSIVE
=========================================================*/

@media (max-width: 992px) {

    .sidebar {

        width: 220px;

    }

    .main {

        margin-left: 220px;

        padding: 20px;

    }

}


@media (max-width: 768px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

    }

    .main {

        margin-left: 0;

        padding: 15px;

    }

    .sidebar ul {

        padding: 10px 0;

    }

}


/*=========================================================
  FOOTER
=========================================================*/

footer {

    margin-bottom: 20px;

}

</style>

</head>


<body>


<!--=========================================================
  SIDEBAR
=========================================================-->

<div class="sidebar">


    <div class="logo">

        <img
            src="../assets/images/logo.png"
            alt="GTU Logo"
        >

        <h4>

            GTU ERP

        </h4>

        <small>

            Student Portal

        </small>

    </div>


    <ul>


        <li>

            <a href="dashboard.php">

                <i class="fas fa-house"></i>

                Dashboard

            </a>

        </li>


        <li>

            <a href="profile.php">

                <i class="fas fa-user"></i>

                My Profile

            </a>

        </li>


        <li>

            <a
                href="attendance.php"
                class="active"
            >

                <i class="fas fa-calendar-check"></i>

                Attendance

            </a>

        </li>


        <li>

            <a href="results.php">

                <i class="fas fa-square-poll-vertical"></i>

                Results

            </a>

        </li>


        <li>

            <a href="fee_status.php">

                <i class="fas fa-money-check-dollar"></i>

                Fee Status

            </a>

        </li>


        <li>

            <a href="messages.php">

                <i class="fas fa-envelope"></i>

                Messages

            </a>

        </li>


        <li>

            <a href="logout.php">

                <i class="fas fa-right-from-bracket"></i>

                Logout

            </a>

        </li>


    </ul>

</div>


<!--=========================================================
  MAIN
=========================================================-->

<div class="main">


    <!--=====================================================
      HEADER
    =====================================================-->

    <div class="header-card">

        <h2>

            <i class="fas fa-calendar-check"></i>

            Attendance Management

        </h2>

        <p>

            View your complete attendance record for all
            registered subjects.

        </p>

    </div>


<?php

/*=========================================================
  OVERALL ATTENDANCE SUMMARY
=========================================================*/

$summaryQuery = $conn->prepare("

    SELECT

        COUNT(attendance_details.id) AS total_lectures,

        SUM(
            CASE
                WHEN attendance_details.status = 'Present'
                THEN 1
                ELSE 0
            END
        ) AS presents,

        SUM(
            CASE
                WHEN attendance_details.status = 'Absent'
                THEN 1
                ELSE 0
            END
        ) AS absents,

        SUM(
            CASE
                WHEN attendance_details.status = 'Late'
                THEN 1
                ELSE 0
            END
        ) AS lates,

        SUM(
            CASE
                WHEN attendance_details.status = 'Leave'
                THEN 1
                ELSE 0
            END
        ) AS leaves

    FROM attendance_details

    INNER JOIN attendance

        ON attendance.id = attendance_details.attendance_id

    WHERE attendance_details.student_id = ?

");


if (!$summaryQuery) {

    die("Attendance Summary Error: " . $conn->error);

}


$summaryQuery->bind_param(
    "i",
    $student_id
);


$summaryQuery->execute();


$summary = $summaryQuery
    ->get_result()
    ->fetch_assoc();


$totalLectures = (int) (
    $summary['total_lectures'] ?? 0
);


$presents = (int) (
    $summary['presents'] ?? 0
);


$absents = (int) (
    $summary['absents'] ?? 0
);


$lates = (int) (
    $summary['lates'] ?? 0
);


$leaves = (int) (
    $summary['leaves'] ?? 0
);


/*=========================================================
  OVERALL PERCENTAGE
=========================================================*/

if ($totalLectures > 0) {

    $attendancePercentage = round(
        ($presents / $totalLectures) * 100,
        2
    );

} else {

    $attendancePercentage = 0;

}

?>


    <!--=====================================================
      SUMMARY CARDS
    =====================================================-->

    <div class="row">


        <!-- TOTAL -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <i
                        class="fas fa-calendar-day fa-3x text-dark mb-3"
                    ></i>

                    <h6>

                        Total Lectures

                    </h6>

                    <h2 class="fw-bold">

                        <?php
                        echo $totalLectures;
                        ?>

                    </h2>

                </div>

            </div>

        </div>


        <!-- PRESENT -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <i
                        class="fas fa-circle-check fa-3x text-success mb-3"
                    ></i>

                    <h6>

                        Present

                    </h6>

                    <h2 class="fw-bold text-success">

                        <?php
                        echo $presents;
                        ?>

                    </h2>

                </div>

            </div>

        </div>


        <!-- ABSENT -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <i
                        class="fas fa-circle-xmark fa-3x text-danger mb-3"
                    ></i>

                    <h6>

                        Absent

                    </h6>

                    <h2 class="fw-bold text-danger">

                        <?php
                        echo $absents;
                        ?>

                    </h2>

                </div>

            </div>

        </div>


        <!-- LEAVE -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <i
                        class="fas fa-person-walking-arrow-right fa-3x text-warning mb-3"
                    ></i>

                    <h6>

                        Leave

                    </h6>

                    <h2 class="fw-bold text-warning">

                        <?php
                        echo $leaves;
                        ?>

                    </h2>

                </div>

            </div>

        </div>


    </div>


    <!--=====================================================
      ATTENDANCE PERCENTAGE
    =====================================================-->

    <div class="subject-card">


        <h4 class="mb-4">

            Overall Attendance Percentage

        </h4>


        <div class="progress mb-3">

            <div

                class="progress-bar"

                role="progressbar"

                style="
                    width: <?php
                    echo $attendancePercentage;
                    ?>%;
                "

            >

                <?php
                echo $attendancePercentage;
                ?>%

            </div>

        </div>


<?php

/*=========================================================
  OVERALL STATUS
=========================================================*/

if ($attendancePercentage >= 90) {

    $status = "Excellent";

    $color = "success";

} elseif ($attendancePercentage >= 80) {

    $status = "Good";

    $color = "primary";

} elseif ($attendancePercentage >= 75) {

    $status = "Average";

    $color = "warning";

} else {

    $status = "Short Attendance";

    $color = "danger";

}

?>


        <div class="alert alert-<?php echo $color; ?>">

            <strong>

                Status :

            </strong>

            <?php
            echo $status;
            ?>

        </div>


    </div>


    <!--=====================================================
      SUBJECT WISE ATTENDANCE
    =====================================================-->

    <div class="subject-card mt-4">


        <h3 class="mb-4">

            <i class="fas fa-book-open"></i>

            Subject Wise Attendance

        </h3>


        <div class="table-responsive">


            <table
                class="table table-bordered table-hover align-middle"
            >


                <thead class="table-warning">

                    <tr>

                        <th width="60">

                            #

                        </th>

                        <th>

                            Teacher

                        </th>

                        <th>

                            Subject

                        </th>

                        <th>

                            Total

                        </th>

                        <th>

                            Present

                        </th>

                        <th>

                            Absent

                        </th>

                        <th>

                            Late

                        </th>

                        <th>

                            Leave

                        </th>

                        <th>

                            %

                        </th>

                        <th>

                            Status

                        </th>

                        <th width="130">

                            Action

                        </th>

                    </tr>

                </thead>


                <tbody>


<?php

/*=========================================================
  SUBJECT QUERY

  IMPORTANT:

  One Subject = One Teacher

  We select MAX(teacher_subjects.id)

  This means the latest/last active teacher
  assigned to each subject will be shown.

  Example:

  ENG101
  - Imran
  - Naveed
  - Rizwan

  Result:

  ENG101 → Rizwan

=========================================================*/

$subjectQuery = $conn->prepare("

    SELECT

        subjects.id,

        subjects.subject_name,

        subjects.subject_code,

        teachers.teacher_name,

        teachers.profile_photo

    FROM subjects

    INNER JOIN teacher_subjects

        ON teacher_subjects.id = (

            SELECT

                MAX(ts.id)

            FROM teacher_subjects AS ts

            WHERE

                ts.subject_id = subjects.id

                AND ts.status = 'Active'

        )

    INNER JOIN teachers

        ON teachers.id = teacher_subjects.teacher_id

    WHERE

        subjects.program_id = ?

        AND subjects.semester = ?

        AND subjects.subject_name IS NOT NULL

        AND TRIM(subjects.subject_name) <> ''

    ORDER BY

        subjects.subject_code ASC

");


if (!$subjectQuery) {

    die(
        "Subject Query Error: " .
        $conn->error
    );

}


$subjectQuery->bind_param(

    "ii",

    $student['program_id'],

    $student['semester']

);


$subjectQuery->execute();


$subjects = $subjectQuery
    ->get_result();


$sr = 1;


/*=========================================================
  SUBJECT LOOP
=========================================================*/

while ($subject = $subjects->fetch_assoc()) {


    /*=====================================================
      SUBJECT ATTENDANCE
    =====================================================*/

    $att = $conn->prepare("

        SELECT

            COUNT(attendance_details.id) AS total,

            SUM(
                CASE
                    WHEN attendance_details.status = 'Present'
                    THEN 1
                    ELSE 0
                END
            ) AS presents,

            SUM(
                CASE
                    WHEN attendance_details.status = 'Absent'
                    THEN 1
                    ELSE 0
                END
            ) AS absents,

            SUM(
                CASE
                    WHEN attendance_details.status = 'Late'
                    THEN 1
                    ELSE 0
                END
            ) AS lates,

            SUM(
                CASE
                    WHEN attendance_details.status = 'Leave'
                    THEN 1
                    ELSE 0
                END
            ) AS leaves

        FROM attendance

        INNER JOIN attendance_details

            ON attendance.id =
               attendance_details.attendance_id

        WHERE

            attendance.subject_id = ?

            AND attendance_details.student_id = ?

    ");


    if (!$att) {

        die(
            "Subject Attendance Error: " .
            $conn->error
        );

    }


    $att->bind_param(

        "ii",

        $subject['id'],

        $student_id

    );


    $att->execute();


    $row = $att
        ->get_result()
        ->fetch_assoc();


    /*=====================================================
      ATTENDANCE VALUES
    =====================================================*/

    $total = (int) (
        $row['total'] ?? 0
    );


    $present = (int) (
        $row['presents'] ?? 0
    );


    $absent = (int) (
        $row['absents'] ?? 0
    );


    $late = (int) (
        $row['lates'] ?? 0
    );


    $leave = (int) (
        $row['leaves'] ?? 0
    );


    /*=====================================================
      SUBJECT PERCENTAGE
    =====================================================*/

    if ($total > 0) {

        $percent = round(
            ($present / $total) * 100,
            2
        );

    } else {

        $percent = 0;

    }


    /*=====================================================
      SUBJECT STATUS
    =====================================================*/

    if ($percent >= 90) {

        $badge = "success";

        $text = "Excellent";

    } elseif ($percent >= 80) {

        $badge = "primary";

        $text = "Good";

    } elseif ($percent >= 75) {

        $badge = "warning";

        $text = "Average";

    } else {

        $badge = "danger";

        $text = "Short";

    }


    /*=====================================================
      TEACHER IMAGE
    =====================================================*/

    $profilePhoto =
        $subject['profile_photo'] ?? '';


    $image =
        "../assets/images/default-user.png";


    if (!empty($profilePhoto)) {

        $teacherImage =
            "../uploads/teachers/" .
            basename($profilePhoto);


        if (file_exists($teacherImage)) {

            $image = $teacherImage;

        }

    }

?>


                    <tr>


                        <!-- NUMBER -->

                        <td>

                            <?php
                            echo $sr++;
                            ?>

                        </td>


                        <!-- TEACHER -->

                        <td>

                            <div class="teacher-box">


                                <img

                                    src="<?php
                                    echo htmlspecialchars($image);
                                    ?>"

                                    alt="Teacher"

                                >


                                <div>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(

                                            $subject[
                                                'teacher_name'
                                            ]

                                        );

                                        ?>

                                    </strong>

                                </div>


                            </div>

                        </td>


                        <!-- SUBJECT -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(

                                    $subject[
                                        'subject_name'
                                    ]

                                );

                                ?>

                            </strong>


                            <br>


                            <small class="text-muted">

                                <?php

                                echo htmlspecialchars(

                                    $subject[
                                        'subject_code'
                                    ]

                                );

                                ?>

                            </small>

                        </td>


                        <!-- TOTAL -->

                        <td>

                            <?php
                            echo $total;
                            ?>

                        </td>


                        <!-- PRESENT -->

                        <td class="text-success fw-bold">

                            <?php
                            echo $present;
                            ?>

                        </td>


                        <!-- ABSENT -->

                        <td class="text-danger fw-bold">

                            <?php
                            echo $absent;
                            ?>

                        </td>


                        <!-- LATE -->

                        <td class="text-warning fw-bold">

                            <?php
                            echo $late;
                            ?>

                        </td>


                        <!-- LEAVE -->

                        <td class="text-primary fw-bold">

                            <?php
                            echo $leave;
                            ?>

                        </td>


                        <!-- PERCENTAGE -->

                        <td>

                            <?php
                            echo $percent;
                            ?>%

                        </td>


                        <!-- STATUS -->

                        <td>

                            <span
                                class="badge bg-<?php
                                echo $badge;
                                ?>"
                            >

                                <?php
                                echo $text;
                                ?>

                            </span>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <a

                                href="attendance_details.php?subject_id=<?php
                                echo (int) $subject['id'];
                                ?>"

                                class="btn btn-sm btn-dark"

                            >

                                <i
                                    class="fas fa-eye"
                                ></i>

                                View

                            </a>

                        </td>


                    </tr>


<?php

}


/*=========================================================
  NO SUBJECT MESSAGE
=========================================================*/

if ($sr === 1) {

?>


                    <tr>

                        <td
                            colspan="11"
                            class="text-center text-muted py-4"
                        >

                            <i
                                class="fas fa-circle-info me-2"
                            ></i>

                            No subjects found for your
                            current program and semester.

                        </td>

                    </tr>


<?php

}

?>


                </tbody>

            </table>


        </div>

    </div>


    <!--=====================================================
      ATTENDANCE ANALYTICS
    =====================================================-->

    <div class="row mt-4">


        <!-- SUMMARY -->

        <div class="col-lg-8">

            <div class="subject-card">


                <h3 class="mb-4">

                    <i class="fas fa-chart-pie"></i>

                    Attendance Summary

                </h3>


                <div class="row text-center">


                    <!-- PRESENT -->

                    <div class="col-md-3">

                        <div
                            class="p-3 rounded bg-success text-white"
                        >

                            <h6>

                                Present

                            </h6>

                            <h2>

                                <?php
                                echo $presents;
                                ?>

                            </h2>

                        </div>

                    </div>


                    <!-- ABSENT -->

                    <div class="col-md-3">

                        <div
                            class="p-3 rounded bg-danger text-white"
                        >

                            <h6>

                                Absent

                            </h6>

                            <h2>

                                <?php
                                echo $absents;
                                ?>

                            </h2>

                        </div>

                    </div>


                    <!-- LATE -->

                    <div class="col-md-3">

                        <div
                            class="p-3 rounded bg-warning"
                        >

                            <h6>

                                Late

                            </h6>

                            <h2>

                                <?php
                                echo $lates;
                                ?>

                            </h2>

                        </div>

                    </div>


                    <!-- LEAVE -->

                    <div class="col-md-3">

                        <div
                            class="p-3 rounded bg-primary text-white"
                        >

                            <h6>

                                Leave

                            </h6>

                            <h2>

                                <?php
                                echo $leaves;
                                ?>

                            </h2>

                        </div>

                    </div>


                </div>


                <hr>


                <h5 class="mt-4">

                    Attendance Percentage

                </h5>


                <div class="progress">

                    <div

                        class="progress-bar"

                        style="
                            width:
                            <?php
                            echo $attendancePercentage;
                            ?>%;
                        "

                    >

                        <?php
                        echo $attendancePercentage;
                        ?>%

                    </div>

                </div>


            </div>

        </div>


        <!-- STATUS -->

        <div class="col-lg-4">

            <div class="subject-card">


                <h3 class="mb-4">

                    <i
                        class="fas fa-triangle-exclamation"
                    ></i>

                    Attendance Status

                </h3>


<?php

if ($attendancePercentage >= 90) {

?>


                <div class="alert alert-success">

                    <h5>

                        Excellent Attendance

                    </h5>

                    <p class="mb-0">

                        Keep it up.

                    </p>

                </div>


<?php

} elseif ($attendancePercentage >= 80) {

?>


                <div class="alert alert-primary">

                    <h5>

                        Very Good

                    </h5>

                    <p class="mb-0">

                        Maintain your attendance.

                    </p>

                </div>


<?php

} elseif ($attendancePercentage >= 75) {

?>


                <div class="alert alert-warning">

                    <h5>

                        Average

                    </h5>

                    <p class="mb-0">

                        Improve your attendance.

                    </p>

                </div>


<?php

} else {

?>


                <div class="alert alert-danger">

                    <h5>

                        Short Attendance

                    </h5>

                    <p class="mb-0">

                        Your attendance is below 75%.

                        Please attend regular classes.

                    </p>

                </div>


<?php

}

?>


            </div>

        </div>


    </div>


    <!--=====================================================
      RECENT ATTENDANCE
    =====================================================-->

    <div class="subject-card mt-4">


        <h3 class="mb-4">

            <i
                class="fas fa-clock-rotate-left"
            ></i>

            Recent Attendance

        </h3>


        <div class="table-responsive">


            <table
                class="table table-hover table-bordered"
            >


                <thead class="table-warning">

                    <tr>

                        <th>

                            Date

                        </th>

                        <th>

                            Subject

                        </th>

                        <th>

                            Teacher

                        </th>

                        <th>

                            Status

                        </th>

                    </tr>

                </thead>


                <tbody>


<?php

/*=========================================================
  RECENT ATTENDANCE
=========================================================*/

$recent = $conn->prepare("

    SELECT

        lecture_sessions.lecture_date,

        subjects.subject_name,

        teachers.teacher_name,

        attendance_details.status

    FROM attendance_details

    INNER JOIN attendance

        ON attendance.id =
           attendance_details.attendance_id

    INNER JOIN lecture_sessions

        ON attendance.lecture_session_id =
           lecture_sessions.id

    INNER JOIN subjects

        ON attendance.subject_id =
           subjects.id

    INNER JOIN teachers

        ON attendance.teacher_id =
           teachers.id

    WHERE attendance_details.student_id = ?

    ORDER BY

        lecture_sessions.lecture_date DESC

    LIMIT 10

");


if (!$recent) {

    die(
        "Recent Attendance Error: " .
        $conn->error
    );

}


$recent->bind_param(
    "i",
    $student_id
);


$recent->execute();


$recentResult =
    $recent->get_result();


if ($recentResult->num_rows > 0) {


    while (
        $row =
        $recentResult->fetch_assoc()
    ) {

?>


                    <tr>


                        <td>

                            <?php

                            echo date(

                                "d M Y",

                                strtotime(
                                    $row['lecture_date']
                                )

                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(

                                $row['subject_name']

                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(

                                $row['teacher_name']

                            );

                            ?>

                        </td>


                        <td>


<?php

if (
    $row['status'] == "Present"
) {

    echo
        "<span class='badge bg-success'>
            Present
        </span>";

} elseif (
    $row['status'] == "Absent"
) {

    echo
        "<span class='badge bg-danger'>
            Absent
        </span>";

} elseif (
    $row['status'] == "Late"
) {

    echo
        "<span class='badge bg-warning text-dark'>
            Late
        </span>";

} else {

    echo
        "<span class='badge bg-primary'>
            Leave
        </span>";

}

?>


                        </td>


                    </tr>


<?php

    }


} else {

?>


                    <tr>

                        <td
                            colspan="4"
                            class="text-center text-muted py-4"
                        >

                            <i
                                class="fas fa-calendar-xmark me-2"
                            ></i>

                            No attendance record
                            available yet.

                        </td>

                    </tr>


<?php

}

?>


                </tbody>

            </table>


        </div>

    </div>


    <!--=====================================================
      ATTENDANCE RULES
    =====================================================-->

    <div class="row mt-4">


        <!-- RULES -->

        <div class="col-lg-6 mb-4">


            <div class="subject-card">


                <h3 class="mb-4">

                    <i class="fas fa-circle-info"></i>

                    Attendance Rules

                </h3>


                <ul class="list-group">


                    <li class="list-group-item">

                        ✅ Minimum
                        <strong>75%</strong>
                        attendance is compulsory.

                    </li>


                    <li class="list-group-item">

                        ✅ Attendance is marked by your
                        course teacher only.

                    </li>


                    <li class="list-group-item">

                        ✅ Attendance cannot be edited
                        by students.

                    </li>


                    <li class="list-group-item">

                        ✅ Leave can only be granted by
                        the course teacher.

                    </li>


                    <li class="list-group-item">

                        ✅ Attendance updates automatically
                        after teacher submits it.

                    </li>


                </ul>


            </div>


        </div>


        <!-- UNIVERSITY NOTICE -->

        <div class="col-lg-6 mb-4">


            <div class="subject-card">


                <h3 class="mb-4">

                    <i class="fas fa-bullhorn"></i>

                    University Notice

                </h3>


                <div class="alert alert-warning">

                    <h5>

                        Important

                    </h5>

                    <p class="mb-0">

                        Students having attendance below

                        <strong>75%</strong>

                        may not be allowed to appear in
                        Mid or Final Examination according
                        to university policy.

                    </p>

                </div>


                <div class="alert alert-success">

                    Always attend lectures regularly
                    to maintain your attendance record.

                </div>


            </div>


        </div>


    </div>


    <!--=====================================================
      FOOTER
    =====================================================-->

    <footer class="mt-5">


        <div class="card border-0 shadow-sm">


            <div class="card-body">


                <div class="row align-items-center">


                    <!-- LEFT -->

                    <div class="col-md-6">


                        <div
                            class="d-flex align-items-center"
                        >


                            <img

                                src="../assets/images/logo.png"

                                style="height:55px;"

                                class="me-3"

                                alt="GTU Logo"

                            >


                            <div>


                                <h5
                                    class="mb-1 text-dark"
                                >

                                    Global Tech University ERP

                                </h5>


                                <p
                                    class="mb-0 text-muted"
                                >

                                    Student Attendance Portal

                                </p>


                            </div>


                        </div>


                    </div>


                    <!-- RIGHT -->

                    <div
                        class="col-md-6
                               text-md-end
                               mt-3
                               mt-md-0"
                    >


                        <p class="mb-1">

                            <strong>

                                Version 1.0

                            </strong>

                        </p>


                        <p class="mb-1 text-muted">

                            Attendance Management System

                        </p>


                        <p class="mb-0 text-secondary">

                            ©

                            <?php
                            echo date("Y");
                            ?>

                            Global Tech University

                        </p>


                    </div>


                </div>


            </div>


        </div>


    </footer>


</div>


<!--=========================================================
  BOOTSTRAP JS
=========================================================-->

<script

    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"

></script>


</body>

</html>