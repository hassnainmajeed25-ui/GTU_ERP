<?php

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

set_time_limit(0);
ini_set('memory_limit','1024M');

/*====================================================
GTU ERP
SMART TIMETABLE GENERATOR
====================================================*/

/*
FEATURES

✔ 55 Teachers
✔ 671 Subjects
✔ 30 Rooms
✔ Morning
✔ Replica
✔ Evening
✔ No Teacher Clash
✔ No Room Clash
✔ No Semester Clash
✔ Automatic Free Lectures
✔ Professional Distribution

*/

/*====================================================
START TRANSACTION
====================================================*/

$conn->begin_transaction();

/*====================================================
DELETE OLD TIMETABLE
====================================================*/

$conn->query("DELETE FROM teacher_timetable");

/*====================================================
WORKING DAYS
====================================================*/

$days = [

    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"

];

/*====================================================
TIME SLOTS
====================================================*/

$timeSlots = [

"Morning"=>[

["08:00:00","09:00:00"],
["09:00:00","10:00:00"],
["10:00:00","11:00:00"],
["11:00:00","12:00:00"],
["12:00:00","13:00:00"]

],

"Replica"=>[

["13:00:00","14:00:00"],
["14:00:00","15:00:00"],
["15:00:00","16:00:00"],
["16:00:00","17:00:00"],
["17:00:00","18:00:00"]

],

"Evening"=>[

["18:00:00","19:00:00"],
["19:00:00","20:00:00"],
["20:00:00","21:00:00"],
["21:00:00","22:00:00"],
["22:00:00","23:00:00"]

]

];

/*====================================================
LOAD ACTIVE ROOMS
====================================================*/

$rooms=[];

$sql="

SELECT

id,
room_no,
capacity

FROM rooms

WHERE status='Active'

ORDER BY CAST(room_no AS UNSIGNED)

";

$result=$conn->query($sql);

while($row=$result->fetch_assoc()){

    $rooms[]=$row;

}

/*====================================================
LOAD ACTIVE TEACHERS
====================================================*/

$teachers=[];

$sql="

SELECT

id,
teacher_name,
faculty_id

FROM teachers

WHERE status='Active'

ORDER BY teacher_name

";

$result=$conn->query($sql);

while($row=$result->fetch_assoc()){

    $teachers[$row['id']]=$row;

}

/*====================================================
LOAD SUBJECTS
====================================================*/

$subjects=[];

$sql="

SELECT

subjects.id,
subjects.subject_name,
subjects.subject_code,
subjects.credit_hours,
subjects.program_id,
subjects.semester,
subjects.teacher_id,

programs.program_name,

teachers.teacher_name

FROM subjects

INNER JOIN teachers

ON teachers.id=subjects.teacher_id

INNER JOIN programs

ON programs.id=subjects.program_id

WHERE

subjects.status='Active'

AND subjects.teacher_id IS NOT NULL

ORDER BY

subjects.program_id,
subjects.semester,
subjects.subject_name

";

$result=$conn->query($sql);

while($row=$result->fetch_assoc()){

    /*
    Credit Hours

    2 Credit = 2 Lectures

    3 Credit = 3 Lectures

    4 Credit = 4 Lectures

    */

    $row['lectures_per_week']=(int)$row['credit_hours'];

    if($row['lectures_per_week']<2){

        $row['lectures_per_week']=2;

    }

    $subjects[]=$row;

}

/*====================================================
SMART MEMORY ARRAYS
====================================================*/

$teacherBusy=[];

$roomBusy=[];

$semesterBusy=[];

$teacherDailyLoad=[];

$semesterDailyLoad=[];

$teacherWeeklyLoad=[];

$roomWeeklyLoad=[];

$generated=0;

echo "

<div class='container mt-4'>

<div class='alert alert-primary'>

<h3>

GTU Smart Timetable Generator Started

</h3>

<hr>

Teachers :
".count($teachers)."

<br>

Subjects :
".count($subjects)."

<br>

Rooms :
".count($rooms)."

<br>

Days :
".count($days)."

</div>

</div>

";

/*====================================================

PART 2 STARTS HERE

====================================================*/

/*====================================================
PART 2A
SMART ALLOCATION ENGINE
====================================================*/

foreach($subjects as $subject){

    $teacher_id = (int)$subject['teacher_id'];

    $subject_id = (int)$subject['id'];

    $program_id = (int)$subject['program_id'];

    $semester = (int)$subject['semester'];

    $lectureRequired = (int)$subject['lectures_per_week'];

    $lectureAssigned = 0;

    $attempt = 0;

    while($lectureAssigned < $lectureRequired){

        $attempt++;

        if($attempt > 1000){
            break;
        }

        /*==========================
        RANDOM DAY
        ==========================*/

        $day = $days[array_rand($days)];

        /*==========================
        DAILY LOAD
        Teacher Maximum 4 Lectures
        ==========================*/

        if(

            isset($teacherDailyLoad[$teacher_id][$day])

            &&

            $teacherDailyLoad[$teacher_id][$day] >= 4

        ){

            continue;

        }

        /*==========================
        SAME SUBJECT
        ONLY ONE LECTURE PER DAY
        ==========================*/

        if(

            isset($semesterDailyLoad[$program_id][$semester][$subject_id][$day])

        ){

            continue;

        }

        /*==========================
        RANDOM SHIFT
        ==========================*/

        $shiftNames = array_keys($timeSlots);

        shuffle($shiftNames);

        $allocated = false;

        foreach($shiftNames as $shift){

            $slots = $timeSlots[$shift];

            shuffle($slots);

            foreach($slots as $slot){

                $start_time = $slot[0];

                $end_time = $slot[1];

                /*==========================
                TEACHER CLASH
                ==========================*/

                if(

                    isset(

                        $teacherBusy[$teacher_id][$day][$start_time]

                    )

                ){

                    continue;

                }

                /*==========================
                PROGRAM CLASH
                ==========================*/

                if(

                    isset(

                        $semesterBusy[$program_id][$semester][$day][$start_time]

                    )

                ){

                    continue;

                }

                /*==========================
                ROOM SEARCH
                ==========================*/

                shuffle($rooms);

                foreach($rooms as $room){

                    $room_no = $room['room_no'];

                    if(

                        isset(

                            $roomBusy[$room_no][$day][$start_time]

                        )

                    ){

                        continue;

                    }

                    /*==========================
                    ROOM FOUND
                    ==========================*/

                    $stmt = $conn->prepare("

                    INSERT INTO teacher_timetable(

                    teacher_id,

                    subject_id,

                    shift,

                    day_name,

                    start_time,

                    end_time,

                    room_no,

                    status

                    )

                    VALUES(

                    ?,?,?,?,?,?,?,'Active'

                    )

                    ");

                    $stmt->bind_param(

                        "iisssss",

                        $teacher_id,

                        $subject_id,

                        $shift,

                        $day,

                        $start_time,

                        $end_time,

                        $room_no

                    );

                    $stmt->execute();

                    $stmt->close();

                    /*==========================
                    SAVE MEMORY
                    ==========================*/

                    $teacherBusy[$teacher_id][$day][$start_time] = true;

                    $semesterBusy[$program_id][$semester][$day][$start_time] = true;

                    $roomBusy[$room_no][$day][$start_time] = true;

                    $teacherDailyLoad[$teacher_id][$day] =
                    ($teacherDailyLoad[$teacher_id][$day] ?? 0) + 1;

                    $semesterDailyLoad[$program_id][$semester][$subject_id][$day] = true;

                    $teacherWeeklyLoad[$teacher_id] =
                    ($teacherWeeklyLoad[$teacher_id] ?? 0) + 1;

                    $roomWeeklyLoad[$room_no] =
                    ($roomWeeklyLoad[$room_no] ?? 0) + 1;

                    $lectureAssigned++;

                    $generated++;

                    $allocated = true;

                    break;

                }

                if($allocated){
                    break;
                }

            }

            if($allocated){
                break;
            }

        }

    }

}

/*====================================================
PART 2B STARTS HERE
====================================================*/

