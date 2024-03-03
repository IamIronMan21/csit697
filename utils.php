<?php

function connect_to_database()
{
  $dsn = "mysql:host=localhost;dbname=quizify";
  $username = "root";
  $password = "";

  try {
    $dbh = new PDO($dsn, $username, $password);
  } catch (PDOException $exception) {
    echo $exception->getMessage();
    exit;
  }

  return $dbh;
}

function prepare_and_execute($sql, $params = [])
{
  $dbh = connect_to_database();
  $stmt = $dbh->prepare($sql);
  $stmt->execute($params);
  $dbh = null;
  return $stmt;
}

function generate_quiz_code()
{
  while (true) {
    $code = "";
    for ($i = 0; $i < 5; $i++) {
      $code .= strval(rand(1, 9));
    }

    $sql = "SELECT 1 FROM quizzes WHERE code = ? LIMIT 1";
    $stmt = prepare_and_execute($sql, [$code]);

    if ($stmt->rowCount() == 0) {
      return $code;
    }
  }
}

function has_submissions_for_quiz($quiz_id)
{
  $sql = "SELECT 1 FROM submissions WHERE quiz_id = ? LIMIT 1";
  $stmt = prepare_and_execute($sql, [$quiz_id]);
  return $stmt->rowCount() == 1;
}

function display_success_message($message)
{
  echo <<<EOD
    <div class="flex items-center bg-green-50 rounded-lg py-3 px-3 border border-green-600 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#4ade80" aria-hidden="true" class="w-6 h-6">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path>
      </svg>
      <p class="text-green-700 text-[14px] px-2">$message</p>
    </div>
  EOD;
}

function display_error_message($message)
{
  echo <<<EOD
    <div class="flex items-center bg-red-50 rounded-lg py-3 px-3 border border-red-600 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f87171" aria-hidden="true" class="w-6 h-6">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path>
      </svg>
      <p class="text-red-700 text-[14px] px-2">$message</p>
    </div>
  EOD;
}

function delete_question($question_id)
{
  $sql = "SELECT type FROM questions WHERE id = ?";
  $stmt = prepare_and_execute($sql, [$question_id]);
  $type = $stmt->fetchColumn();

  $sql = "DELETE FROM questions WHERE id = ?";
  prepare_and_execute($sql, [$question_id]);

  if ($type == "MC") {
    $sql = "DELETE FROM choices WHERE question_id = ?";
    prepare_and_execute($sql, [$question_id]);

    $sql = "DELETE FROM answers WHERE question_id = ?";
    prepare_and_execute($sql, [$question_id]);
  }

  if ($type == "TF") {
    $sql = "DELETE FROM answers WHERE question_id = ?";
    prepare_and_execute($sql, [$question_id]);
  }
}
