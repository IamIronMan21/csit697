<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

if (isset($_POST["new-course-button"])) {
  $sql = "INSERT INTO courses (name, tutor_id) VALUES (?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$_POST["course-name"], $_SESSION["tutor_id"]]);

  header("Location: .");
  exit;
}

$sql = "SELECT * FROM courses WHERE tutor_id = ?";
$stmt = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$courses = $stmt->fetchAll();

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
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Courses</a>
              <a href="../quizzes/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
              <a href="../submissions/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Submissions</a>
            </div>
          </div>
        </div>
        <div class="inset-y-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
          <div class="relative ml-3">
            <div class="flex space-x-4">
              <a href="../settings/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Settings</a>
              <a href="../index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Sign out</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="mx-auto bg-white min-h-screen px-12 pt-4">

    <div class="flex mb-4 items-center">
      <h1 class="grow text-xl font-medium">Courses</h1>
      <button id="show-dialog" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">+ New course</button>
    </div>

    <div class="w-full h-fit border border-slate-500 shadow-sm rounded-lg mb-10">
      <table class="w-full">
        <thead class="border-b border-slate-500 h-[35px] text-[15px]">
          <th class="font-semibold pl-1">#</th>
          <th class="font-semibold text-left pl-12">Name</th>
          <th class="font-semibold text-left">Date Created</th>
          <th class="font-semibold"></th>
          <th class="font-semibold"></th>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($courses as $index => $course) : ?>
            <tr class="h-[40px] <?= ($index % 2 == 1) ? "bg-slate-100" : ""; ?>">
              <td class="text-center font-medium pl-1"><?= $index + 1 ?> </td>
              <td class="pl-12"><?= $course["name"] ?></td>
              <td class="text-gray-500"><?= $course["created_at"] ?></td>
              <td>
                <a href="./edit.php?course_id=<?= $course["id"] ?>" class="text-indigo-600 hover:underline">Edit</a>
              </td>
              <td>
                <a href="#" class="text-indigo-600 hover:underline">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <dialog class="w-1/2 h-1/2 rounded-md backdrop:backdrop-brightness-75" id="dialog">
      <h1>Add a new course</h1>
      <form class="w-1/2 h-1/2 mx-auto mt-10" method="post">
        <input class="border" type="text" name="course-name" placeholder="New course name">
        <input type="submit" name="new-course-button" value="Add">
        <div>
          <button type="button" id="js-close">Close</button>
        </div>
      </form>
    </dialog>

    <script>
      const showBtn = document.getElementById("show-dialog");
      const dialog = document.getElementById("dialog");
      const jsCloseBtn = dialog.querySelector("#js-close");

      showBtn.addEventListener("click", () => {
        dialog.showModal();
      });

      jsCloseBtn.addEventListener("click", (e) => {
        e.preventDefault();
        dialog.close();
      });
    </script>
</body>

</html>
