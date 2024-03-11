<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {
  $quiz_code = $_POST["quiz-code"];

  $sql = "SELECT id FROM quizzes WHERE code = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$quiz_code]);

  if ($stmt->rowCount() > 0) {
    $_SESSION["quiz_id"] = $stmt->fetchColumn();
    header("Location: ./session/index.php");
    exit;
  } else {
    $error_message = "Invalid quiz code. Please try again.";
  }
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

<body class="bg-gray-800">
  <div class="w-[447.5px] mx-auto bg-white pt-5 pb-8 px-8 border border-slate-300 rounded-lg text-center mt-7">
    <h1 class="font-['Literata'] text-2xl mt-3 mb-2 text-center">Quizify</h1>
    <p class="text-center text font- text-slate-700 mb-4">Enter a code to start taking a quiz.</p>

    <?php if (isset($error_message)) : ?>
      <div class="flex items-center bg-red-50 rounded-lg py-3 px-3 border border-red-600 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f87171" aria-hidden="true" class="w-6 h-6">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-red-700 text-[14px] font-medium px-2">
          <?= $error_message ?>
        </p>
      </div>
    <?php endif; ?>

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
  </div>
</body>

</html>
