<?php

require "./utils.php";

session_start();

$dbh = connect_to_database();

if (isset($_POST["submit-btn"])) {
  $code = $_POST["quiz-code"];

  $sql = "SELECT * FROM quizzes WHERE code = ? LIMIT 1";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$code]);

  if ($stmt->rowCount() > 0) {
    echo "found quiz using code";
  } else {
    $error_message = "cannot find quiz";
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
    <h1 class="my-4">Quizify</h1>

    <?php if (isset($error_message)) : ?>
      <p class="text-red-500"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="post" class="mb-4">
      <input class="border border-slate-400" type="text" name="quiz-code" placeholder="Quiz code" required>
      <button class="border bg-slate-200" name="submit-btn" type="submit">Submit</button>
    </form>

    <a class="text-blue-500 underline" href="./login.php">Login</a>
    <div>
      <a class="text-blue-500 underline" href="./register.php">Register</a>
    </div>
  </div>
</body>

</html>
