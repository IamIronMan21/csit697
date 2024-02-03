<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

$quiz_id = $_GET["quiz_id"];

if (isset($_POST["new-mc-question"])) {
  $sql = "INSERT INTO questions (type, content, quiz_id) VALUES (?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(["MC", $_POST["question"], $quiz_id]);

  $question_id = $dbh->lastInsertId();

  foreach ($_POST["choice"] as $choice) {
    $sql = "INSERT INTO choices (content, question_id) VALUES (?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$choice, $question_id]);
  }

  $sql = "INSERT INTO answers (content, question_id) VALUES (?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["choice"][$_POST["correct-choice-number"] - 1], $question_id]);

  header("Location: ./edit.php?quiz_id={$_GET["quiz_id"]}");
  exit;
}

//
$sql = "SELECT * FROM quizzes WHERE id = ? LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute([$quiz_id]);

$quiz = $stmt->fetch();

$course_id = $quiz["course_id"];

$sql = "SELECT * FROM courses WHERE id = ? LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute([$course_id]);

$course = $stmt->fetch();

//
$sql = "SELECT * FROM questions WHERE quiz_id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$quiz_id]);

$questions = $stmt->fetchAll();

$sql = <<<EOD
SELECT q.id as id, q.content AS question_content, GROUP_CONCAT(c.content SEPARATOR '|') AS choices
FROM questions q
JOIN choices c ON q.id = c.question_id
GROUP BY q.id, q.content;
EOD;
$stmt = $dbh->prepare($sql);
$stmt->execute([]);

foreach ($stmt->fetchAll() as $row) {
  echo $row["id"] . " " . $row["question_content"] . $row["choices"] . "<br>";
  echo $row["choices"];
  echo "<br>";
}

$choices = explode("|", $row["choices"]);
var_dump($choices);

?>

<!doctype html>
<html lang="en">

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
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Quizzes</a>
              <a href="../submissions/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Submissions</a>
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

  <div class="mx-auto bg-white border-slate-500 min-h-screen px-12 pt-3">

    <a href="./index.php">back</a>

    <div class="my-3">
      <h1 class="text-lg font-bold"><?= $course["name"] ?></h1>
      <h1><?= $quiz["name"] ?></h1>
    </div>

    <div class="border">
      <p>Add multiple choice question</p>
      <form method="post" class="p-2">
        <input class="border w-1/2 block" type="text" name="question" placeholder="question" required>
        <div class="flex">
          <span>1</span>
          <input class="border block w-1/2" type="text" name="choice[]" placeholder="choice 1" required>
        </div>
        <div class="flex">
          <span>2</span>
          <input class="border block w-1/2" type="text" name="choice[]" placeholder="choice 2" required>
        </div>
        <div class="flex">
          <span>3</span>
          <input class="border block w-1/2" type="text" name="choice[]" placeholder="choice 3" required>
        </div>
        <div class="flex">
          <span>4</span>
          <input class="border block w-1/2" type="text" name="choice[]" placeholder="choice 4" required>
        </div>
        <div class="flex">
          <span>correct answer</span>
          <input type="number" class="border" name="correct-choice-number" min="1" max="4" placeholder="1-4" required>
        </div>

        <input type="submit" name="new-mc-question" value="Submit">
      </form>
    </div>

    <table class="w-full border">
      <thead>
        <tr>
          <td>#</td>
          <td>Question</td>
          <td>Type</td>
          <td>Choice #1</td>
          <td>Choice #2</td>
          <td>Choice #3</td>
          <td>Choice #4</td>
          <td>Answer</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $index => $q) : ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= $q["content"] ?></td>
            <td><?= $q["type"] ?></td>
            <td>.</td>
            <td>.</td>
            <td>.</td>
            <td>.</td>
            <td>.</td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>

    <script>
      function copyTextToClipboard(textToCopy, element) {
        // Create a temporary input element
        let tempInput = document.createElement('input');

        // Set the value of the temporary input element to the provided text
        tempInput.value = textToCopy;

        // Append the input element to the document
        document.body.appendChild(tempInput);

        // Select the text inside the input element
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text to the clipboard
        document.execCommand('copy');

        // Remove the temporary input element from the document
        document.body.removeChild(tempInput);

        // Display a temporary "Copied!" message in the element
        element.textContent = 'Copied!';

        // Reset the message after a short delay (e.g., 2 seconds)
        setTimeout(function() {
          element.textContent = textToCopy;
        }, 1500);
      }
    </script>
</body>

</html>
