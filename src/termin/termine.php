<?php

require_once __DIR__ . '/../patienten/db.php'; // Datenbankverbindung
// require_once bindet die Datenbank nur einmal ein

// In $sql wird die SQL-Abfrage gespeichert
// Alle Termine werden mit den dazugehörigen Patienten und Ärzten aus der DB geladen
// JOIN 1 wird verwendet, um Patienteninfos mit der Termine-Tabelle zu verbinden
// JOIN 2 wird verwendet, um Ärzteinfos mit der Termine-Tabelle zu verbinden
// ORDER BY sortiert die Termine nach Datum und Uhrzeit

$sql = "
    SELECT
        termin.id,
        termin.datum,
        termin.uhrzeit,
        termin.grund,
        patient.vorname AS patient_vorname,
        patient.nachname AS patient_nachname,
        arzt.titel,
        arzt.vorname AS arzt_vorname,
        arzt.nachname AS arzt_nachname
    FROM termin
    JOIN patient ON termin.patient_sv = patient.sv_nummer
    JOIN arzt ON termin.arzt_id = arzt.id
    ORDER BY termin.datum, termin.uhrzeit
";

// Gespeicherte SQL-Abfrage wird ausgeführt
// $pdo->query führt die SQL-Abfrage aus
$stmt = $pdo->query($sql);

// Ausgeführte SQL-Abfrage wird in $termine gespeichert
// Alle gefundenen Termine werden geladen
$termine = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Alle Termine</title>
</head>

<body>

    <h1>Alle Termine</h1>

    <!-- Link zum Anlegen eines neuen Termins -->
    <a href="termin_anlegen.php">Neuen Termin anlegen</a>

    <!-- Tabelle zur Anzeige aller Termine -->
    <table border="1">

        <tr>
            <th>Patient</th>
            <th>Arzt</th>
            <th>Datum</th>
            <th>Startzeit</th>
            <th>Grund</th>
        </tr>

        <?php foreach ($termine as $termin): ?>
            <tr>

                <td>
                    <?= htmlspecialchars(
                        $termin['patient_vorname'] . ' ' .
                        $termin['patient_nachname']
                    ) ?>
                    <!-- htmlspecialchars() schützt vor XSS-Angriffen -->
                </td>

                <td>
                    <?= htmlspecialchars(
                        $termin['titel'] . ' ' .
                        $termin['arzt_vorname'] . ' ' .
                        $termin['arzt_nachname']
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars($termin['datum']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($termin['uhrzeit']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($termin['grund']) ?>
                </td>

            </tr>
        <?php endforeach; ?>

        <?php if (count($termine) === 0): ?>
            <tr>
                <td colspan="5">Es sind noch keine Termine vorhanden.</td>
            </tr>
        <?php endif; ?>

    </table>

    <br>

    <a href="../index.php">Zurück zur Startseite</a>

</body>

</html>