<?php
 
<<<<<<< HEAD
$host = getenv('DB_HOST') ?: 'db';
=======
$host = getenv('DB_HOST');
>>>>>>> origin/F1
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
 
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Verbindung fehlgeschlagen: ' . $e->getMessage());
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/F1
