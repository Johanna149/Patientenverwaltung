<?php
require_once 'db.php';//Datenbankverbindung
//require_once bindet die Datenbank nur einmal ein

//in $sql wird die SQL-Abfrage gespeichert
//alle Termine werden mit den dazugehörigen Patienten und Ärzten aus der DB geladen
//JOIN 1 wird verwendet um Patienteninfos mit Terminen Tabelle zu verbinden
//JOIN 2 wird verwendet um Ärzteinfos mit Terminen Tabelle zu verbinden
//ORDER BY sortiert die Termine nach Datum und Uhrzeit
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
//gespeicherte SQL-Abfrage wird ausgeführt //$pdo->Datenbankverbindung,query führt die SQL Abfrage aus
$stmt = $pdo->query($sql);
//ausgeführte SQL-Abfrage wird in $termine gespeichert (alle Termine, die gefunden wurden)
$termine = $stmt->fetchAll();
?>

<h1>Alle Termine</h1>
<!--link damit man neuen Termin anlegen kann, auf index.php sichtbar-->
<a href="termin_create.php">Neuen Termin anlegen</a> 
<!--Tabelle zum Ausfüllen, damit Termin angelegt werden kann-->
<table border="1">
    <tr>
        <th>Patient</th>
        <th>Arzt</th>
        <th>Datum</th>
        <th>Startzeit</th>
        <th>Grund</th>
    </tr>

    <?php foreach ($termine as $termin): ?><!--foreach Schleife, die alle $termine durchläuft und in der Tabelle als $termin anzeigt-->
        <tr>
            <td>
                <?= htmlspecialchars($termin['patient_vorname'] . ' ' . $termin['patient_nachname']) ?>
                <!--punkt greift auf vornamen des Patienten zu und zeigt ihn in der Tabelle an-->
                <!--Anführungszeichen verbinden Zeichenketten, in diesem Fall Vorname und Nachname des Patienten-->
                <!--htmlspecialchars() schützt vor XSS-Angriffen, indem es Sonderzeichen in HTML-Entities umwandelt-->
                <!--selbiges Prinzip wird für Ärzte,datum,Startzeit und Grund angewendet-->
            </td>
            <td>
                <?= htmlspecialchars($termin['titel'] . ' ' . $termin['arzt_vorname'] . ' ' . $termin['arzt_nachname']) ?>
            </td>
            <td><?= htmlspecialchars($termin['datum']) ?></td>
            <td><?= htmlspecialchars($termin['uhrzeit']) ?></td>
            <td><?= htmlspecialchars($termin['grund']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="index.php">Zurück zur Startseite</a><br>
