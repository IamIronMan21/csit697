<?php

require "../utils.php";

session_start();

$dbh = connect_to_database();

$sql = <<<EOD
SELECT C.name as course_name, Q.name as quiz_name, S.submitter, S.created_at
FROM tutors T, courses C, quizzes Q, submissions S
WHERE (
  C.tutor_id = {$_SESSION["tutor_id"]} AND
  C.id = Q.course_id AND
  Q.id = S.quiz_id
)
EOD;
$stmt  = prepare_and_execute($sql, []);
$submissions = $stmt->fetchAll();

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
              <a href="../courses/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Courses</a>
              <a href="../quizzes/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
              <a href="." class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Submissions</a>
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
      <h1 class="grow text-xl font-medium">Submissions</h1>
      <div class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm invisible">+</div>
    </div>

    <!-- <?= var_dump($submissions) ?> -->

    <div class="w-full h-fit border border-slate-500 shadow-sm rounded-lg mb-10 overflow-hidden">
      <table class="w-full">
        <thead class="border-b border-slate-500 h-[35px] text-[15px]">
          <th class="font-semibold w-[3vw] pl-1">#</th>
          <th class="font-semibold text-left w-[27vw] pl-12">Submitter</th>
          <th class="font-semibold text-left">Quiz</th>
          <th class="font-semibold text-left">Course</th>
          <th class="font-semibold text-left w-[13vw]">Date Submitted</th>
          <th class="font-semibold"></th>
          <th class="font-semibold"></th>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($submissions as $index => $submission) : ?>
            <tr class="h-[45px] <?= ($index % 2 == 1) ? "bg-slate-100" : ""; ?>">
              <td class="text-center font-medium pl-1"><?= $index + 1 ?> </td>
              <td class="pl-12"><?= $submission["submitter"] ?></td>
              <td class=""><?= $submission["course_name"] ?></td>
              <td class=""><?= $submission["quiz_name"] ?></td>
              <td class="text-gray-500">
                <?= (new DateTime($submission["created_at"]))->format('m/d/Y') ?>
              </td>
              <td>
                <a href="#" class="text-indigo-600 underline hover:text-indigo-500">Edit</a>
              </td>
              <td>
                <a href="#" class="text-indigo-600 underline hover:text-indigo-500">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>

</html>
