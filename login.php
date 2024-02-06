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

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen">

  <header class="px-8 py-5 bg-gray-800">
    <div class="flex items-center">
      <div class="grow">
        &nbsp;
      </div>
    </div>
  </header>

  <div class="w-1/2 mx-auto bg-white min-h-screen px-8">
    <h1 class="my-4">Login</h1>

    <form method="post" class="mb-4">

      <?php if (isset($error_message)) : ?>
        <p class="text-red-500"><?= $error_message ?></p>
      <?php endif; ?>

      <input class="border border-slate-400" type="text" name="email" placeholder="Email" required>

      <input class="border border-slate-400" type="password" name="password" placeholder="Password" required>

      <input class="border bg-slate-200" type="submit" name="submit-button" value="Submit">
    </form>

    <a href="./" class="text-blue-500 underline">Back</a>
  </div>
</body>

</html>
