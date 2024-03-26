<?php

require_once "../utils.php"; // Use require_once to ensure utils.php is included only once

session_start();

$errors = [];

$sql = "SELECT * FROM tutors WHERE id = ?";
$stmt = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$tutor = $stmt->fetch();

if (isset($_POST["profile-save-button"])) {
  $name = $_POST["name"];
  $email = $_POST["email"];

  // Update name and email in the database if there are no errors
  $sql = "UPDATE tutors SET name = ?, email = ? WHERE id = ?";
  prepare_and_execute($sql, [$name, $email, $_SESSION["tutor_id"]]);
  $_SESSION["success_message"] = "Profile updated successfully";
  header("Location: .");
  exit;
}

if (isset($_POST["password-save-button"])) {
  $newPassword = $_POST["new-password"];
  $confirmPassword = $_POST["confirm-new-password"];

  // Validate if new password and confirm password match
  if ($newPassword !== $confirmPassword) {
    $_SESSION["error_message"] = "New password and confirm passwords do not match. Please try again.";
    header("Location: .");
    exit;
  } else {
    // Update password in the database
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE tutors SET password = ? WHERE id = ?";
    prepare_and_execute($sql, [$hashedPassword, $_SESSION["tutor_id"]]);
    $_SESSION["success_message"] = "Password updated successfully";
    // Redirect to prevent form resubmission
    header("Location: .");
    exit;
  }
}

if (isset($_POST["delete-account-button"])) {
  delete_tutor($_SESSION["tutor_id"]);
  header("Location: ../index.php");
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

<body class="min-h-screen font-['Inter']">
  <nav class="bg-gray-800 px-4">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
        </div>
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="flex flex-shrink-0 items-center">
            <h1 class="text-white font-['Literata'] text-xl mr-1">Quizify</h1>
          </div>
          <div class="hidden sm:ml-6 sm:block">
            <div class="flex space-x-4">
              <a href="../courses/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Courses</a>
              <a href="../quizzes/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
              <a href="../submissions/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Submissions</a>
            </div>
          </div>
        </div>
        <div class="inset-y-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
          <div class="relative ml-3">
            <div class="flex space-x-4">
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium">Settings</a>
              <a href="../index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Sign out</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="mx-auto max-w-7xl bg-white border-slate-500 min-h-screen px-12 pt-4">

    <div class="w-3/5 mx-auto">
      <?php
      if (isset($_SESSION["error_message"])) {
        display_error_message($_SESSION["error_message"]);
        unset($_SESSION["error_message"]);
      }
      ?>
      <?php
      if (isset($_SESSION["success_message"])) {
        display_success_message($_SESSION["success_message"]);
        unset($_SESSION["success_message"]);
      }
      ?>
    </div>

    <div class="w-3/5 mt-5 mx-auto mb-14">
      <div class="space-y-8">
        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Profile</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Edit your personal information to be up-to-date.</p>

          <form method="post">
            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <!-- Name -->
              <div class="col-span-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                  <input type="text" name="name" value="<?= $tutor["name"] ?>" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>
              <!-- Email  -->
              <div class="col-span-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                <div class="mt-2">
                  <input type="text" name="email" value="<?= $tutor["email"] ?>" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
              <button name="profile-save-button" type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
            </div>
          </form>
        </div>

        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Password</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Set a new password to keep your account safe.</p>

          <form method="post">
            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <!-- New Password -->
              <div class="col-span-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">New Password</label>
                <div class="mt-2">
                  <input type="password" required name="new-password" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="••••••••">
                </div>
              </div>
              <!-- Confirm New Password -->
              <div class="col-span-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">Confirm New Password</label>
                <div class="mt-2">
                  <input type="password" required name="confirm-new-password" class="block px-2.5 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="••••••••">
                </div>
              </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
              <button name="password-save-button" type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
            </div>
          </form>
        </div>

        <div class="border-b border-gray-900/10 pb-12">
          <h2 class="text-base font-semibold leading-7 text-gray-900">Account Removal</h2>
          <p class="mt-1 text-sm leading-6 text-gray-600">Deleting your account is permanent. All of your data will be lost and cannot be restored.</p>

          <form method="post" onsubmit="return confirm('Are you sure you want to delete your account?');">
            <div class="mt-10">
              <button type="submit" name="delete-account-button" class="w-[150px] rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Delete Account</button>
            </div>
          </form>
        </div>
      </div>
</body>

</html>
