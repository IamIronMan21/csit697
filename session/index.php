<?php

require "../utils.php";

session_start();

$quiz_id = $_SESSION["quiz_id"];

$dbh = connect_to_database();

if (isset($_POST["submit"])) {
  echo "submitted";
}

$sql = <<<EOD
SELECT q.id AS id,
       q.content AS question,
       GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
       a.content AS answer
FROM questions q
JOIN choices c ON q.id = c.question_id
LEFT JOIN answers a ON q.id = a.question_id
WHERE q.quiz_id = $quiz_id
GROUP BY q.id, q.content, a.content;
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

      <?php foreach ($rows as $index => $row) : ?>
        <div class="border rounded-lg my-10 px-4 py-2 border-slate-300">
          <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
          <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
          <div class="mt-6 space-y-2">
            <?php $h = uniqid(); ?>
            <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
              <div class="flex items-center gap-x-3">
                <input id="<?= $choice ?>" name="<?= $h ?>" type="radio">
                <label for="<?= $choice ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
              </div>
            <?php endforeach ?>
          </div>

          <!-- <div class="mt-6 space-y-6">
            <div class="flex items-center gap-x-3">
              <input id="push-everything" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
              <label for="push-everything" class="block text-sm font-medium leading-6 text-gray-900">Everything</label>
            </div>
            <div class="flex items-center gap-x-3">
              <input id="push-email" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
              <label for="push-email" class="block text-sm font-medium leading-6 text-gray-900">Same as email</label>
            </div>
            <div class="flex items-center gap-x-3">
              <input id="push-nothing" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
              <label for="push-nothing" class="block text-sm font-medium leading-6 text-gray-900">No push notifications</label>
            </div>
          </div> -->
        </div>
      <?php endforeach; ?>


      <button name="submit" class="mb-14">submit</button>
    </form>

  </div>
</body>

</html>
