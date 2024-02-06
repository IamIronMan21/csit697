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

function execute_query($sql, $params = [])
{
  $dbh = connect_to_database();
  $stmt = $dbh->prepare($sql);
  $stmt->execute($params);
  return $stmt;
}

function get_tutor($tutor_id)
{
  $sql = "SELECT * FROM tutors WHERE id = ?";
  $stmt = execute_query($sql, [$tutor_id]);
  return $stmt->fetch();
}

function get_courses_for_tutor($tutor_id)
{
  $sql = "SELECT * FROM courses WHERE tutor_id = ?";
  $stmt = execute_query($sql, [$tutor_id]);
  return $stmt->fetchAll();
}

function get_course($course_id)
{
  $sql = "SELECT * FROM courses WHERE id = ? LIMIT 1";
  $stmt = execute_query($sql, [$course_id]);
  return $stmt->fetch();
}

function get_quizzes_for_course($course_id)
{
  $sql = "SELECT * FROM quizzes WHERE course_id = ?";
  $stmt = execute_query($sql, [$course_id]);
  return $stmt->fetchAll();
}

function get_quiz($quiz_id)
{
  $sql = "SELECT * FROM quizzes WHERE id = ? LIMIT 1";
  $stmt = execute_query($sql, [$quiz_id]);
  return $stmt->fetch();
}

function create_question($content, $quiz_id)
{
  $sql = "INSERT INTO questions (content, quiz_id) VALUES (?, ?)";
  $stmt = execute_query($sql, [$content, $quiz_id]);
  return $stmt->fetch();
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
  $dbh = connect_to_database();
  $code = "";

  while (true) {
    $code = generate_quiz_code();

    $sql = "SELECT * FROM quizzes WHERE code = ? LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$code]);

    if ($stmt->rowCount() == 0) {
      return $code;
    }
  }
}
