<?php

require "../utils.php";

session_start();

$course_id = $_GET["course_id"];

if (isset($_POST["save-button"])) {
  $new_course_name = $_POST["new-name"];
  $new_semester = $_POST["new-semester"];
  $new_institution = $_POST["new-institution"];

  $sql = "SELECT 1 FROM courses WHERE name = ? AND semester = ? AND institution = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$new_course_name, $new_semester, $new_institution]);

  if ($stmt->rowCount() == 1) {
    $_SESSION["error_message"] = "A similar course already exists. Please verify your input.";
    header("Location: ./edit.php?course_id=$course_id");
    exit;
  }

  $sql = "UPDATE courses SET name = ?, semester = ?, institution = ? WHERE id = ?";
  prepare_and_execute($sql, [$new_course_name, $new_semester, $new_institution, $_GET["course_id"]]);

  $_SESSION["success_message"] = "Success! Your changes have been saved.";
  header("Location: ./edit.php?course_id=$course_id");
  exit;
}

$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = prepare_and_execute($sql, [$course_id]);
$course = $stmt->fetch();

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
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Courses</a>
              <a href="../quizzes/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
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

  <div class="mx-auto max-w-7xl bg-white min-h-screen pt-4 px-12">

    <div class="w-3/5 mx-auto">

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

      <form method="post" class="mt-5 mb-14">

        <div class="space-y-12">
          <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Edit Course</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Update the information of an existing course.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                  <input name="new-name" type="text" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $course["name"] ?>">
                </div>
              </div>
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Semester</label>
                <div class="mt-2">
                  <input name="new-semester" type="text" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $course["semester"] ?>">
                </div>
              </div>
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Institution</label>
                <div class="mt-2">
                  <input name="new-institution" type="text" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $course["institution"] ?>">
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
          <a href="./index.php" class="text-sm font-semibold leading-6 text-gray-900">Back</a>
          <button type="submit" name="save-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
      </form>
    </div>

</body>

</html>
