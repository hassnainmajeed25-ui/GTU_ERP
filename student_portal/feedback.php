<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


/* =========================================================
   STUDENT LOGIN PROTECTION
   ========================================================= */

if (
    !isset($_SESSION['student_id']) ||
    empty($_SESSION['student_id'])
) {
    header("Location: dashboard.php");
    exit;
}


$student_id = (int) $_SESSION['student_id'];

$message = "";
$message_type = "";


/* =========================================================
   CHECK IF STUDENT HAS ALREADY SUBMITTED FEEDBACK
   ========================================================= */

$already_submitted = false;

$check_sql = "
    SELECT id
    FROM student_feedback_submissions
    WHERE student_id = ?
    LIMIT 1
";

$check_stmt = $conn->prepare($check_sql);

if ($check_stmt) {

    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();

    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $already_submitted = true;
    }

    $check_stmt->close();
}


/* =========================================================
   GET ALL QUESTIONS
   ========================================================= */

$questions = [];

$question_sql = "
    SELECT
        id,
        category,
        question,
        option_1,
        option_2,
        option_3,
        option_4,
        question_order
    FROM student_feedback_questions
    ORDER BY question_order ASC
";

$question_result = $conn->query($question_sql);

if ($question_result) {

    while ($row = $question_result->fetch_assoc()) {
        $questions[] = $row;
    }
}


/* =========================================================
   HANDLE FEEDBACK SUBMISSION
   ========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_feedback'])
) {

    if ($already_submitted) {

        $message = "You have already submitted your feedback.";
        $message_type = "error";

    } else {

        $answers = $_POST['answers'] ?? [];

        /* ---------------------------------------------
           CHECK ALL 30 QUESTIONS ARE ANSWERED
           --------------------------------------------- */

        $all_answered = true;

        foreach ($questions as $question) {

            $question_id = (int) $question['id'];

            if (
                !isset($answers[$question_id]) ||
                !in_array(
                    (int) $answers[$question_id],
                    [1, 2, 3, 4],
                    true
                )
            ) {

                $all_answered = false;
                break;
            }
        }


        if (!$all_answered) {

            $message = "Please answer all questions before submitting.";
            $message_type = "error";

        } else {

            /* -----------------------------------------
               START DATABASE TRANSACTION
               ----------------------------------------- */

            $conn->begin_transaction();

            try {

                /* -------------------------------------
                   CREATE SUBMISSION
                   ------------------------------------- */

                $insert_submission = "
                    INSERT INTO student_feedback_submissions
                    (student_id)
                    VALUES (?)
                ";

                $submission_stmt = $conn->prepare(
                    $insert_submission
                );

                if (!$submission_stmt) {
                    throw new Exception(
                        "Unable to create feedback submission."
                    );
                }

                $submission_stmt->bind_param(
                    "i",
                    $student_id
                );

                if (!$submission_stmt->execute()) {
                    throw new Exception(
                        "Unable to save feedback submission."
                    );
                }

                $submission_id = $conn->insert_id;

                $submission_stmt->close();


                /* -------------------------------------
                   INSERT ANSWERS
                   ------------------------------------- */

                $insert_answer = "
                    INSERT INTO student_feedback_answers
                    (
                        submission_id,
                        student_id,
                        question_id,
                        selected_option
                    )
                    VALUES (?, ?, ?, ?)
                ";

                $answer_stmt = $conn->prepare(
                    $insert_answer
                );

                if (!$answer_stmt) {
                    throw new Exception(
                        "Unable to prepare feedback answers."
                    );
                }


                foreach ($questions as $question) {

                    $question_id = (int) $question['id'];

                    $selected_option = (int)
                        $answers[$question_id];


                    $answer_stmt->bind_param(
                        "iiii",
                        $submission_id,
                        $student_id,
                        $question_id,
                        $selected_option
                    );


                    if (!$answer_stmt->execute()) {

                        throw new Exception(
                            "Unable to save one or more answers."
                        );
                    }
                }


                $answer_stmt->close();


                /* -------------------------------------
                   COMMIT
                   ------------------------------------- */

                $conn->commit();

                $already_submitted = true;

                $message =
                    "Thank you! Your feedback has been submitted successfully.";

                $message_type = "success";


            } catch (Exception $e) {

                $conn->rollback();

                $message =
                    "Something went wrong while submitting your feedback. Please try again.";

                $message_type = "error";
            }
        }
    }
}

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
        Student Feedback | GTU
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        /* =====================================================
           GLOBAL
           ===================================================== */

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #eef5ff,
                    #f8fbff
                );

            color: #111827;
        }


        /* =====================================================
           PAGE
           ===================================================== */

        .feedback-page {

            min-height: 100vh;

            padding: 45px 20px 70px;

            display: flex;

            justify-content: center;

            align-items: flex-start;
        }


        .feedback-wrapper {

            width: 100%;

            max-width: 900px;
        }


        /* =====================================================
           HEADER
           ===================================================== */

        .feedback-header {

            background:
                linear-gradient(
                    135deg,
                    #07152f,
                    #102d59,
                    #0284c7
                );

            border-radius: 25px;

            padding: 35px;

            color: white;

            margin-bottom: 25px;

            position: relative;

            overflow: hidden;

            box-shadow:
                0 20px 55px
                rgba(15, 23, 42, 0.18);
        }


        .feedback-header::before {

            content: "";

            position: absolute;

            width: 250px;
            height: 250px;

            border-radius: 50%;

            background:
                rgba(255, 210, 31, 0.13);

            right: -80px;
            top: -100px;
        }


        .feedback-header-content {

            position: relative;

            z-index: 2;
        }


        .feedback-badge {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            background:
                rgba(255, 210, 31, 0.16);

            color: #ffd21f;

            border: 1px solid
                rgba(255, 210, 31, 0.35);

            padding: 8px 15px;

            border-radius: 50px;

            font-size: 13px;

            font-weight: 800;

            margin-bottom: 17px;
        }


        .feedback-header h1 {

            margin: 0 0 12px;

            font-size: 38px;

            font-weight: 900;
        }


        .feedback-header h1 span {

            color: #ffd21f;
        }


        .feedback-header p {

            margin: 0;

            max-width: 700px;

            color: #dbeafe;

            line-height: 1.7;

            font-size: 15px;
        }


        /* =====================================================
           PROGRESS
           ===================================================== */

        .progress-card {

            background: white;

            border-radius: 20px;

            padding: 20px 25px;

            margin-bottom: 20px;

            box-shadow:
                0 10px 35px
                rgba(0, 0, 0, 0.07);

            border: 1px solid #e5e7eb;
        }


        .progress-top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 12px;
        }


        .progress-label {

            font-size: 14px;

            font-weight: 800;

            color: #374151;
        }


        .progress-number {

            font-size: 14px;

            font-weight: 900;

            color: #0284c7;
        }


        .progress-bar {

            width: 100%;

            height: 10px;

            background: #e5e7eb;

            border-radius: 50px;

            overflow: hidden;
        }


        .progress-fill {

            width: 3.33%;

            height: 100%;

            background:
                linear-gradient(
                    90deg,
                    #ffd21f,
                    #38bdf8
                );

            border-radius: 50px;

            transition:
                width 0.3s ease;
        }


        /* =====================================================
           ALERTS
           ===================================================== */

        .feedback-alert {

            padding: 18px 20px;

            border-radius: 15px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 700;

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .feedback-alert.success {

            background: #ecfdf5;

            color: #047857;

            border: 1px solid #a7f3d0;
        }


        .feedback-alert.error {

            background: #fef2f2;

            color: #b91c1c;

            border: 1px solid #fecaca;
        }


        /* =====================================================
           QUESTION CARD
           ===================================================== */

        .question-card {

            background: white;

            border-radius: 25px;

            padding: 40px;

            box-shadow:
                0 18px 55px
                rgba(0, 0, 0, 0.08);

            border: 1px solid #e5e7eb;
        }


        .question-step {

            display: none;
        }


        .question-step.active {

            display: block;

            animation:
                questionFade
                0.3s
                ease;
        }


        @keyframes questionFade {

            from {

                opacity: 0;

                transform:
                    translateX(15px);
            }

            to {

                opacity: 1;

                transform:
                    translateX(0);
            }
        }


        .question-category {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            background:
                rgba(2, 132, 199, 0.10);

            color: #0284c7;

            padding: 8px 14px;

            border-radius: 50px;

            font-size: 12px;

            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            margin-bottom: 20px;
        }


        .question-number {

            color: #6b7280;

            font-size: 14px;

            font-weight: 800;

            margin-bottom: 10px;
        }


        .question-title {

            margin: 0 0 30px;

            font-size: 25px;

            line-height: 1.45;

            font-weight: 900;

            color: #111827;
        }


        /* =====================================================
           OPTIONS
           ===================================================== */

        .options {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 15px;
        }


        .option-label {

            position: relative;

            cursor: pointer;
        }


        .option-label input {

            position: absolute;

            opacity: 0;

            pointer-events: none;
        }


        .option-box {

            min-height: 85px;

            padding: 18px 20px;

            border-radius: 16px;

            border: 2px solid #e5e7eb;

            background: #fafafa;

            display: flex;

            align-items: center;

            gap: 15px;

            transition:
                all 0.25s ease;
        }


        .option-circle {

            width: 28px;

            height: 28px;

            flex: 0 0 28px;

            border-radius: 50%;

            border: 2px solid #cbd5e1;

            display: flex;

            align-items: center;

            justify-content: center;

            color: white;

            font-size: 12px;
        }


        .option-text {

            font-size: 15px;

            font-weight: 800;

            color: #374151;
        }


        .option-label:hover .option-box {

            border-color: #38bdf8;

            background: #f0f9ff;

            transform:
                translateY(-2px);
        }


        .option-label input:checked
        + .option-box {

            border-color: #0284c7;

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #f0f9ff
                );

            box-shadow:
                0 8px 25px
                rgba(2, 132, 199, 0.12);
        }


        .option-label input:checked
        + .option-box
        .option-circle {

            background: #0284c7;

            border-color: #0284c7;
        }


        .option-label input:checked
        + .option-box
        .option-circle::after {

            content: "✓";

            font-weight: 900;
        }


        .option-label input:checked
        + .option-box
        .option-text {

            color: #0284c7;
        }


        /* =====================================================
           NAVIGATION
           ===================================================== */

        .question-navigation {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-top: 35px;

            padding-top: 25px;

            border-top: 1px solid #e5e7eb;
        }


        .nav-btn {

            min-width: 140px;

            min-height: 50px;

            border: none;

            border-radius: 13px;

            padding: 12px 20px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            font-size: 14px;

            font-weight: 900;

            cursor: pointer;

            transition:
                all 0.25s ease;
        }


        .previous-btn {

            background: #eef2f7;

            color: #374151;
        }


        .previous-btn:hover {

            background: #e2e8f0;

            transform:
                translateY(-2px);
        }


        .next-btn {

            background:
                linear-gradient(
                    135deg,
                    #0284c7,
                    #2563eb
                );

            color: white;

            margin-left: auto;
        }


        .next-btn:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 25px
                rgba(37, 99, 235, 0.25);
        }


        .submit-btn {

            background:
                linear-gradient(
                    135deg,
                    #ffd21f,
                    #f4b900
                );

            color: #111;

            margin-left: auto;
        }


        .submit-btn:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 25px
                rgba(255, 210, 31, 0.30);
        }


        /* =====================================================
           ALREADY SUBMITTED
           ===================================================== */

        .submitted-card {

            background: white;

            border-radius: 25px;

            padding: 60px 35px;

            text-align: center;

            box-shadow:
                0 18px 55px
                rgba(0, 0, 0, 0.08);
        }


        .submitted-icon {

            width: 90px;

            height: 90px;

            margin: 0 auto 25px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #dcfce7;

            color: #16a34a;

            font-size: 40px;
        }


        .submitted-card h2 {

            margin: 0 0 12px;

            font-size: 30px;

            font-weight: 900;
        }


        .submitted-card p {

            margin: 0;

            color: #6b7280;

            line-height: 1.7;
        }


        /* =====================================================
           MOBILE
           ===================================================== */

        @media (max-width: 700px) {

            .feedback-page {

                padding:
                    20px 12px 40px;
            }


            .feedback-header {

                padding: 28px 22px;

                border-radius: 20px;
            }


            .feedback-header h1 {

                font-size: 30px;
            }


            .question-card {

                padding: 25px 20px;

                border-radius: 20px;
            }


            .question-title {

                font-size: 21px;
            }


            .options {

                grid-template-columns: 1fr;
            }


            .option-box {

                min-height: 70px;
            }


            .question-navigation {

                flex-wrap: wrap;
            }


            .nav-btn {

                min-width: 120px;
            }


            .next-btn,
            .submit-btn {

                margin-left: auto;
            }
        }

    </style>

</head>


<body>


<div class="feedback-page">

    <div class="feedback-wrapper">


        <!-- =================================================
             HEADER
             ================================================= -->

        <div class="feedback-header">

            <div class="feedback-header-content">

                <div class="feedback-badge">

                    <i class="fas fa-comments"></i>

                    Student Feedback

                </div>


                <h1>

                    Help Us Improve
                    <span>GTU</span>

                </h1>


                <p>

                    Your feedback helps Global Tech University
                    improve teaching, learning, facilities and
                    student services. Please answer all questions
                    honestly.

                </p>

            </div>

        </div>


        <?php if ($message !== ""): ?>

            <div
                class="feedback-alert
                <?php echo htmlspecialchars($message_type); ?>"
            >

                <?php if ($message_type === "success"): ?>

                    <i class="fas fa-check-circle"></i>

                <?php else: ?>

                    <i class="fas fa-exclamation-circle"></i>

                <?php endif; ?>


                <span>

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </span>

            </div>

        <?php endif; ?>


        <?php if ($already_submitted): ?>


            <!-- =============================================
                 ALREADY SUBMITTED
                 ============================================= -->

            <div class="submitted-card">

                <div class="submitted-icon">

                    <i class="fas fa-check"></i>

                </div>


                <h2>
                    Feedback Submitted
                </h2>


                <p>

                    Thank you for sharing your valuable
                    feedback with Global Tech University.

                    <br>

                    Your response has been successfully
                    recorded.

                </p>

            </div>


        <?php elseif (count($questions) < 30): ?>


            <!-- =============================================
                 QUESTION ERROR
                 ============================================= -->

            <div class="feedback-alert error">

                <i class="fas fa-database"></i>

                <span>

                    Feedback questions are not configured correctly.
                    Please contact the administrator.

                </span>

            </div>


        <?php else: ?>


            <!-- =============================================
                 PROGRESS
                 ============================================= -->

            <div class="progress-card">

                <div class="progress-top">

                    <span class="progress-label">

                        Feedback Progress

                    </span>


                    <span
                        class="progress-number"
                        id="progressNumber"
                    >

                        Question 1 of 30

                    </span>

                </div>


                <div class="progress-bar">

                    <div
                        class="progress-fill"
                        id="progressFill"
                    ></div>

                </div>

            </div>


            <!-- =============================================
                 FORM
                 ============================================= -->

            <form
                method="POST"
                id="feedbackForm"
            >


                <div class="question-card">


                    <?php foreach ($questions as $index => $question): ?>

                        <?php

                        $question_number = $index + 1;

                        $question_id =
                            (int) $question['id'];

                        ?>


                        <div
                            class="question-step
                            <?php
                            echo $index === 0
                                ? 'active'
                                : '';
                            ?>"
                            data-question="<?php
                                echo $question_number;
                            ?>"
                        >


                            <div class="question-category">

                                <i class="fas fa-layer-group"></i>

                                <?php
                                echo htmlspecialchars(
                                    $question['category']
                                );
                                ?>

                            </div>


                            <div class="question-number">

                                Question
                                <?php
                                echo $question_number;
                                ?>
                                of 30

                            </div>


                            <h2 class="question-title">

                                <?php
                                echo htmlspecialchars(
                                    $question['question']
                                );
                                ?>

                            </h2>


                            <div class="options">


                                <!-- OPTION 1 -->

                                <label class="option-label">

                                    <input
                                        type="radio"
                                        name="answers[
                                            <?php
                                            echo $question_id;
                                            ?>
                                        ]"
                                        value="1"
                                    >

                                    <div class="option-box">

                                        <div
                                            class="option-circle"
                                        ></div>

                                        <div
                                            class="option-text"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $question['option_1']
                                            );
                                            ?>

                                        </div>

                                    </div>

                                </label>


                                <!-- OPTION 2 -->

                                <label class="option-label">

                                    <input
                                        type="radio"
                                        name="answers[
                                            <?php
                                            echo $question_id;
                                            ?>
                                        ]"
                                        value="2"
                                    >

                                    <div class="option-box">

                                        <div
                                            class="option-circle"
                                        ></div>

                                        <div
                                            class="option-text"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $question['option_2']
                                            );
                                            ?>

                                        </div>

                                    </div>

                                </label>


                                <!-- OPTION 3 -->

                                <label class="option-label">

                                    <input
                                        type="radio"
                                        name="answers[
                                            <?php
                                            echo $question_id;
                                            ?>
                                        ]"
                                        value="3"
                                    >

                                    <div class="option-box">

                                        <div
                                            class="option-circle"
                                        ></div>

                                        <div
                                            class="option-text"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $question['option_3']
                                            );
                                            ?>

                                        </div>

                                    </div>

                                </label>


                                <!-- OPTION 4 -->

                                <label class="option-label">

                                    <input
                                        type="radio"
                                        name="answers[
                                            <?php
                                            echo $question_id;
                                            ?>
                                        ]"
                                        value="4"
                                    >

                                    <div class="option-box">

                                        <div
                                            class="option-circle"
                                        ></div>

                                        <div
                                            class="option-text"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $question['option_4']
                                            );
                                            ?>

                                        </div>

                                    </div>

                                </label>


                            </div>


                            <!-- NAVIGATION -->

                            <div class="question-navigation">


                                <?php if ($index > 0): ?>

                                    <button
                                        type="button"
                                        class="nav-btn previous-btn"
                                        onclick="previousQuestion()"
                                    >

                                        <i
                                            class="fas fa-arrow-left"
                                        ></i>

                                        Previous

                                    </button>

                                <?php endif; ?>


                                <?php if ($index < count($questions) - 1): ?>

                                    <button
                                        type="button"
                                        class="nav-btn next-btn"
                                        onclick="nextQuestion()"
                                    >

                                        Next

                                        <i
                                            class="fas fa-arrow-right"
                                        ></i>

                                    </button>

                                <?php else: ?>

                                    <button
                                        type="submit"
                                        name="submit_feedback"
                                        value="1"
                                        class="nav-btn submit-btn"
                                    >

                                        <i
                                            class="fas fa-paper-plane"
                                        ></i>

                                        Submit Feedback

                                    </button>

                                <?php endif; ?>


                            </div>


                        </div>

                    <?php endforeach; ?>


                </div>

            </form>

        <?php endif; ?>


    </div>

</div>



<script>

/* =========================================================
   FEEDBACK QUESTION NAVIGATION
   ========================================================= */

const questions =
    document.querySelectorAll('.question-step');

const progressFill =
    document.getElementById('progressFill');

const progressNumber =
    document.getElementById('progressNumber');

let currentQuestion = 0;


/* =========================================================
   SHOW QUESTION
   ========================================================= */

function showQuestion(index) {

    if (!questions.length) {
        return;
    }


    questions.forEach(function(question, i) {

        question.classList.remove('active');

        if (i === index) {

            question.classList.add('active');
        }

    });


    currentQuestion = index;


    /* ---------------------------------------------
       UPDATE PROGRESS
       --------------------------------------------- */

    const questionNumber =
        currentQuestion + 1;

    const totalQuestions =
        questions.length;


    const percentage =
        (questionNumber / totalQuestions) * 100;


    if (progressFill) {

        progressFill.style.width =
            percentage + '%';
    }


    if (progressNumber) {

        progressNumber.textContent =
            'Question ' +
            questionNumber +
            ' of ' +
            totalQuestions;
    }


    /* ---------------------------------------------
       SCROLL TO TOP OF QUESTION
       --------------------------------------------- */

    window.scrollTo({

        top: 0,

        behavior: 'smooth'

    });

}


/* =========================================================
   CHECK CURRENT ANSWER
   ========================================================= */

function currentQuestionAnswered() {

    const current =
        questions[currentQuestion];


    if (!current) {

        return false;
    }


    const selected =
        current.querySelector(
            'input[type="radio"]:checked'
        );


    return selected !== null;
}


/* =========================================================
   NEXT QUESTION
   ========================================================= */

function nextQuestion() {

    if (!currentQuestionAnswered()) {

        alert(
            'Please select one option before continuing.'
        );

        return;
    }


    if (
        currentQuestion <
        questions.length - 1
    ) {

        showQuestion(
            currentQuestion + 1
        );
    }

}


/* =========================================================
   PREVIOUS QUESTION
   ========================================================= */

function previousQuestion() {

    if (currentQuestion > 0) {

        showQuestion(
            currentQuestion - 1
        );
    }

}


/* =========================================================
   FINAL SUBMIT VALIDATION
   ========================================================= */

const feedbackForm =
    document.getElementById('feedbackForm');


if (feedbackForm) {

    feedbackForm.addEventListener(
        'submit',
        function(event) {

            let allAnswered = true;

            let firstUnanswered = -1;


            questions.forEach(
                function(question, index) {

                    const selected =
                        question.querySelector(
                            'input[type="radio"]:checked'
                        );


                    if (!selected) {

                        allAnswered = false;

                        if (firstUnanswered === -1) {

                            firstUnanswered =
                                index;
                        }
                    }

                }
            );


            if (!allAnswered) {

                event.preventDefault();


                showQuestion(
                    firstUnanswered
                );


                alert(
                    'Please answer all questions before submitting.'
                );

                return;
            }


            const confirmed =
                confirm(
                    'Are you sure you want to submit your feedback? You will not be able to submit it again.'
                );


            if (!confirmed) {

                event.preventDefault();

            }

        }
    );

}


/* =========================================================
   INITIAL QUESTION
   ========================================================= */

showQuestion(0);

</script>


</body>

</html>