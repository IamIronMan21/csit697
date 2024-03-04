<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

$quiz_id = $_GET["quiz_id"];


$sql = "SELECT * FROM quizzes WHERE id = ? LIMIT 1";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$quiz = $stmt->fetch();

$course_id = $quiz["course_id"];

if (isset($_POST["delete-question-button"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

  $question_id = $_POST["delete-question-button"];
  delete_question($question_id);

  $_SESSION["success_message"] = "Question has been deleted.";
  header("Location: ./edit.php?quiz_id=$quiz_id");
  exit;
}

if (isset($_POST["new-mc-question"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

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

  $_SESSION["success_message"] = "Added new question.";
  header("Location: ./edit.php?quiz_id=$quiz_id");
  exit;
}

if (isset($_POST["new-tf-question"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

  $sql = "INSERT INTO questions (type, content, quiz_id) VALUES (?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(["TF", $_POST["question"], $quiz_id]);

  $question_id = $dbh->lastInsertId();

  $sql = "INSERT INTO answers (content, question_id) VALUES (?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["true-false-option"], $question_id]);

  $_SESSION["success_message"] = "Added new question.";
  // header("Location: ./edit.php?quiz_id={$_GET["quiz_id"]}");
  // exit;
}

if (isset($_POST["new-oe-question"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

  $sql = "INSERT INTO questions (type, content, quiz_id) VALUES (?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute(["OE", $_POST["question"], $quiz_id]);

  $_SESSION["success_message"] = "Added new question.";
  // header("Location: ./edit.php?quiz_id={$_GET["quiz_id"]}");
  // exit;
}

if (isset($_POST["edit-question-submit-button"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

  $id = $_POST["edit-question-submit-button"];
  $content = $_POST["question-$id"];

  $sql = "UPDATE questions SET content = ? WHERE id = ?";
  prepare_and_execute($sql, [$content, $id]);

  $type = $_POST["question-$id-type"];

  if ($type == "TF") {
    $new_answer = $_POST["question-$id-edit-true-false-option"];

    $sql = "UPDATE answers SET content = ? WHERE question_id = ?";
    prepare_and_execute($sql, [$new_answer, $id]);
  }

  if ($type == "MC") {
    $sql = "SELECT id, content FROM choices WHERE question_id = ?";
    $stmt = prepare_and_execute($sql, [$id]);
    $choices = $stmt->fetchAll();

    $sql = "SELECT content FROM answers WHERE question_id = ?";
    $stmt = prepare_and_execute($sql, [$id]);
    $answer = $stmt->fetchColumn();

    $choice_number = $_POST["question-$id-new-correct-choice-number"];
    // adjust
    $choice_number -= 1;

    foreach ($choices as $index => $choice) {
      $new_choice = $_POST["question-$id-choice-$index"];

      // if ($choice["content"] == $answer) {
      //   $sql = "UPDATE answers SET content = ? WHERE question_id = ?";
      //   prepare_and_execute($sql, [$new_choice, $id]);
      // }

      $sql = "UPDATE choices SET content = ? WHERE id = ?";
      prepare_and_execute($sql, [$new_choice, $choice["id"]]);
    }

    $sql = "UPDATE answers SET content = ? WHERE question_id = ?";
    prepare_and_execute($sql, [$_POST["question-$id-choice-$choice_number"], $id]);
  }

  header("Location: ./edit.php?quiz_id=$quiz_id");
  exit;
}

if (isset($_POST["rename-save-button"])) {
  if (has_submissions_for_quiz($quiz_id)) {
    $_SESSION["error_message"] = "Cannot edit quiz that already has submissions.";
    header("Location: ./edit.php?quiz_id=$quiz_id");
    exit;
  }

  $sql = "UPDATE quizzes SET name = ? WHERE id = ? LIMIT 1";
  prepare_and_execute($sql, [$_POST["new-quiz-name"], $quiz_id]);
  $_SESSION["success_message"] = "Quiz has been renamed.";
  header("Location: ./edit.php?quiz_id=$quiz_id");
  exit;
}

if (isset($_POST["new-clone-name"])) {
  $sql = "INSERT INTO quizzes (name, code, course_id) VALUES (?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["new-clone-name"], generate_quiz_code(), $course_id]);

  $new_quiz_id = $dbh->lastInsertId();

  $sql = "SELECT id, type, content FROM questions WHERE quiz_id = ?";
  $stmt = prepare_and_execute($sql, [$quiz_id]);
  $rows = $stmt->fetchAll();

  foreach ($rows as $row) {
    $sql = "INSERT INTO questions (type, content, quiz_id) VALUES (?, ?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$row["type"], $row["content"], $new_quiz_id]);

    $new_question_id = $dbh->lastInsertId();

    if ($row["type"] == "MC" || $row["type"] == "TF") {
      // Clone answer
      $sql = "SELECT content FROM answers WHERE question_id = ?";
      $stmt = prepare_and_execute($sql, [$row["id"]]);
      $answer = $stmt->fetchColumn();

      $sql = "INSERT INTO answers (content, question_id) VALUES (?, ?)";
      $stmt = prepare_and_execute($sql, [$answer, $new_question_id]);
    }

    if ($row["type"] == "MC") {
      // Clone choices
      $sql = "SELECT content FROM choices WHERE question_id = ?";
      $stmt = prepare_and_execute($sql, [$row["id"]]);
      $choices = $stmt->fetchAll();
      foreach ($choices as $choice) {
        $sql = "INSERT INTO choices (content, question_id) VALUES (?, ?)";
        prepare_and_execute($sql, [$choice["content"], $new_question_id]);
      }
    }
  }

  $_SESSION["success_message"] = "Clone of {$quiz['name']} has been created.";
  header("Location: ./index.php");
  exit;
}

$sql = "SELECT name FROM courses WHERE id = ? LIMIT 1";
$stmt = prepare_and_execute($sql, [$course_id]);
$course = $stmt->fetch();

$sql = "SELECT COUNT(id) FROM questions WHERE quiz_id = ?";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$num_questions = $stmt->fetchColumn();

$sql = "SELECT COUNT(id) FROM submissions WHERE quiz_id = ?";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$num_submissions = $stmt->fetchColumn();

$sql = "
SELECT
  q.id AS id,
  q.type AS type,
  q.content AS question,
  GROUP_CONCAT(c.content SEPARATOR '|') AS choices,
  a.content AS answer
FROM
  questions q
  LEFT JOIN choices c ON q.id = c.question_id
  LEFT JOIN answers a ON q.id = a.question_id
WHERE
  q.quiz_id = ?
GROUP BY
  q.id;
";
$stmt = prepare_and_execute($sql, [$quiz_id]);
$rows = $stmt->fetchAll();

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

  <div class="mx-auto max-w-7xl bg-white min-h-screen px-12 pt-4">

    <div class="flex w-full min-h-screen">
      <div class="w-1/4 border-slate-400">
        <div class="border-slate-400 flex justify-end">
          <a href="./index.php" id="" class="flex w-1/3 items-center justify-center rounded-md bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            <div class="text-center ml-2">
              Back
            </div>
          </a>
        </div>
      </div>

      <div class="w-1/2">
        <div class="w-4/5 mx-auto">
          <?php
          if (isset($_SESSION["error_message"])) {
            display_error_message($_SESSION["error_message"]);
            unset($_SESSION["error_message"]);
          }
          ?>

          <?php
          if (isset($_SESSION["success_message"])) {
            display_success_message($_SESSION["success_message"]);
            unset($_SESSION["success_message"]);
          }
          ?>
        </div>

        <div class="border w-4/5 mx-auto rounded-md border-slate-400 shadow-sm mb-6 px-4 py-3.5 bg-white">
          <div class="w-full flex items-center mb-1">
            <div class="text-lg font-semibold">
              <?= $quiz["name"] ?>
            </div>
            <div class="mx-1 text-slate-400">
              —
            </div>
            <?= $course["name"] ?>
          </div>
          <div class="flex items-center">
            <div class="grow text-left">
              Questions: <?= $num_questions ?>
            </div>
            <div class="grow text-center">
              Submissions: <?= $num_submissions ?>
            </div>
            <div class="grow text-right">
              <span>Code:&nbsp;</span>
              <div class="inline">
                <button type="button" onclick="copyTextToClipboard(<?= $quiz['code'] ?>, this)" class="rounded-md bg-white px-4 py-1 text-sm w-[83.1px] font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"><?= $quiz["code"] ?></button>
              </div>
            </div>
          </div>
        </div>

        <?php foreach ($rows as $index => $row) : ?>
          <div class="border shadow-sm mx-auto w-4/5 rounded-md mb-6 px-4 py-3.5 border-slate-400 bg-white">
            <div class="flex items-center">
              <div class="grow">
                <legend class="text-sm font-semibold leading-6 text-gray-900">Question #<?= $index + 1; ?></legend>
              </div>
              <div class="mr-3">
                <button type="button" value="<?= $index ?>" class="edit-question-button rounded-md py-1 px-2 w-[65px] text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</button>
              </div>
              <div>
                <form method="post">
                  <button type="submit" name="delete-question-button" value="<?= $row["id"] ?>" class="w-[65px] rounded-md text-sm font-semibold bg-red-600 text-white py-1 px-2 hover:bg-red-500">Delete</button>
                </form>
              </div>
            </div>
            <p class="my-4 text-sm leading-6 text-slate-500"><?= $row["question"] ?></p>
            <div class="my-4">
              <?php $h = uniqid("", true); ?>
              <?php if ($row["type"] == "MC") : ?>
                <?php foreach (explode("|", $row["choices"]) as $choice) : ?>
                  <div class="flex items-center gap-x-3 my-2">
                    <input id="<?= $h . $choice ?>" name="<?= $h ?>" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" disabled>
                    <label for="<?= $h . $choice ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= htmlspecialchars($choice) ?></label>
                    <?php if ($row["answer"] == $choice) : ?>
                      <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                        Answer
                      </span>
                    <?php endif ?>
                  </div>
                <?php endforeach ?>
              <?php elseif ($row["type"] == "TF") : ?>
                <div class="flex items-center gap-x-3 my-2">
                  <input id="<?= $h . "True" ?>" name="<?= $h ?>" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" disabled>
                  <label for="<?= $h . "True" ?>" class="block text-sm font-medium leading-6 text-gray-900">True</label>
                  <?php if ($row["answer"] == "True") : ?>
                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                      Answer
                    </span>
                  <?php endif ?>
                </div>
                <div class="flex items-center gap-x-3">
                  <input id="<?= $h . "False" ?>" name="<?= $h ?>" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600" disabled>
                  <label id="<?= $h . "False" ?>" class="block text-sm font-medium leading-6 text-gray-900">False</label>
                  <?php if ($row["answer"] == "False") : ?>
                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                      Answer
                    </span>
                  <?php endif ?>
                </div>
              <?php endif; ?>
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

          <!-- Edit question modal -->
          <dialog class="edit-modal w-2/5 rounded-xl backdrop:backdrop-brightness-[65%] h-[405px]">
            <form method="post" class="mt-5 mb-14">

              <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                  <h2 class="text-base font-semibold leading-7 text-gray-900">Edit Question</h2>
                  <p class="mt-1 text-sm leading-6 text-gray-600">...</p>

                  <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                      <label class="block text-sm font-medium leading-6 text-gray-900">Question</label>
                      <div class="mt-2">
                        <input name="question-<?= $row["id"]; ?>" type="text" required class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $row["question"] ?>">
                      </div>
                    </div>
                  </div>

                  <input type="hidden" name="question-<?= $row["id"] ?>-type" value="<?= $row["type"] ?>">

                  <?php if ($row["type"] == "TF") : ?>
                    <!-- True/False Options -->
                    <div class="flex items-center gap-x-3">
                      <input id="edit-true-option" name="question-<?= $row["id"] ?>-edit-true-false-option" type="radio" value="True" required <?= ($row["answer"] == "True") ? "checked" : "" ?>>
                      <label for="edit-true-option" class="block text-sm font-medium leading-6 text-gray-900">True</label>
                    </div>
                    <div class="flex items-center gap-x-3">
                      <input id="edit-false-option" name="question-<?= $row["id"] ?>-edit-true-false-option" type="radio" value="False" required <?= ($row["answer"] == "False") ? "checked" : "" ?>>
                      <label for="edit-false-option" class="block text-sm font-medium leading-6 text-gray-900">False</label>
                    </div>
                  <?php endif; ?>

                  <?php if ($row["type"] == "MC") : ?>
                    <?php

                    $choices = explode("|", $row["choices"]);
                    ?>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8">
                      <div>
                        <input type="hidden" name="question-<?= $row["id"] ?>-answer" value="<?= $row["answer"] ?>">
                        <div class="flex">
                          <span>1</span>
                          <input class="border block w-1/2" type="text" name="question-<?= $row["id"]; ?>-choice-0" placeholder="choice 1" required value="<?= $choices[0]; ?>">
                        </div>
                        <div class="flex">
                          <span>2</span>
                          <input class="border block w-1/2" type="text" name="question-<?= $row["id"]; ?>-choice-1" placeholder="choice 2" required value="<?= $choices[1]; ?>">
                        </div>
                        <div class="flex">
                          <span>3</span>
                          <input class="border block w-1/2" type="text" name="question-<?= $row["id"]; ?>-choice-2" required value="<?= $choices[2]; ?>">
                        </div>
                        <div class="flex">
                          <span>4</span>
                          <input class="border block w-1/2" type="text" name="question-<?= $row["id"]; ?>-choice-3" placeholder="choice 4" required value="<?= $choices[3]; ?>">
                        </div>
                        <div class="flex">
                          <span>correct answer</span>
                          <?php
                          $correct_choice_value = null;
                          foreach ($choices as $k => $choice) {
                            if ($choice == $row["answer"]) {
                              $correct_choice_value = $k + 1;
                            }
                          }
                          ?>
                          <input type="number" class="border" name="question-<?= $row["id"] ?>-new-correct-choice-number" min="1" max="4" placeholder="1-4" required value="<?= $correct_choice_value ?>">
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="" value="<?= $index ?>">Cancel</button>
                <button type="submit" name="edit-question-submit-button" value="<?= $row["id"] ?>" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
              </div>
            </form>
          </dialog>
        <?php endforeach; ?>
      </div>

      <!--  -->
      <div class="w-1/4 border-slate-400">
        <div class="space-y-3.5 flex flex-col justify-start">

          <button id="show-dialog" class="flex w-1/2 items-center justify-center rounded-md bg-indigo-600 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="h-5 w-5 mr-0.5">
              <path d="M10.75 6.75a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"></path>
            </svg>
            <div class="text-center">
              New question
            </div>
          </button>

          <button id="show-rename-dialog" class="flex w-1/2 items-center justify-center rounded-md bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <div class="">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>

            </div>
            <div class="text-center ml-2">
              Rename
            </div>
          </button>

          <button id="show-clone-dialog" class="flex w-1/2 items-center justify-center rounded-md bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <div class="">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
              </svg>
            </div>
            <div class="text-center ml-2">
              Clone
            </div>
          </button>
        </div>
      </div>
    </div>

    <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%] h-[500px]" id="dialog">
      <div class="tab w-full flex select-none">
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-500 border-b border-indigo-600 hover:border-indigo-500 text-indigo-700" value="multiple-choice-tab" onclick="openCity('multiple-choice-tab')">Multiple Choice</button>
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-500 border-b hover:border-indigo-500" value="true-false-tab" onclick="openCity('true-false-tab')">True or False</button>
        <button class="w-full tablinks pt-3.5 py-3 px-2 hover:text-indigo-500 border-b hover:border-indigo-500" value="open-ended-tab" onclick="openCity('open-ended-tab')">Open-Ended</button>
      </div>
      <hr class="mb-2">

      <section id="multiple-choice-tab" class="tabcontent">
        <form method="post" class="px-8 mx-auto pt-2 h-[415px] flex flex-col">
          <div class="border-b border-gray-900/10 pb-6 grow">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8">
              <div class="w-full">
                <label class="block text-sm font-medium leading-6 text-gray-900">Question</label>
                <div class="mt-2">
                  <input name="question" type="text" required placeholder="Multiple choice question" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div class="mt-4">
                  <div class="text-slate-500 text-sm mb-4">
                    Enter choices and select the correct answer
                  </div>

                  <div class="space-y-3">
                    <div class="flex items-center">
                      <span class="w-1/5 text-sm text-right pr-4">Choice 1</span>
                      <input type="text" name="choice[]" placeholder="" required class="block px-2 w-full rounded-md border-0 py-0.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="flex items-center">
                      <span class="w-1/5 text-sm text-right pr-4">Choice 2</span>
                      <input type="text" name="choice[]" placeholder="" required class="block px-2 w-full rounded-md border-0 py-0.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="flex items-center">
                      <span class="w-1/5 text-sm text-right pr-4">Choice 3</span>
                      <input type="text" name="choice[]" placeholder="" required class="block px-2 w-full rounded-md border-0 py-0.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="flex items-center">
                      <span class="w-1/5 text-sm text-right pr-4">Choice 4</span>
                      <input type="text" name="choice[]" placeholder="" required class="block px-2 w-full rounded-md border-0 py-0.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                  </div>
                  <div class="flex items-center mt-3.5">
                    <span class="w-1/5 text-sm text-right pr-4">Answer</span>
                    <input type="number" name="correct-choice-number" min="1" max="4" placeholder="Select the choice number (between 1 to 4)" required class="block px-2 w-full rounded-md border-0 py-0.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900">Cancel</button>
            <button type="submit" name="new-mc-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              Submit
            </button>
          </div>
        </form>
      </section>

      <section id="true-false-tab" class="tabcontent" style="display: none;">
        <form method="post" class="px-8 mx-auto pt-2 h-[415px] flex flex-col">
          <div class="border-b border-gray-900/10 pb-12 grow">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8">
              <div class="w-full">
                <label class="block text-sm font-medium leading-6 text-gray-900">Question</label>
                <div class="mt-2">
                  <input name="question" type="text" required placeholder="True or false question" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div class="mt-4">
                  <div class="text-slate-500 text-sm mb-4">
                    Select the correct answer
                  </div>

                  <div class="flex items-center gap-x-3 my-2">
                    <input id="true-option" name="true-false-option" type="radio" value="True" required class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="true-option" class="block text-sm font-medium leading-6 text-gray-900">True</label>
                  </div>
                  <div class="flex items-center gap-x-3">
                    <input id="false-option" name="true-false-option" type="radio" value="False" required class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="false-option" class="block text-sm font-medium leading-6 text-gray-900">False</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900">Cancel</button>
            <button type="submit" name="new-tf-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              Submit
            </button>
          </div>
        </form>
      </section>

      <section id="open-ended-tab" class="tabcontent" style="display: none;">
        <form method="post" class="px-8 mx-auto pt-2 h-[415px] flex flex-col">
          <div class="border-b border-gray-900/10 pb-12 grow">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8">
              <div class="w-full">
                <label class="block text-sm font-medium leading-6 text-gray-900">Question</label>
                <div class="mt-2">
                  <input name="question" type="text" required placeholder="Open-ended question" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div class="mt-4">
                  <div class="text-slate-500 text-sm mb-4">
                    Note: Open-ended answers will be manually graded by you.
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900">Cancel</button>
            <button type="submit" name="new-oe-question" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              Submit
            </button>
          </div>
        </form>
      </section>
    </dialog>
  </div>

  <!-- Clone modal -->
  <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%]" id="clone-dialog">
    <form method="post" class="px-8 mx-auto pt-6 pb-8">
      <div class="space-y-10">
        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Clone Quiz</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Cloning this quiz will create a copy of it without its submissions.</p>

          <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="sm:col-span-4">
              <label class="block text-sm font-medium leading-6 text-gray-900">Cloned Quiz Name</label>
              <div class="mt-2">
                <input name="new-clone-name" type="text" autocomplete="email" required class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-x-6">
        <button type="button" class="js-close text-sm font-semibold leading-6 text-gray-900" id="">Cancel</button>
        <button type="submit" name="clone-save-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
      </div>
    </form>
  </dialog>

  <!-- Rename modal -->
  <dialog class="w-2/5 rounded-xl backdrop:backdrop-brightness-[65%]" id="rename-dialog">
    <form method="post" class="px-8 mx-auto pt-6 pb-8">
      <div class="space-y-10">
        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Rename Quiz</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Update the name of this quiz.</p>

          <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
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
    let tabcontent = document.getElementsByClassName("tabcontent");
    let tablinks = document.getElementsByClassName("tablinks");

    function openCity(cityName) {
      for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tablinks[i].classList.remove("text-indigo-700")
        tablinks[i].classList.remove("border-indigo-600")
      }
      document.getElementById(cityName).style.display = "";
      for (const link of tablinks) {
        if (link.getAttribute("value") == cityName) {
          link.classList.add("text-indigo-700")
          link.classList.add("border-indigo-600")
        }
      }
      // document.getElementById(cityName).classList.add = "active";
    }

    // openCity('multiple-choice-tab');
  </script>

  <script>
    const editQuestionButtons = document.getElementsByClassName("edit-question-button");
    const editQuestionDialogs = document.getElementsByClassName("edit-modal");

    for (const b of editQuestionButtons) {
      b.addEventListener("click", (e) => {
        e.preventDefault();
        console.log(b.value);
        editQuestionDialogs[b.value].showModal();
      })
    }

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

    const cloneDialogButton = document.getElementById("show-clone-dialog");
    const cloneDialog = document.getElementById("clone-dialog")

    cloneDialogButton.addEventListener("click", () => {
      cloneDialog.showModal();
    });

    for (const b of jsCloseBtns) {
      b.addEventListener("click", (e) => {
        e.preventDefault();
        dialog.close();
        renameDialog.close();
        cloneDialog.close();
        console.log(b);
        console.log(b.value);
        if (b.value != null) {
          editQuestionDialogs[parseInt(b.value)].close();
        }
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
