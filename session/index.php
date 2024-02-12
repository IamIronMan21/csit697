<?php

require "../utils.php";

session_start();

$quiz_id = $_SESSION["quiz_id"];

$dbh = connect_to_database();

$showResult = false;
$resultMessage = '';
$correctAnswersInfo = [];

$studentName = ''; // Initialize student's name variable

if (isset($_POST["submit"])) {
    // Get student's name
    $studentName = isset($_POST['student_name']) ? $_POST['student_name'] : '';

    // Get user's answers from the submitted form
    $userAnswers = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $questionId = substr($key, strlen('question_'));
            $userAnswers[$questionId] = $value;
        }
    }

    // Fetch correct answers from the database
    $sql = "SELECT id, content AS answer FROM answers WHERE question_id IN (" . implode(",", array_keys($userAnswers)) . ")";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $correctAnswers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Calculate the grade
    $totalQuestions = count($correctAnswers);
    $correctCount = 0;
    $wrongQuestions = [];

    foreach ($userAnswers as $questionId => $userChoice) {
        if (isset($correctAnswers[$questionId]) && $userChoice === $correctAnswers[$questionId]) {
            $correctCount++;
        } else {
            $wrongQuestions[] = $questionId;
        }
    }

    $grade = ($correctCount / $totalQuestions) * 100;

    // Display the result with color codes
    $resultMessage = '<div style="font-size: 18px; font-weight: bold; color: ';
    if ($grade >= 90) {
        $resultMessage .= 'green;">A';
    } elseif ($grade >= 80) {
        $resultMessage .= 'green;">B';
    } elseif ($grade >= 70) {
        $resultMessage .= 'yellow;">C';
    } elseif ($grade >= 60) {
        $resultMessage .= 'yellow;">D';
    } else {
        $resultMessage .= 'red;">F';
    }
    $resultMessage .= ' - Your Grade: ' . $grade . '%</div>';

    // Display correct and incorrect answers for each question
    foreach ($userAnswers as $questionId => $userChoice) {
        if (in_array($questionId, $wrongQuestions)) {
            $correctChoice = isset($correctAnswers[$questionId]) ? $correctAnswers[$questionId] : '';
            $correctAnswersInfo[] = ['questionId' => $questionId, 'correctChoice' => $correctChoice];
        }
    }

    $showResult = true;
}

$sql = <<<EOD
SELECT q.id AS id,
       q.content AS question,
       GROUP_CONCAT(c.content SEPARATOR '|') AS choices
FROM questions q
JOIN choices c ON q.id = c.question_id
WHERE q.quiz_id = $quiz_id
GROUP BY q.id, q.content;
EOD;
$stmt = $dbh->prepare($sql);
$stmt->execute();

$rows = $stmt->fetchAll();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quizify</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen">

<header class="px-8 py-5 bg-gray-800">
    <div class="flex items-center">
        <div class="grow">
            &nbsp;
        </div>
    </div>
</header>

<div class="w-1/2 mx-auto bg-white min-h-screen px-8">
    <form method="post">
        <h1 class="my-4">Quiz</h1>

        <!-- Student's Name input -->
        <div class="mb-4">
            <label for="student_name" class="block text-sm font-medium text-gray-900">Student's Name</label>
            <input type="text" id="student_name" name="student_name" class="mt-1 p-2 border rounded-md w-full" value="<?= htmlspecialchars($studentName) ?>">
        </div>

        <?php foreach ($rows as $index => $row) : ?>
            <div class="border rounded-lg my-10 px-4 py-2 border-slate-300">
                <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
                <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
                <div class="mt-6 space-y-2">
                    <?php $h = 'question_' . $row['id']; ?>
                    <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
                        <div class="flex items-center gap-x-3">
                            <input id="<?= $h . '_' . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>">
                            <label for="<?= $h . '_' . $index ?>"
                                   class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach; ?>

        <button name="submit" class="mb-14">Submit</button>

        <?php if ($showResult) : ?>
            <div style="margin-top: 20px;">
                <?= $resultMessage ?>
                <?php foreach ($correctAnswersInfo as $info) : ?>
                    <div style="margin-top: 10px;">
                        Question #<?= $info['questionId']-1 ?>: Correct Answer - <?= $info['correctChoice'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
