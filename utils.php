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
