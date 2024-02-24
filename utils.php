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
  return $stmt;
}

function generate_quiz_code()
{
  $pin = "";
  for ($i = 0; $i < 5; $i++) {
    $pin .= strval(rand(1, 9));
  }
  return $pin;
}

function get_new_quiz_code()
{
  while (true) {
    $code = generate_quiz_code();

    $sql = "SELECT * FROM quizzes WHERE code = ? LIMIT 1";
    $stmt = prepare_and_execute($sql, [$code]);

    if ($stmt->rowCount() == 0) {
      return $code;
    }
  }
}
