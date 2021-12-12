<?php

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}

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
        die($db->errorInfo());
    }
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(":name", $form['name']);
    $stmt->bindParam(":email", $form['email']);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":picture", $form['image']);

    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
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
        die($db->errorInfo());
    }

    $stmt->bindValue("email", $email);
    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
    }
    $stmt->bindColumn('cnt', $cnt, PDO::PARAM_INT);
    $stmt->fetch(PDO::FETCH_BOUND);

    return $cnt >  0;
}

function login(string $email, string $password): array
{
    $db = dbConnect();
    $stmt = $db->prepare('select id, name, password from members where email = :email limit 1');
    if (!$stmt) {
        die($db->errorInfo());
    }
    $stmt->bindValue(':email', $email);
    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
    }

    $stmt->bindColumn('id', $id, PDO::PARAM_INT);
    $stmt->bindColumn('name', $name, PDO::PARAM_STR);
    $stmt->bindColumn('password', $hash, PDO::PARAM_STR);
    $stmt->fetch(PDO::FETCH_BOUND);

    if (password_verify($password, $hash)) {
        return [
            'id' => $id,
            'name' => $name,
        ];
    } else {
        return [];
    }
}

function postMessage(string $message, int $member_id): void
{
     $db = dbConnect();
     $stmt = $db->prepare('insert into posts (message, member_id) values (:message, :member_id)');
     if (!$stmt) {
         die($db->errorInfo());
     }

     $stmt->bindValue(':message', $message);
     $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
     $success = $stmt->execute();
     if (!$success) {
         die($stmt->errorInfo());
     }
}

function getPosts(): array
{
    $db = dbConnect();

    $sql = 'select p.id, p.member_id, p.message, p.created, m.name, m.picture
        from posts p, members m
        where m.id = p.member_id
        order by id desc';

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die($db->errorInfo());
    }

    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPost(int $id): array
{
    $db = dbConnect();

    $sql = 'select p.id, p.member_id, p.message, p.created, m.name, m.picture
        from posts p, members m
        where m.id = p.member_id
        and p.id = :id';

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die($db->errorInfo());
    }

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        return [];
    }
    return $result;
}

function deletePost(int $post_id, int $member_id) {
    $db = dbConnect();
    $stmt = $db->prepare('delete from posts where id = :id and member_id = :member_id limit 1');
    if (!$stmt) {
        die($db->errorInfo());
    }

    $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    if (!$success) {
        die($stmt->errorInfo());
    }
}