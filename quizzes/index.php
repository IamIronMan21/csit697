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

$sql = "SELECT * FROM courses WHERE tutor_id = ?";
$stmt = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$courses = $stmt->fetchAll();

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

  <div class="mx-auto bg-white min-h-screen px-12 pt-4">

    <div class="flex mb-4 items-center">
      <h1 class="grow text-xl font-medium">Quizzes</h1>
      <button id="show-dialog" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">+ New quiz</button>
    </div>

    <div class="w-full h-fit border border-slate-500 shadow-sm rounded-lg mb-10 overflow-hidden">
      <table class="w-full">
        <thead class="border-b border-slate-500 h-[35px] text-[15px]">
          <th class="font-semibold w-[4%] pl-1">#</th>
          <th class="font-semibold text-left w-[30%] pl-8">Quiz</th>
          <th class="font-semibold text-left w-[21%]">Course</th>
          <th class="font-semibold text-left w-[18%]">Code</th>
          <th class="font-semibold text-left w-[15%]">Date Created</th>
          <th class="font-semibold w-[6%]"></th>
          <th class="font-semibold w-[6%]"></th>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($rows as $index => $row) : ?>
            <tr class="h-[45px] <?= ($index % 2 == 1) ? "bg-slate-100" : ""; ?>">
              <td class="text-center font-medium pl-1"><?= $index + 1 ?> </td>
              <td class="pl-8"><?= $row["quiz_name"] ?></td>
              <td><?= $row["course_name"] ?></td>
              <td class="">
                <span>
                  <button type="button" onclick="copyTextToClipboard(<?= $row['code'] ?>, this)" class="rounded-md bg-white px-4 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"><?= $row["code"] ?></button>
                </span>
              </td>
              <td class="text-gray-500">
                <?= (new DateTime($row["created_at"]))->format('m/d/Y') ?>
              </td>
              <td class="text-blue-500 underline">
                <a href="./edit.php?quiz_id=<?= $row["id"] ?>" class="text-indigo-600 underline hover:text-indigo-500">
                  Edit
                </a>
              </td>
              <td>
                <a href="#" class="text-indigo-600 underline hover:text-indigo-500">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%]" id="dialog">
      <form method="post" class="px-8 mx-auto pt-6 pb-8">
        <div class="space-y-10">
          <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">New Quiz</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Create a new quiz here.</p>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Course</label>
                <div class="mt-2">
                  <input list="courses" type="text" name="course-name" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                  <datalist id="courses">
                    <?php foreach ($courses as $course) : ?>
                      <option value="<?= strval($course[1]) ?>"></option>
                    <?php endforeach; ?>
                  </datalist>
                </div>
              </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Quiz Name</label>
                <div class="mt-2">
                  <input name="quiz-name" type="text" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm font-semibold leading-6 text-gray-900" id="js-close">Cancel</button>
            <button type="submit" name="new-quiz" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add</button>
          </div>
      </form>
    </dialog>

    <script>
      const showBtn = document.getElementById("show-dialog");
      const dialog = document.getElementById("dialog");
      const jsCloseBtn = dialog.querySelector("#js-close");

      showBtn.addEventListener("click", () => {
        dialog.showModal();
      });

      jsCloseBtn.addEventListener("click", (e) => {
        e.preventDefault();
        dialog.close();
      });
    </script>

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
