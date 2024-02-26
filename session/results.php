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
  choices.choices AS choices,
  r.content AS response,
  a.content AS answer
FROM questions q
JOIN answers a ON q.id = a.question_id
JOIN responses r ON q.id = r.question_id
JOIN (
  SELECT question_id, GROUP_CONCAT(DISTINCT content ORDER BY choices.id SEPARATOR '|') AS choices
  FROM choices
  GROUP BY question_id
) choices ON q.id = choices.question_id
WHERE q.quiz_id = ?
GROUP BY q.id;
";
$stmt = prepare_and_execute($sql, [$submission["quiz_id"]]);
$rows = $stmt->fetchAll();

echo "<pre style='font-size: 9px;'>";
print_r($rows);
echo "</pre>";

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
    name: <?= $submission["submitter"] ?>
    <div>
      <a href="../index.php">Return</a>
    </div>

    <form method="">
      <fieldset disabled="disabled">
        <?php foreach ($rows as $index => $row) : ?>
          <div class="border rounded-lg my-10 px-4 py-2 border-slate-300">
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
