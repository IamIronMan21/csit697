<?php

require "../utils.php";

session_start();

$submission_id = $_GET["submission_id"];

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
  a.content AS answer,
  r.score AS score
FROM questions q
JOIN responses r ON q.id = r.question_id
LEFT JOIN choices c ON q.id = c.question_id
LEFT JOIN answers a ON q.id = a.question_id
WHERE q.quiz_id = ? AND r.submission_id = ?
GROUP BY q.id
";
$stmt = prepare_and_execute($sql, [$submission["quiz_id"], $submission["id"]]);
$rows = $stmt->fetchAll();

$num_correct = 0;

foreach ($rows as $row) {
  if ($row["response"] == $row["answer"]) {
    $num_correct++;
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

  <div class="mx-auto bg-white min-h-screen px-12 pt-4 w-1/2">
    <div>
      <a href="./index.php" class="text-gray-900 hover:text-gray-700">‹ Back</a>
    </div>
    <br>
    <div>
      name: <?= $submission["submitter"] ?>
    </div>
    <div>
      grade: <?= $grade ?>
    </div>

    <form method="">
      <fieldset disabled="disabled">
        <?php foreach ($rows as $index => $row) : ?>
          <div class="border rounded-lg my-10 px-4 py-2 border-2 <?= ($row["response"] ==  $row["answer"]) ? "border-green-500" : "border-red-500" ?>">
            <div class="flex">
              <legend class="grow text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
              <div>
                score: <?= $row["score"]; ?>
              </div>
            </div>
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

              <?php elseif ($row["type" == "TF"]) : ?>

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
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <fieldset>
    </form>
  </div>

</body>

</html>
