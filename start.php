<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {
  $quiz_code = $_POST["quiz-code"];

  $sql = "SELECT id FROM quizzes WHERE code = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$quiz_code]);

  if ($stmt->rowCount() == 0) {
    $_SESSION["error_message"] = "Invalid quiz code. Please try again.";
    header("Location: ./start.php");
    exit;
  }

  $_SESSION["quiz_id"] = $stmt->fetchColumn();
  header("Location: ./session/index.php");
  exit;
}

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

<body class="font-['Inter'] bg-gray-800">
  <div class="w-[447.5px] mx-auto bg-white pt-5 pb-8 px-8 border border-slate-300 rounded-lg text-center mt-7">
    <h1 class="font-['Literata'] text-2xl mt-3 mb-2 text-center">Quizify</h1>
    <p class="text-center text font- text-slate-700 mb-4">Enter a code to start taking a quiz.</p>

    <?php
    if (isset($_SESSION["error_message"])) {
      display_error_message($_SESSION["error_message"]);
      unset($_SESSION["error_message"]);
    }
    ?>

    <form method="post" class="my-6">
      <div class="flex items-center justify-between">
        <label for="" class="block text-sm font-medium leading-6 text-gray-900"></label>
      </div>
      <div class="mt-2">
        <input type="text" name="quiz-code" placeholder="Quiz code" required class="px-2.5 mb-4 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
      </div>
      <button name="submit-button" type="submit" class="rounded-md w-full bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Enter</button>
    </form>

    <a class="text-indigo-600 hover:text-indigo-500" href="./login.php">Sign in as tutor ›</a>

    <div class="mt-6">
      <a href="./index.php" id="" class="flex w-1/4 items-center mx-auto justify-center rounded-md bg-white px- py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
        <div class="text-center ml-2">
          Return
        </div>
      </a>
    </div>
  </div>
</body>

</html>
