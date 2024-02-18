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
    $error_message = "Unable to find quiz with given code. Please try again.";
  }
}

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

<body class="min-h-screen bg-gray-00">

  <header class="px-8 py-2.5 bg-gray-00">
    <div class="flex items-center">
      <div class="grow">
        &nbsp;
      </div>
    </div>
  </header>

  <div class="w-[447.5px] mx-auto bg-white min pt-5 pb-10 px-8 border border-slate-300 rounded-lg">
    <h1 class="font-['Literata'] text-2xl my-4 text-center">Quizify</h1>

    <?php if (isset($error_message)) : ?>
      <p class="text-red-500"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="post" class="mb-4">
      <input class="border border-slate-400" type="text" name="quiz-code" placeholder="Quiz code" required>
      <button class="border bg-slate-200" name="submit-button" type="submit">Submit</button>
    </form>

    <a class="text-blue-500 underline" href="./login.php">Login</a>
    <div>
      <a class="text-blue-500 underline" href="./register.php">Register</a>
    </div>
  </div>
</body>

</html>
