<?php

require "./utils.php";

session_start();

if (isset($_POST["submit-button"])) {

  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_ARGON2I);

  $sql = "SELECT 1 FROM tutors WHERE email = ? LIMIT 1";
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
    <p class="text-center text-xl font-semibold">Register</p>

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



      <div class="my-10 flex">

        <a href="./" class="text-blue-500 underline">Back</a>
        <input class="border bg-slate-200" type="submit" name="submit-button" value="Submit">
      </div>
      <div class="my-10">
        Already have an account? <a href="./login.php" class="text-indigo-600 underline">Sign in</a>
      </div>
    </form>
  </div>
</body>

</html>
