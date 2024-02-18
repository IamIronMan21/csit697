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
    <p class="text-center text-xl font-semibold">Sign in</p>

    <form method="post" class="mb-4">

      <?php if (isset($error_message)) : ?>
        <p class="text-red-500"><?= $error_message ?></p>
      <?php endif; ?>

      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        <div class="mt-2">
          <input id="email" name="email" type="text" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <!-- <input class="border border-slate-400" type="text" name="email" placeholder="Email" required> -->

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <!-- <input class="border border-slate-400" type="password" name="password" placeholder="Password" required> -->

      <div class="my-10">
        Need an account? <a href="./register.php" class="text-indigo-600 underline">Register here</a>
      </div>

      <div class="my-10">

        <a href="./" class="text-blue-500 underline">Back</a>
        <input class="border bg-slate-200" type="submit" name="submit-button" value="Submit">
      </div>
    </form>

  </div>
</body>

</html>
