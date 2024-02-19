<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {
  $email = $_POST["email"];
  $password = $_POST["password"];

  $sql = "SELECT id, password FROM tutors WHERE email = ?";
  $stmt = prepare_and_execute($sql, [$email]);

  $tutor = $stmt->fetch();

  if ($tutor && password_verify($password, $tutor["password"])) {
    $_SESSION["tutor_id"] = $tutor["id"];
    header("Location: ./courses/index.php");
    exit;
  } else {
    $error_message = "Invalid email or password. Please try again.";
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

<body>
  <div class="w-[447.5px] mx-auto bg-white pt-5 pb-10 px-8 border border-slate-300 rounded-lg text-center mt-11">
    <h1 class="font-['Literata'] text-2xl my-4 text-center">Quizify</h1>
    <p class="text-center text-xl font-semibold">Sign in</p>

    <form method="post" class="mb-4">

      <?php if (isset($error_message)) : ?>
        <p class="text-red-500"><?= $error_message ?></p>
      <?php endif; ?>

      <div>
        <div class="flex items-center justify-between">
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        </div>
        <div class="mt-2">
          <input id="email" name="email" type="text" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div class="my-8 flex">
        <input type="submit" name="submit-button" value="Submit" class="rounded-md w-full bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
      </div>

      <hr class="w-4/5 mx-auto">

      <div class="text-center">
        <div class="my-5">
          Don't have an account? <a href="./register.php" class="text-indigo-600 underline">Sign up</a>
        </div>
        <a href="./index.php" class="text-blue-500 underline">Back</a>
      </div>
    </form>

  </div>
</body>

</html>
