<?php

require "../utils.php";

session_start();

$submission_id = $_GET["submission_id"];

if (isset($_POST["edit-button"])) {
  $sql = "UPDATE responses SET score = ? WHERE id = ?";
  prepare_and_execute($sql, [$_POST["score-input"], $_POST["response-id"]]);

  $_SESSION["success_message"] = "Score has been updated successfully.";
  header("Location: ./edit.php?submission_id=" . $submission_id);
  exit;
}

$sql = "
SELECT
  c.name AS course,
  q.name AS quiz
FROM quizzes q, submissions s, courses c
WHERE
  s.id = ? AND q.id = s.quiz_id AND c.id = q.course_id
LIMIT
  1;
";
$stmt = prepare_and_execute($sql, [$submission_id]);
$row = $stmt->fetch();
$course = $row["course"];
$quiz = $row["quiz"];

$sql = "SELECT * FROM submissions WHERE id = ? LIMIT 1";
$stmt = prepare_and_execute($sql, [$submission_id]);
$submission = $stmt->fetch();

$sql = "
SELECT
  q.id AS id,
  q.type AS type,
  q.content AS question,
  GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
  a.content AS answer,
  r.id AS response_id,
  r.content AS response,
  r.score AS score
FROM
  questions q
  JOIN responses r ON q.id = r.question_id
  LEFT JOIN choices c ON q.id = c.question_id
  LEFT JOIN answers a ON q.id = a.question_id
WHERE
  q.quiz_id = ?
  AND r.submission_id = ?
GROUP BY
  q.id;
";
$stmt = prepare_and_execute($sql, [$submission["quiz_id"], $submission["id"]]);
$rows = $stmt->fetchAll();

$total_score = 0;

foreach ($rows as $row) {
  $total_score += $row["score"];
}

$num_questions = $stmt->rowCount();
$grade = round(($total_score / $num_questions) * 100, 2);

// echo "<pre style='font-size: 9px;'>";
// print_r($rows);
// echo "</pre>";

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
  <nav class="bg-gray-800 px-4">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">

        </div>
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="flex flex-shrink-0 items-center">
            <h1 class="text-white font-['Literata'] text-xl mr-1">Quizify</h1>
          </div>
          <div class="hidden sm:ml-6 sm:block">
            <div class="flex space-x-4">
              <a href="../courses/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Courses</a>
              <a href="../quizzes/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Submissions</a>
            </div>
          </div>
        </div>
        <div class="inset-y-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
          <div class="relative ml-3">
            <div class="flex space-x-4">
              <a href="../settings/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Settings</a>
              <a href="../index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Sign out</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="mx-auto max-w-7xl bg-white min-h-screen px-12 pt-4">

    <div class="flex w-full">
      <div class="w-1/4">
        <div class="border-slate-400 flex justify-end sticky top-[16px]">
          <a href="./index.php" id="" class="flex w-1/3 items-center justify-center rounded-md bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            <div class="text-center ml-2">
              Back
            </div>
          </a>
        </div>
      </div>
      <div class="w-1/2">
        <div class="w-4/5 mx-auto">

          <?php
          if (isset($_SESSION["success_message"])) {
            display_success_message($_SESSION["success_message"]);
            unset($_SESSION["success_message"]);
          }
          ?>

          <div class="mb-8 border-y border-gray-300">
            <dl class="divide-y divide-gray-300">
              <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Submitter</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $submission["submitter"] ?></dd>
              </div>
              <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Course</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $course ?></dd>
              </div>
              <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Quiz</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $quiz ?></dd>
              </div>
              <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Grade</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $grade ?></dd>
              </div>
            </dl>
          </div>


          <form method="" class="mb-14">

            <?php foreach ($rows as $index => $row) : ?>
              <?php
              $border_color = "";
              if ($row["type"] == "OE") {
                $border_color = "border-slate-400";
              } else {
                if ($row["response"] ==  $row["answer"]) {
                  $border_color = "border-green-500";
                } else {
                  $border_color = "border-red-500";
                }
              }
              ?>
              <div class="border shadow-sm mx-auto rounded-md mb-6 px-4 py-3.5 bordr-slate-400 bg-white <?= $border_color ?>">
                <div class="flex items-center h-[28px]">
                  <div class="grow">
                    <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
                  </div>
                  <div>
                    <div class="flex items-center border-slate-400 rounded-md py-1">
                      <div class="px-2">
                        <p class="text-sm">Score: <?= $row["score"]; ?></p>
                      </div>
                      <?php if ($row["type"] == "OE") : ?>
                        <div class="w-fit ml-1">
                          <button type="button" value="<?= $row["response_id"] . "-" . $row["score"]; ?>" class="edit-button cursor-pointer rounded-md bg-white px-3 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Edit
                          </button>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <!-- <div class="flex items-center">
                  <legend class="grow text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
                </div> -->
                <p class="my-4 text-sm leading-6 text-slate-500"><?= $row["question"] ?></p>
                <div class="my-4">
                  <?php $h = "question_" . $row["id"]; ?>

                  <?php if ($row["type"] == "MC") : ?>

                    <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
                      <div class="flex items-center gap-x-3 my-2">
                        <?php if ($row["response"] == $choice) : ?>
                          <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" checked>
                          <label for="<?= $h . "_" . $index ?>" class="block text-sm leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                          <?php if ($row["answer"] == $choice) : ?>
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                              Correct
                            </span>
                          <?php else : ?>
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                              Incorrect
                            </span>
                          <?php endif ?>
                        <?php else : ?>
                          <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" disabled>
                          <label for="<?= $h . "_" . $index ?>" class="block text-sm leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                          <?php if ($choice == $row["answer"] && $row["response"] != $row["answer"]) : ?>
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                              Answer
                            </span>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    <?php endforeach ?>

                  <?php elseif ($row["type"] == "TF") : ?>

                    <div class="flex items-center gap-x-3 my-2">
                      <?php if ($row["response"] == "True") : ?>
                        <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="True" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" checked>
                        <label id="<?= $h ?>" class="block text-sm leading-6 text-gray-900">True</label>
                        <?php if ($row["answer"] == "True") : ?>
                          <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Correct
                          </span>
                        <?php else : ?>
                          <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                            Incorrect
                          </span>
                        <?php endif ?>
                      <?php else : ?>
                        <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="True" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" disabled>
                        <label id="<?= $h ?>" class="block text-sm leading-6 text-gray-900">True</label>
                      <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-x-3 my-2">
                      <?php if ($row["response"] == "False") : ?>
                        <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="False" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" checked>
                        <label id="<?= $h ?>" class="block text-sm leading-6 text-gray-900">False</label>
                        <?php if ($row["answer"] == "False") : ?>
                          <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Correct
                          </span>
                        <?php else : ?>
                          <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                            Incorrect
                          </span>
                        <?php endif ?>
                      <?php else : ?>
                        <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="False" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" disabled>
                        <label id="<?= $h ?>" class="block text-sm leading-6 text-gray-900">False</label>
                      <?php endif; ?>
                    </div>

                  <?php elseif ($row["type"] == "OE") : ?>
                    <div class="flex items-center gap-x-3 my-2">
                      <textarea id="<?= $h ?>" name="<?= $h ?>" disabled rows="3" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><?= $row["response"]; ?></textarea>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </form>
        </div>
      </div>
    </div>
    <div class="w-1/4"></div>
  </div>


  </div>

  <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%]" id="edit-dialog">
    <form method="post" class="px-8 mx-auto pt-6 pb-8">
      <div class="space-y-10">
        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Edit Score</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Open-ended response scores ranges from 0 to 1 with increments of 0.1.</p>

          <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="sm:col-span-4">
              <label class="block text-sm font-medium leading-6 text-gray-900">Score</label>
              <div class="mt-2">
                <input type="hidden" name="response-id" id="response-id">
                <input id="score-input" name="score-input" type="number" min="0" max="1" step="0.1" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-x-6">
        <button type="button" class="text-sm font-semibold leading-6 text-gray-900" id="js-close">Cancel</button>
        <button type="submit" name="edit-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          Save
        </button>
      </div>
    </form>
  </dialog>

  <script>
    const editButtons = document.getElementsByClassName("edit-button");
    const editDialog = document.getElementById("edit-dialog");

    const scoreInput = document.getElementById("score-input");
    const responseId = document.getElementById("response-id");

    const jsCloseBtn = document.getElementById("js-close");

    for (const b of editButtons) {
      b.addEventListener("click", (e) => {
        e.preventDefault();
        editDialog.showModal();
        console.log(b.value);
        const [responseIdValue, score] = b.value.split("-");
        scoreInput.value = score;
        responseId.value = responseIdValue;
      });
    }

    jsCloseBtn.addEventListener("click", (e) => {
      e.preventDefault();
      editDialog.close();
    });
  </script>

</body>

</html>
