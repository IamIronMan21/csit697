<?php

require "../utils.php";

session_start();

$quiz_id = $_SESSION["quiz_id"];

if (isset($_POST["submit"])) {
  $dbh = connect_to_database();
  $sql = "INSERT INTO submissions (submitter, quiz_id) VALUES (?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["submitter"], $quiz_id]);

  $submission_id = $dbh->lastInsertId();

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
}

$sql = "
SELECT
  q.name as quiz_name,
  c.name as course_name
FROM
  quizzes q,
  courses c
WHERE
  q.id = ?
  AND q.course_id = c.id
LIMIT
  1
";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$quiz = $stmt->fetch();

$sql = "
SELECT
  q.id AS id,
  q.content AS question,
  q.type AS type,
  GROUP_CONCAT(c.content SEPARATOR '|') AS choices
FROM
  questions q
  LEFT JOIN choices c ON q.id = c.question_id
WHERE
  q.quiz_id = ?
GROUP BY
  q.id
";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$rows = $stmt->fetchAll();

?>

<!doctype html>
<html lang="en" class="overscroll-none">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Quizify</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Literata:ital,opsz@0,7..72;1,7..72&display=swap">

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen font-['Inter']">

  <header class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center text-center">
        <div class="grow">
          <h1 class="text-white text-center font-['Literata'] text-xl">Quizify</h1>
        </div>
      </div>
    </div>
  </header>

  <div class="mx-auto max-w-7xl bg-white min-h-screen px-12 pt-4">

    <div class="flex w-full min-h-screen">

      <div class="w-1/4">
        <div class="flex justify-end sticky top-[16px]">
          <div class="w-1/2 items-center justify-center rounded-md bg-white px-3 py-3 shadow-sm ring-1 ring-inset ring-gray-300 select-none">
            <div class="text-sm text-slate-500">Time elapsed</div>
            <div class="block">
              <p id="stopwatch" class="text-gray-900">0:00:00</p>
            </div>
          </div>
        </div>
        <script>
          const stopwatchElement = document.getElementById('stopwatch');

          // Function to format the time as HH:MM:SS
          function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainingSeconds = seconds % 60;
            return `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
          }

          // Function to update the stopwatch
          function updateStopwatch() {
            elapsedTime++;
            stopwatchElement.textContent = formatTime(elapsedTime);
          }

          // Initialize variables
          let elapsedTime = 0;

          // Start the stopwatch automatically when the page loads
          const intervalId = setInterval(updateStopwatch, 1000);
        </script>
      </div>

      <div class="w-1/2">
        <form method="post">
          <div class="w-4/5 mx-auto border rounded-md border-slate-400 mb-5 pb-3.5 bg-white">
            <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
            <div class="mt-2">
              <input name="submitter" placeholder="" type="text" required class="block w-1/2 rounded-md border-0 py-1.5 px-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="w-4/5 border mx-auto rounded-md border-slate-400 shadow-sm mb-6 px-4 py-3.5 bg-white">

            <div class="w-full flex items-center mb-1">
              <div class="text-lg font-semibold">
                <?= $quiz["quiz_name"] ?>
              </div>
              <div class="mx-1 text-slate-400">
                —
              </div>
              <?= $quiz["course_name"] ?>
            </div>

          </div>


          <?php foreach ($rows as $index => $row) : ?>
            <div class="border shadow-sm mx-auto w-4/5 rounded-md mb-6 px-4 py-3.5 border-slate-400 bg-white">
              <div class="flex items-center">
                <div class="grow">
                  <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
                </div>
                <div class="mr-3">
                  <!-- <button type="button" value="<?= $index ?>" class="edit-question-button rounded-md py-1 px-2 w-[65px] text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</button> -->
                </div>
                <div>
                  <!-- <form method="post">
                <button type="submit" name="delete-question-button" value="<?= $row["id"] ?>" class="w-[65px] rounded-md text-sm font-semibold bg-red-600 text-white py-1 px-2 hover:bg-red-500">Delete</button>
              </form> -->
                </div>
              </div>
              <p class="my-4 text-sm leading-6 text-slate-500"><?= $row["question"] ?></p>

              <div class="my-4">
                <?php $h = "question_" . $row["id"]; ?>

                <?php if ($row["type"] == "MC") : ?>
                  <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
                    <div class="flex items-center gap-x-3 my-2">
                      <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                      <label for="<?= $h . "_" . $index ?>" class="block text-sm leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                    </div>
                  <?php endforeach ?>
                <?php elseif ($row["type"] == "TF") : ?>
                  <div class="flex items-center gap-x-3 my-2">
                    <input id="<?= $h . "True" ?>" name="<?= $h ?>" type="radio" value="True" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="<?= $h . "True" ?>" class="block text-sm leading-6 text-gray-900">True</label>
                  </div>
                  <div class="flex items-center gap-x-3">
                    <input id="<?= $h . "False" ?>" name="<?= $h ?>" type="radio" value="False" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="<?= $h . "False" ?>" class="block text-sm leading-6 text-gray-900">False</label>
                  </div>
                <?php elseif ($row["type"] == "OE") : ?>
                  <div class="flex items-center gap-x-3 my-2">
                    <textarea id="<?= $h ?>" name="<?= $h ?>" placeholder="Type your answer here" required rows="3" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                  </div>
                <?php endif; ?>
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
      <div class="w-1/4">

      </div>
    </div>
  </div>
</body>

</html>
