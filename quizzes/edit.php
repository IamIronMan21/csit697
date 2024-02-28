<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

$quiz_id = $_GET["quiz_id"];

if (isset($_POST["delete-question-button"])) {
  $question_id = $_POST["delete-question-button"];

  $sql = "DELETE FROM questions WHERE id = ?";
  prepare_and_execute($sql, [$question_id]);

  header("Location: ./edit.php?quiz_id={$_GET["quiz_id"]}");
  exit;
}

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

if (isset($_POST["rename-save-button"])) {
  $sql = "UPDATE quizzes SET name = ? WHERE id = ? LIMIT 1";
  prepare_and_execute($sql, [$_POST["new-quiz-name"], $quiz_id]);
  header("Location: ./edit.php?quiz_id={$_GET["quiz_id"]}");
  exit;
}

$sql = "SELECT * FROM quizzes WHERE id = ? LIMIT 1";
$stmt = prepare_and_execute($sql, [$quiz_id]);
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

$sql = "
SELECT q.id AS id,
       q.content AS question,
       GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
       a.content AS answer
FROM questions q
JOIN choices c ON q.id = c.question_id
LEFT JOIN answers a ON q.id = a.question_id
WHERE q.quiz_id = ?
GROUP BY q.id, q.content, a.content;
";
$stmt = $dbh->prepare($sql);
$stmt->execute([$quiz_id]);

// foreach ($stmt->fetchAll() as $row) {
//   echo $row["id"] . " " . $row["question"] . $row["choices"] . "<br>";
//   echo $row["choices"];
//   echo "<br>";
// }

// $choices = explode("|", $row["choices"]);

$questions = $stmt->fetchAll();

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

  <div class="mx-auto bg-white border-slate-500 min-h-screen px-12">

    <div class="flex w-full min-h-screen">
      <div class="border-r pt-4 pr-6 mr-10 border-slate-400 pl-4 pr-10">
        <div class="pt-4 border-slate-400 px-10 text-center">
          <a href="./index.php" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            ‹&nbsp;Back
          </a>
        </div>
      </div>

      <div class="w-2/3 pt-4">
        <div class="border rounded-xl border-slate-400 mb-3 px-4 py-3 bg-white">
          <h1 class="text-lg font-bold"><?= $course["name"] ?></h1>
          <h1><?= $quiz["name"] ?></h1>
          <div class="flex">
            <h1>Code:</h1>
            <button type="button" onclick="copyTextToClipboard(<?= $quiz['code'] ?>, this)" class="rounded-md bg-white px-4 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"><?= $quiz["code"] ?></button>
          </div>
        </div>



        <script>
          function updateWordCount(textarea) {
            const wordCountDisplay = document.getElementById('wordCount');
            const wordCount = textarea.value.trim().split(/\s+/).length;
            wordCountDisplay.textContent = `Word count: ${wordCount}/300`;

            // You can also add logic to prevent submission if the word count exceeds the limit.
            // For example:
            // if (wordCount > 300) {
            //     alert('Word limit exceeded! Please limit your response to 300 words.');
            //     textarea.value = textarea.value.split(/\s+/).slice(0, 300).join(' ');
            // }
          }
        </script>

        <?php foreach ($questions as $index => $row) : ?>
          <div class="border mx-auto w-4/5 rounded-lg my-10 px-4 py-2 border-slate-300 bg-white">
            <div class="flex">
              <div class="grow">
                <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
              </div>
              <div>
                <form method="post">
                  <button type="submit" name="delete-question-button" value="<?= $row["id"] ?>">delete</button>
                </form>
              </div>
            </div>
            <p class="mt-1 text-sm leading-6 text-gray-600"><?= $row["question"] ?></p>
            <div class="mt-6 space-y-2">
              <?php $h = uniqid("", true); ?>
              <?php foreach (explode("|", $row["choices"]) as $index => $choice) : ?>
                <div class="flex items-center gap-x-3">
                  <input id="<?= htmlspecialchars($choice) ?>" name="<?= $h ?>" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                  <label for="<?= htmlspecialchars($choice) ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                </div>
              <?php endforeach ?>
              <div class="text-green-700">answer: <?= $row["answer"] ?></div>
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
      </div>
      <div class="border-l pl-6 ml-10 border-slate-400 pt-4 flex flex-col space-y-2">
        <button id="show-dialog" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          + New question
        </button>

        <button id="show-rename-dialog" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
          Rename
        </button>
        <button class="rounded-md bg-indigo-300 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          Clone
        </button>
      </div>
    </div>

    <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%] h-[405px]" id="dialog">
      <div class="tab w-full flex">
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-600 hover: hover:border-indigo-600" onclick="openCity(event, 'London')">Multiple Choice</button>
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-600 hover: hover:border-indigo-600" onclick="openCity(event, 'Paris')">True/False</button>
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-600 hover: hover:border-indigo-600" onclick="openCity(event, 'Tokyo')">Open-Ended</button>
      </div>
      <hr class="mb-2">

      <style>
        .tabcontent {
          display: none;
        }
      </style>

      <div id="London" class="tabcontent">
        <form method="post" class="p-2">
          <div class="h-full">
            <input class="border w-1/2 block" type="text" name="question" placeholder="question" required>

            <!-- Choices for Multiple Choice Question -->
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
          </div>
          <!-- <input type="submit" name="new-mc-question" value="Submit"> -->
          <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="">Cancel</button>
            <button type="submit" name="new-mc-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
          </div>
        </form>
      </div>

      <div id="Paris" class="tabcontent">
        <!-- Add True/False question -->
        <div class="">
          <p>Add True/False question</p>
          <form method="post" class="p-2">
            <input class="border w-1/2 block" type="text" name="question" placeholder="True/False question" required>

            <!-- True/False Options -->
            <div class="flex items-center gap-x-3">
              <input id="true-option" name="true_false_option" type="radio" value="True" required>
              <label for="true-option" class="block text-sm font-medium leading-6 text-gray-900">True</label>
            </div>
            <div class="flex items-center gap-x-3">
              <input id="false-option" name="true_false_option" type="radio" value="False" required>
              <label for="false-option" class="block text-sm font-medium leading-6 text-gray-900">False</label>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
              <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="">Cancel</button>
              <button type="submit" name="new-tf-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
            </div>
          </form>
        </div>
      </div>

      <div id="Tokyo" class="tabcontent">
        <div class="">
          <p>Add Open-Ended question</p>
          <form method="post" class="p-2">
            <input class="border w-1/2 block" type="text" name="question" placeholder="Open-Ended question" required>

            <!-- Open-Ended Textarea with word count limit and counter -->
            <textarea class="border block w-1/2" name="open_ended_answer" placeholder="Student's answer" required oninput="updateWordCount(this)" maxlength="300"></textarea>
            <div id="wordCount" class="text-sm text-gray-500">Word count: 0/300</div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
              <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="">Cancel</button>
              <button type="submit" name="new-oe-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
            </div>
          </form>
        </div>
      </div>

      <form method="post" class="px-8 mx-auto pt-6 pb-8">

        <!-- <div class="mt-6 flex items-center justify-end gap-x-6">
          <button type="button" class="text-sm font-semibold leading-6 text-gray-900" id="js-close">Cancel</button>
          <button type="submit" name="new-course-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add</button>
        </div> -->
      </form>
    </dialog>

    <!-- Rename modal -->
    <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%] h-[405px]" id="rename-dialog">
      <form method="post" class="mt-5 mb-14">

        <div class="space-y-12">
          <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Rename Quiz</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Update the name of this quiz.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                  <input name="new-quiz-name" type="text" autocomplete="email" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $quiz["name"] ?>">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
          <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="">Cancel</button>
          <button type="submit" name="rename-save-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
      </form>
    </dialog>

    <script>
      function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
      }
      openCity(null, 'London')
    </script>

    <script>
      const showBtn = document.getElementById("show-dialog");
      const dialog = document.getElementById("dialog");
      const jsCloseBtns = document.getElementsByClassName("js-close");

      const showRenameDialogButton = document.getElementById("show-rename-dialog");
      const renameDialog = document.getElementById("rename-dialog");

      showRenameDialogButton.addEventListener("click", () => {
        renameDialog.showModal();
      })

      showBtn.addEventListener("click", () => {
        dialog.showModal();
      });

      for (const b of jsCloseBtns) {
        b.addEventListener("click", (e) => {
          e.preventDefault();
          dialog.close();
          renameDialog.close();
        });
      }
    </script>
    <script>
      function copyTextToClipboard(textToCopy, element) {
        // Create a temporary input element
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

        // Remove the temporary input element from the document
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
