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
  q.content AS question,
  GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
  r.content AS response,
  a.content AS answer
FROM questions q
JOIN responses r ON q.id = r.question_id
JOIN choices c ON q.id = c.question_id
JOIN answers a ON q.id = a.question_id
WHERE q.quiz_id = ? AND r.submission_id = ?
GROUP BY q.id
";
$stmt = prepare_and_execute($sql, [$submission["quiz_id"], $submission_id]);
$rows = $stmt->fetchAll();

$num_correct = 0;

foreach ($rows as $row) {
  if ($row["response"] == $row["answer"]) {
    $num_correct++;
    $sql = "UPDATE responses SET score = 1 WHERE id = ? LIMIT 1";
    prepare_and_execute($sql, [$submission_id]);
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
      name: <?= $submission["submitter"] ?>
    </div>
    <div>
      grade: <?= $grade ?>
    </div>
    <div>
      <a href="../index.php">Return</a>
    </div>

    <form method="">
      <fieldset disabled="disabled">
        <?php foreach ($rows as $index => $row) : ?>
          <div class="border rounded-lg my-10 px-4 py-2 border-2 <?= ($row["response"] ==  $row["answer"]) ? "border-green-500" : "border-red-500" ?>">
            <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
            <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
            <div class="mt-6 space-y-2">
              <?php $h = "question_" . $row["id"]; ?>
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
            </div>
          </div>
        <?php endforeach; ?>
        <fieldset>
    </form>
  </div>

</body>

</html>
