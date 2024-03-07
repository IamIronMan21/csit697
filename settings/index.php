<?php
require "../utils.php";
session_start();

// Initialize error messages and field values
$errors = [];
$nameValue = "";
$emailValue = "";

// Check if the form for updating profile information is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["profile-save-button"])) {
    // Retrieve the updated name and email from the form
    $updatedName = $_POST["name"];
    $updatedEmail = $_POST["email"];

    // Check for empty fields
    if (empty($updatedName)) {
        $errors["name"] = "Name cannot be empty.";
    } else {
        $nameValue = $updatedName;
    }

    if (empty($updatedEmail)) {
        $errors["email"] = "Email cannot be empty.";
    } else {
        $emailValue = $updatedEmail;
    }

    // If there are no errors, update the tutor's profile in the database
    if (empty($errors)) {
        $sql = "UPDATE tutors SET name = ?, email = ? WHERE id = ?";
        $params = [$updatedName, $updatedEmail, $_SESSION["tutor_id"]];

        // Add error handling
        try {
            prepare_and_execute($sql, $params);
        } catch (Exception $e) {
            $errors["profile"] = "Error updating profile: " . $e->getMessage();
        }
    }
}

// Check if the form for updating the password is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password-save-button"])) {
    // Retrieve the updated password from the form
    $newPassword = $_POST["new-password"];

    // Check for empty password (add other validation as needed)
    if (empty($newPassword)) {
        $errors["password"] = "New password cannot be empty.";
    } else {
        // Hash the new password
        $updatedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the tutor's password in the database
        $sql = "UPDATE tutors SET password = ? WHERE id = ?";
        $params = [$updatedPassword, $_SESSION["tutor_id"]];

        // Add error handling
        try {
            prepare_and_execute($sql, $params);
        } catch (Exception $e) {
            $errors["password"] = "Error updating password: " . $e->getMessage();
        }
    }
}

// Fetch tutor data
$sql = "SELECT * FROM tutors WHERE id = ?";
$stmt = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$tutor = $stmt->fetch();

// Check if $tutor is set before accessing its properties
$nameValue = isset($tutor["name"]) ? $tutor["name"] : $nameValue;
$emailValue = isset($tutor["email"]) ? $tutor["email"] : $emailValue;
?>

<!DOCTYPE html>
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
                                    <input type="text" name="name" autocomplete="given-name" class="block px-2.5 w-full rounded-md border-<?= isset($errors['name']) ? '2 text-red-500' : '0' ?> py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $nameValue ?>">
                                </div>
                                <?= isset($errors['name']) ? '<p class="text-red-500 text-sm mt-1">' . $errors['name'] . '</p>' : '' ?>
                            </div>
                            <!-- Email  -->
                            <div class="col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                                <div class="mt-2">
                                    <input type="text" name="email" class="block px-2.5 w-full rounded-md border-<?= isset($errors['email']) ? '2 text-red-500' : '0' ?> py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="<?= $emailValue ?>">
                                </div>
                                <?= isset($errors['email']) ? '<p class="text-red-500 text-sm mt-1">' . $errors['email'] . '</p>' : '' ?>
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
                                    <input type="password" name="new-password" class="block w-full px-2.5 rounded-md border-<?= isset($errors['password']) ? '2 text-red-500' : '0' ?> py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="••••••••">
                                </div>
                                <?= isset($errors['password']) ? '<p class="text-red-500 text-sm mt-1">' . $errors['password'] . '</p>' : '' ?>
                            </div>
                            <!-- Confirm New Password -->
                            <div class="col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Confirm New Password</label>
                                <div class="mt-2">
                                    <input type="password" name="confirm-new-password" class="block w-full px-2.5 rounded-md border-<?= isset($errors['password']) ? '2 text-red-500' : '0' ?> py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="••••••••">
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

                    <div class="mt-10">
                        <button type="button" class="w-[150px] rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Delete Account</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</body>

</html>

