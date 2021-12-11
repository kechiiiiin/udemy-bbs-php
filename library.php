<?php

function dbConnect()
{
    $user = 'keisuke';
    $password = 'keisuke';
    $dbName = 'udemy';
    $host = 'training-db';
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

    try {
        $dbh = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } catch (PDOException $e) {
        echo '接続失敗' . $e->getMessage();
        exit();
    };
    return $dbh;
}

function createUser(array $form): void
{
    $db = dbConnect();
    if ($db === null) {
        die('DBに接続できない');
    }

    $stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (:name, :email, :password, :picture)');
    if (!$stmt) {
        die("prepare失敗");
    }
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(":name", $form['name']);
    $stmt->bindParam(":email", $form['email']);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":picture", $form['image']);

    $success = $stmt->execute();
    if (!$success) {
        die('execute失敗');
    }
}

function existsEmail(string $email): bool
{
    $db = dbConnect();
    if ($db === null) {
        die('DBに接続できない');
    }

    $stmt = $db->prepare('select count(*) as cnt from members where email = :email');
    if (!$stmt) {
        die('prepareNG');
    }

    $stmt->bindParam("email", $email);
    $success = $stmt->execute();
    if (!$success) {
        die('executeNG');
    }
    $stmt->bindColumn('cnt', $cnt, PDO::PARAM_INT);
    $stmt->fetch(PDO::FETCH_BOUND);

    return $cnt >  0;
}

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}