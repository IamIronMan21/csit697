<?php

require "../utils.php";

session_start();

$submission_id = $_SESSION["submission_id"];

// echo $submission_id;

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
  q.type as type,
  q.content AS question,
  GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
  r.content AS response,
  a.content AS answer
FROM questions q
JOIN responses r ON q.id = r.question_id
LEFT JOIN answers a ON q.id = a.question_id
LEFT JOIN choices c ON q.id = c.question_id
WHERE q.quiz_id = ? AND r.submission_id = ?
GROUP BY q.id
";
$stmt = prepare_and_execute($sql, [$submission["quiz_id"], $submission_id]);
$rows = $stmt->fetchAll();

$num_correct = 0;

foreach ($rows as $row) {
  if ($row["response"] == $row["answer"]) {
    $num_correct++;
    $sql = "UPDATE responses SET score = 1 WHERE submission_id = ? AND question_id = ?";
    prepare_and_execute($sql, [$submission_id, $row["id"]]);
  }
}

$num_questions = $stmt->rowCount();
$grade = round(($num_correct / $num_questions) * 100, 2);

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

    <div class="flex w-full">
      <div class="w-1/4">
        <div class="border-slate-400 flex justify-end sticky top-[16px]">
          <a href="../start.php" id="" class="flex w-1/3 items-center justify-center rounded-md bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            <div class="text-center ml-2">
              Return
            </div>
          </a>
        </div>
      </div>
      <div class="w-1/2">
        <div class="w-4/5 mx-auto">
          <div class="px-4 sm:px-0">
            <?= display_success_message("Your quiz has been submitted. View your results below."); ?>
          </div>
          <div class="mt-6 border-y border-gray-300">
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
        </div>

        <div class="flex items-center justify-center my-6 w-4/5 mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
          </svg>
          <p class="text-slate-500 text-[14px] pl-1.5 select-none">
            Open-ended answers will be graded manually by the tutor.
          </p>
        </div>

        <form method="" class="mx-auto">

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
            <div class="border shadow-sm mx-auto w-4/5 rounded-md mb-6 px-4 py-3.5 bordr-slate-400 bg-white <?= $border_color ?>">
              <!-- <div class="border rounded-lg my-10 px-4 py-2 border-2 <?= $border_color ?>"> -->
              <div class="flex items-center h-[28px]">
                <div class="grow ">
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
                  <div class="flex items-center gap-x-3">
                    <textarea id="<?= $h ?>" name="<?= $h ?>" rows="3" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><?= $row["response"]; ?></textarea>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

        </form>
      </div>
      <div class="w-1/4">

      </div>

    </div>

  </div>

</body>

</html>
