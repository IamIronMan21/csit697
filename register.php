<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_ARGON2I);

  $sql = "SELECT 1 FROM tutors WHERE email = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$email]);

  if ($stmt->rowCount() == 1) {
    $_SESSION["error_message"] = "Email is already taken. Please choose another.";
    header("Location: ./register.php");
    exit;
  }

  $sql = "INSERT INTO tutors (name, email, password) VALUES (?, ?, ?)";
  prepare_and_execute($sql, [$name, $email, $password]);

  header("Location: ./login.php");
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
  <div class="w-[447.5px] mx-auto bg-white pt-5 pb-4 px-8 rounded-lg text-center mt-6">
    <h1 class="font-['Literata'] text-2xl mt-2.5 mb-2 text-center">Quizify</h1>
    <p class="text-center text-slate-500 mb-3">Sign up for a tutor account</p>

    <?php
    if (isset($_SESSION["error_message"])) {
      display_error_message($_SESSION["error_message"]);
      unset($_SESSION["error_message"]);
    }
    ?>

    <form method="post" class="mb-3">
      <div class="mt-3">
        <div class="flex items-center justify-between">
          <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
        </div>
        <div class="mt-2">
          <input id="name" name="name" type="text" required class="px-2.5 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div class="mt-3">
        <div class="flex items-center justify-between">
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        </div>
        <div class="mt-2">
          <input id="email" name="email" type="text" required class="px-2.5 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div class="mt-3">
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" required class="px-2.5 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>



      <div class="my-8 flex items-center">
        <input type="submit" name="submit-button" value="Submit" class="rounded-md w-full bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
      </div>
    </form>

    <hr class="w-4/5 mx-auto border-slate-300">

    <div class="text-center">
      <div class="my-5 text-slate-500">
        Already have an account? <a href="./login.php" class="text-indigo-600 hover:text-indigo-500">Sign in</a>
      </div>
    </div>
  </div>
</body>

</html>
