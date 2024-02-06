<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {

  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_ARGON2I);

  $sql = "SELECT 1 FROM tutors WHERE email = ?";
  $stmt = prepare_and_execute($sql, [$email]);

  if ($stmt->rowCount() == 0) {
    $sql = "INSERT INTO tutors (name, email, password) VALUES (?, ?, ?)";
    $stmt = prepare_and_execute($sql, [$name, $email, $password]);
    header("Location: ./login.php");
    exit;
  } else {
    $error_message = "Sorry! That email is already taken. Please choose another one.";
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
    <h1 class="my-4">Register</h1>

    <?php if (isset($error_message)) : ?>
      <p class="text-red-500"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="post" class="mb-4">
      <label for="name">Name</label>
      <input class="border border-slate-400" type="text" name="name" required><br>

      <label for="email">Email</label>
      <input class="border border-slate-400" type="text" name="email" required><br>

      <label for="password">Password</label>
      <input class="border border-slate-400" type="password" name="password" required><br>

      <input class="border bg-slate-200" name="submit-button" type="submit" value="Submit">
    </form>

    <a href="./" class="text-blue-500 underline">Back</a>
  </div>
</body>

</html>
