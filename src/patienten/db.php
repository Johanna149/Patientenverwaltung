<?php

// Datenbank-Zugangsdaten aus den Umgebungsvariablen laden
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

try {

    // Verbindung zur MySQL-Datenbank herstellen
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            // Fehler werden als Exceptions behandelt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Datenbankergebnisse werden als assoziative Arrays zurückgegeben
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

} catch (PDOException $e) {

    // Keine technischen Datenbankdetails an Benutzer ausgeben
    error_log($e->getMessage());

    die('Die Verbindung zur Datenbank ist fehlgeschlagen. Bitte versuche es später erneut.');
}

?>