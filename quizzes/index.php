<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

if (isset($_POST["new-quiz"])) {
  $sql = "SELECT id FROM courses WHERE name = ?";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(([$_POST["course-name"]]));

  $row = $stmt->fetch();
  $course_id = $row["id"];

  $sql = "INSERT INTO quizzes (name, code, course_id) VALUES (?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $success = $stmt->execute([$_POST["quiz-name"], get_new_quiz_code(), $course_id]);

  header("Location: .");
  exit;
}

$sql = <<<EOD
SELECT Q.id, C.name as course_name, Q.name as quiz_name, Q.created_at as created_at, Q.code as code
FROM courses C, quizzes Q
WHERE c.id = Q.course_id AND C.tutor_id = {$_SESSION["tutor_id"]};
EOD;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

$courses = get_courses_for_tutor($_SESSION["tutor_id"]);

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

    <form method="post">

      <input list="courses" class="border" type="text" name="course-name" placeholder="Course name">
      <datalist id="courses">
        <?php foreach ($courses as $course) : ?>
          <option value="<?= strval($course[1]) ?>"></option>
        <?php endforeach; ?>
      </datalist>

      <input class="border" type="text" name="quiz-name" placeholder="New quiz name">

      <input type="submit" name="new-quiz" value="Add">
    </form>

    <hr class="mt-4">

    <div class="flex py-4 items-center">
      <h1 class="grow text-2xl">Quizzes</h1>
      <button class="py-1 px-4 border bg-slate-100 rounded border-slate-300 hover:bg-slate-200">+ New quiz</button>
    </div>

    <table class="w-full border mb-12">
      <thead>
        <th>#</th>
        <th>Quiz</th>
        <th>Course</th>
        <th>Code</th>
        <th>Date Created</th>
        <th></th>
      </thead>
      <tbody>
        <?php foreach ($rows as $index => $row) : ?>
          <tr class="text-center h-[45px] border-t">
            <td><?= $index + 1 ?> </td>
            <td><?= $row["quiz_name"] ?></td>
            <td><?= $row["course_name"] ?></td>
            <td class="w-[10vw]">
              <span>
                <button type="button" onclick="copyTextToClipboard(<?= $row['code'] ?>, this)" class="rounded-md bg-white px-4 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"><?= $row["code"] ?></button>
              </span>
            </td>
            <td><?= $row["created_at"] ?></td>
            <td class="text-blue-500 underline">
              <a href="./edit.php?quiz_id=<?= $row["id"] ?>">
                Edit
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <script>
      function copyTextToClipboard(textToCopy, element) {
        let tempInput = document.createElement("input");

        // Set the value of the temporary input element to the provided text
        tempInput.value = textToCopy;

        // Append the input element to the document
        document.body.appendChild(tempInput);

        // Select the text inside the input element
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text to the clipboard
        document.execCommand("copy");

        document.body.removeChild(tempInput);

        // Display a temporary "Copied!" message in the element
        element.textContent = "Copied!";

        // Reset the message after a short delay (e.g., 2 seconds)
        setTimeout(function() {
          element.textContent = textToCopy;
        }, 1500);
      }
    </script>
</body>

</html>
