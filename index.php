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

<body>
  <div class="w-[447.5px] mx-auto bg-white pt-5 pb-10 px-8 border border-slate-300 rounded-lg text-center mt-11">
    <h1 class="font-['Literata'] text-2xl my-4 text-center mb-5">Quizify</h1>

    <?php if (isset($error_message)) : ?>
      <div class="text-left bg-red-50 rounded-lg py-2 px-3 border border-red-600 mb-4 pb-3.5">
        <h1 class="text-red-800 font-medium mb-0.5">
          Error
        </h1>
        <p class="text-red-700 text-sm">
          <?= $error_message ?>
        </p>
      </div>
    <?php endif; ?>

    <form method="post" class="mb-4">
      <input type="text" name="quiz-code" placeholder="Quiz code" required class="px-2.5 mb-4 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
      <button name="submit-button" type="submit" class="rounded-md w-full bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Enter</button>
    </form>

    <a class="text-indigo-600 hover:text-indigo-500" href="./login.php">Sign in as tutor ›</a>
  </div>
</body>

</html>
