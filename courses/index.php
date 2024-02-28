<?php

require "../utils.php";

session_start();

if (isset($_POST["new-course-button"])) {
  $course_name = $_POST["course-name"];

  $sql = "SELECT 1 FROM courses WHERE name = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$course_name]);
  if ($stmt->rowCount() == 1) {
    $_SESSION["error_message"] = "Course entered already exists.";
    header("Location: .");
    exit;
  }

  $sql = "INSERT INTO courses (name, tutor_id) VALUES (?, ?)";
  prepare_and_execute($sql, [$course_name, $_SESSION["tutor_id"]]);

  $_SESSION["success_message"] = "The new course has been added.";
  header("Location: .");
  exit;
}

$sql = "SELECT * FROM courses WHERE tutor_id = ?";
$stmt = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$courses = $stmt->fetchAll();

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

  <div class="mx-auto bg-white min-h-screen px-12 pt-4">

    <?php if (isset($_SESSION["error_message"])) : ?>
      <div class="flex items-center bg-red-50 rounded-lg py-3 px-3 border border-red-600 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f87171" aria-hidden="true" class="w-6 h-6">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-red-700 text-[14px] font-medium px-2">
          <?= $_SESSION["error_message"] ?>
        </p>
      </div>
      <?php unset($_SESSION["error_message"]) ?>
    <?php endif; ?>

    <?php if (isset($_SESSION["success_message"])) : ?>
      <div class="flex items-center bg-green-50 rounded-lg py-3 px-3 border border-green-600 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#4ade80" aria-hidden="true" class="w-6 h-6">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-green-700 text-[14px] font-medium px-2">
          <?= $_SESSION['success_message'] ?>
        </p>
      </div>
      <?php unset($_SESSION["success_message"]) ?>
    <?php endif; ?>

    <div class="flex mb-4 items-center">
      <h1 class="grow text-xl font-medium">Courses</h1>
      <!-- <button id="show-dialog" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">+ New course</button> -->
      <button id="show-dialog" class="flex items-center rounded-md bg-indigo-600 px-3 pl-2 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="h-5 w-5 mr-0.5">
          <path d="M10.75 6.75a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"></path>
        </svg>
        <div>
          New course
        </div>
      </button>
    </div>

    <div class="w-full h-fit border border-slate-500 shadow-sm rounded-lg mb-10 overflow-hidden">
      <table class="w-full">
        <thead class="border-b border-slate-500 h-[35px] text-[15px]">
          <th class="font-semibold w-[4%] pl-1">#</th>
          <th class="font-semibold text-left w-[42%] pl-8">Name</th>
          <th class="font-semibold text-left w-[42%]">Date Created</th>
          <th class="font-semibold w-[6%]"></th>
          <th class="font-semibold w-[6%]"></th>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($courses as $index => $course) : ?>
            <tr class="h-[45px] <?= ($index % 2 == 1) ? "bg-slate-100" : ""; ?>">
              <td class="text-center font-light text-slate-600 pl-1"><?= $index + 1 ?> </td>
              <td class="pl-8"><?= $course["name"] ?></td>
              <td class="text-slate-500">
                <?= (new DateTime($course["created_at"]))->format('m/d/Y') ?>
              </td>
              <td>
                <a href="./edit.php?course_id=<?= $course["id"] ?>" class="text-indigo-600 underline hover:text-indigo-500">Edit</a>
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
            <h2 class="text-base font-semibold leading-7 text-gray-900">New Course</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Create a new course here.</p>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <div class="sm:col-span-4">
                <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                  <input name="course-name" type="text" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
          <button type="button" class="text-sm font-semibold leading-6 text-gray-900" id="js-close">Cancel</button>
          <button type="submit" name="new-course-button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add</button>
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
</body>

</html>
