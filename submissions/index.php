<?php
require "../utils.php";

session_start();

// Check if submission_id is set and not empty
if (isset($_POST['submission_id']) && !empty($_POST['submission_id'])) {
    // Call the delete_submission function to delete the submission and its related responses
    delete_submission($_POST['submission_id']);
}

$sql = "
SELECT
  s.id,
  c.name AS course_name,
  q.name AS quiz_name,
  s.submitter,
  s.created_at
FROM
  courses c,
  quizzes q,
  submissions s
WHERE
  c.tutor_id = ?
  AND c.id = q.course_id
  AND q.id = s.quiz_id
ORDER BY
  s.created_at;
";
$stmt  = prepare_and_execute($sql, [$_SESSION["tutor_id"]]);
$submissions = $stmt->fetchAll();

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

  <div class="mx-auto max-w-7xl bg-white min-h-screen pt-4 px-12">
    <div class="flex mb-4 items-center">
      <h1 class="grow text-xl font-medium">Submissions</h1>
      <div class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm invisible">+</div>
    </div>

    <div class="flex items-center mb-5 w-full shadow rounded-md">
      <div class="pl-2.5 pr-1 block w-fit rounded-l-md border-l border-y py-2 text-gray-900 bg-white border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </div>
      <input type="text" id="search-input" placeholder="Search by submitter" class="px-2 block w-full rounded-r-md border-y border-r py-1.5 text-gray-900 border-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6 outline-none">
    </div>

    <div class="w-full h-fit border border-slate-500 shadow-sm rounded-lg mb-10 overflow-hidden">
      <table class="w-full">
        <thead class="border-b border-slate-500 h-[35px] text-[15px]">
          <th class="font-semibold w-[4%] pl-1">#</th>
          <th class="font-semibold text-left w-[30%] pl-8">Submitter</th>
          <th class="font-semibold text-left w-[21%]">Quiz</th>
          <th class="font-semibold text-left w-[18%]">Course</th>
          <th class="font-semibold text-left w-[15%]">Date Submitted</th>
          <th class="font-semibold w-[6%]"></th>
          <th class="font-semibold w-[6%]"></th>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($submissions as $index => $submission) : ?>
            <tr class="submission h-[45px] <?= ($index % 2 == 1) ? "bg-slate-100" : ""; ?>" value="<?= $submission["submitter"] ?>">
              <td class="index text-center font-light text-slate-500 pl-1"><?= $index + 1 ?></td>
              <td class="pl-8"><?= $submission["submitter"] ?></td>
              <td><?= $submission["course_name"] ?></td>
              <td><?= $submission["quiz_name"] ?></td>
              <td class="text-slate-500">
                <?= (new DateTime($submission["created_at"]))->format('m/d/Y') ?>
              </td>
              <td>
                <a href="<?= "./edit.php?submission_id=" . $submission["id"] ?>" class="text-indigo-600 underline hover:text-indigo-500">Edit</a>
                </td>
              <td>
                <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this submission?')">
                  <input type="hidden" name="submission_id" value="<?= $submission["id"] ?>">
                  <button type="submit" class="text-indigo-600 underline hover:text-indigo-500">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    const searchInput = document.getElementById("search-input");

    const submissions = document.getElementsByClassName("submission");

    searchInput.addEventListener("input", (e) => {
      e.preventDefault();

      for (const c of submissions) {
        const name = c.getAttribute("value");
        if (!name.includes(searchInput.value)) {
          c.style.display = "none";
        } else {
          c.style.display = "";
        }
      }

      for (const c of submissions) {
        const name = c.getAttribute("value");
        if (!(name.toLowerCase()).includes((searchInput.value.toLowerCase()))) {
          c.style.display = "none";
        } else {
          c.style.display = "";
        }
      }

      let f = 1;
      let t = false;
      let i = 1;

      for (const c of submissions) {
        c.style.borderTop = "1px solid rgb(203 213 225)";
        const name = c.getAttribute("value");
        if (c.style.display == "") {
          if (f) {
            c.style.backgroundColor = "#fff"
          } else {
            c.style.backgroundColor = "#f1f5f9";
          }
          f ^= 1;
          c.querySelector(".index").innerHTML = i;
          i++;
          if (!t) {
            c.style.borderTop = "1px solid rgb(100 116 139)";
            t = true;
          }
        }
      }
    });
  </script>
</body>

</html>

