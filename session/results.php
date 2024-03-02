<?php

require "../utils.php";

session_start();

$submission_id = $_SESSION["submission_id"];

// echo $submission_id;

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
    <div>
      <a href="../start.php">Return</a>
    </div>
    <div class="">
      <div class="px-4 sm:px-0">
        <h3 class="text-base font-semibold leading-7 text-gray-900">Results</h3>
        <!-- <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Here are your results.</p> -->
      </div>
      <div class="mt-6 border-t border-gray-100">
        <dl class="divide-y divide-gray-100">
          <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Submitter</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $submission["submitter"] ?></dd>
          </div>
          <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Quiz</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">...</dd>
          </div>
          <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Course</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">...</dd>
          </div>
          <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="text-sm font-medium leading-6 text-gray-900">Grade</dt>
            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= $grade ?></dd>
          </div>
        </dl>
      </div>
    </div>



    <hr>

    <!-- <div>
      name: <?= $submission["submitter"] ?>
    </div>
    <div>
      grade: <?= $grade ?>
    </div> -->

    <p>Note: Open-ended answers will be graded manually.</p>

    <form method="">
      <fieldset disabled="disabled">
        <?php foreach ($rows as $index => $row) : ?>
          <?php
          $border_color = "";
          if ($row["type"] == "OE") {
            $border_color = "border-gray-300";
          } else {
            if ($row["response"] ==  $row["answer"]) {
              $border_color = "border-green-500";
            } else {
              $border_color = "border-red-500";
            }
          }
          ?>
          <div class="border rounded-lg my-10 px-4 py-2 border-2 <?= $border_color ?>">
            <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
            <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
            <div class="mt-6 space-y-2">
              <?php $h = "question_" . $row["id"]; ?>

              <?php if ($row["type"] == "MC") : ?>

                <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
                  <div class="flex items-center gap-x-3">
                    <?php if ($row["response"] == $choice) : ?>
                      <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" required checked>
                    <?php else : ?>
                      <input id="<?= $h . "_" . $index ?>" name="<?= $h ?>" type="radio" value="<?= $choice ?>" required>
                    <?php endif; ?>
                    <label for="<?= $h . "_" . $index ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                  </div>
                <?php endforeach ?>

              <?php elseif ($row["type"] == "TF") : ?>

                <div class="flex items-center gap-x-3">
                  <?php if ($row["response"] == "True") : ?>
                    <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="True" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" checked>
                  <?php else : ?>
                    <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="True" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                  <?php endif; ?>

                  <label id="<?= $h ?>" class="block text-sm font-medium leading-6 text-gray-900">True</label>
                </div>
                <div class="flex items-center gap-x-3">

                  <?php if ($row["response"] == "False") : ?>
                    <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="False" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" checked>
                  <?php else : ?>
                    <input id="<?= $h ?>" name="<?= $h ?>" type="radio" value="False" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                  <?php endif; ?>
                  <label id="<?= $h ?>" class="block text-sm font-medium leading-6 text-gray-900">False</label>
                </div>

              <?php elseif ($row["type"] == "OE") : ?>
                <div class="flex items-center gap-x-3">
                  <textarea id="<?= $h ?>" name="<?= $h ?>" required class="border block w-full rounded p-1 border-slate-300"><?= $row["response"] ?></textarea>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <fieldset>
    </form>
  </div>

</body>

</html>
