<?php

require "../utils.php";

session_start();

$quiz_id = $_SESSION["quiz_id"];

$dbh = connect_to_database();
// $showResult = false;
// $resultMessage = "";
// $correctAnswersInfo = [];

if (isset($_POST["submit"])) {
  // print_r($_POST);

  $sql = "INSERT INTO submissions (submitter, quiz_id) VALUES (?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["submitter"], $quiz_id]);

  $submission_id = $dbh->lastInsertId();
  // echo $submission_id . "<br>";

  // Get user's answers from the submitted form
  // $userAnswers = [];
  // foreach ($_POST as $key => $value) {
  //   if (strpos($key, "question_") === 0) {
  //     $questionId = substr($key, strlen("question_"));
  //     $userAnswers[$questionId] = $value;
  //   }
  // }

  foreach ($_POST as $key => $value) {
    if (strpos($key, "question_") === 0) {
      $questionId = substr($key, strlen("question_"));
      $sql = "INSERT INTO responses (content, score, submission_id, question_id) VALUES (?, ?, ?, ?)";
      prepare_and_execute($sql, [$value, 0, $submission_id, $questionId]);
    }
  }

  $_SESSION["submission_id"] = $submission_id;
  header("Location: ./results.php");
  exit;

  // // Fetch correct answers from the database
  // $sql = "SELECT id, content AS answer FROM answers WHERE question_id IN (" . implode(",", array_keys($userAnswers)) . ")";
  // $stmt = $dbh->prepare($sql);
  // $stmt->execute();
  // $correctAnswers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

  // // Calculate the grade
  // $totalQuestions = count($correctAnswers);
  // $correctCount = 0;
  // $wrongQuestions = [];

  // foreach ($userAnswers as $questionId => $userChoice) {
  //   if (isset($correctAnswers[$questionId]) && $userChoice === $correctAnswers[$questionId]) {
  //     $correctCount++;
  //   } else {
  //     $wrongQuestions[] = $questionId;
  //   }
  // }

  // $grade = ($correctCount / $totalQuestions) * 100;

  // $resultMessage = "Grade:  $grade %</div>";

  // // Display correct and incorrect answers for each question
  // foreach ($userAnswers as $questionId => $userChoice) {
  //   if (in_array($questionId, $wrongQuestions)) {
  //     $correctChoice = isset($correctAnswers[$questionId]) ? $correctAnswers[$questionId] : '';
  //     $correctAnswersInfo[] = ['questionId' => $questionId, 'correctChoice' => $correctChoice];
  //   }
  // }

  // $showResult = true;
}

$sql = "SELECT * FROM quizzes WHERE id = ? LIMIT 1";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$quiz = $stmt->fetch();

$sql = "
SELECT q.id AS id,
q.content AS question,
GROUP_CONCAT(c.content SEPARATOR '|') AS choices
FROM questions q
JOIN choices c ON q.id = c.question_id
WHERE q.quiz_id = ?
GROUP BY q.id, q.content
";
$stmt = $dbh->prepare($sql);
$stmt->execute([$quiz_id]);

$rows = $stmt->fetchAll();

?>

<!doctype html>
<html lang="en" class="overscroll-none">

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
      <h1 class="my-4 font-semibold"><?= $quiz["name"] ?></h1>

      <div class="mb-4">
        <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
        <div class="mt-2">
          <input name="submitter" type="text" required class="block w-1/2 rounded-md border-0 py-1.5 px-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <?php foreach ($rows as $index => $row) : ?>
        <div class="border rounded-lg my-10 px-4 py-2 border-slate-300">
          <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
          <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
          <div class="mt-6 space-y-2">
            <?php $h = "question_" . $row["id"]; ?>
            <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
              <div class="flex items-center gap-x-3">
                <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" required>
                <label for="<?= $h . "_" . $index ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
              </div>
            <?php endforeach ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="flex">
        <div class="grow"></div>
        <button name="submit" class="mb-14 rounded-md bg-indigo-600 px-8 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
      </div>

      <?php if (isset($showResult)) : ?>
        <div style="margin-top: 20px;">
          <?= $resultMessage ?>
          <?php foreach ($correctAnswersInfo as $info) : ?>
            <div style="margin-top: 10px;">
              Question #<?= $info['questionId'] - 1 ?>: Correct Answer - <?= $info['correctChoice'] ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
</body>

</html>
