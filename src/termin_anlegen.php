<?php
require_once 'db.php';
 
// Patienten aus der Datenbank laden
$stmt = $pdo->query("
    SELECT sv_nummer, vorname, nachname
    FROM patient
    ORDER BY nachname, vorname
");
$patienten = $stmt->fetchAll();
 
// Ärzte aus der Datenbank laden
$stmt = $pdo->query("
    SELECT id, titel, vorname, nachname, fachrichtung
    FROM arzt
    ORDER BY nachname, vorname
");
$aerzte = $stmt->fetchAll();
 
$fehler = '';
 
// Wenn das Formular abgeschickt (POST) wurde, die Daten verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = $_POST['datum'];
    $uhrzeit = $_POST['uhrzeit'];
    $grund = trim($_POST['grund']);
    $patient_sv = $_POST['patient_sv'];
    $arzt_id = $_POST['arzt_id'];
 
    //wenn nicht alle Felder ausgefüllt werden bei der Termineingabe, dann soll eine Fehlermeldung kommen
    if (
        $datum === '' ||
        $uhrzeit === '' ||
        $grund === '' ||
        $patient_sv === '' ||
        $arzt_id === ''
    ) {
        $fehler = 'Bitte alle Felder ausfüllen.';
 
    //wenn alle nötigen Felder ausgefüllt wurden, dann wird ein Termin in der Datenbank gespeichert
    //wenn Speicherung abgeschlossen -> weiterleitung auf termine.php
    } else {
        $sql = "
            INSERT INTO termin
            (datum, uhrzeit, grund, patient_sv, arzt_id)
            VALUES
            (:datum, :uhrzeit, :grund, :patient_sv, :arzt_id)
        ";
 
        $stmt = $pdo->prepare($sql);
 
        //F4: keine Doppelbuchung von Terminen zulassen
        try {
            $stmt->execute([
                ':datum' => $datum,
                ':uhrzeit' => $uhrzeit,
                ':grund' => $grund,
                ':patient_sv' => $patient_sv,
                ':arzt_id' => $arzt_id
            ]);
 
            header('Location: termine.php');
            exit;
 
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $fehler = 'Fehler: Dieser Arzt hat zu dieser Zeit bereits einen Termin.';
            } else {
                throw $e;
            }
        }
    }
}
?>
 
<h1>Neuen Termin anlegen</h1>
 
<!--Meldung gehört zu F4:keine Doppelbuchung von Terminen zulassen-->
<?php if ($fehler): ?>
    <p><?= htmlspecialchars($fehler) ?></p>
<?php endif; ?>
 
 
<form method="POST">
<!--Auswahlfeld für Patienten, die in der Datenbank gespeichert sind-->
    <label>Patient:</label>
    <select name="patient_sv" required>
        <option value="">Patient auswählen</option>
        <?php foreach ($patienten as $patient): ?>
            <option value="<?= htmlspecialchars($patient['sv_nummer']) ?>">
                <?= htmlspecialchars($patient['vorname'] . ' ' . $patient['nachname']) ?>
            </option>
        <?php endforeach; ?>
    </select>
 
    <br><br>
 
<!--Auswahlfeld für Ärzte, die in der Datenbank gespeichert sind-->
    <label>Arzt:</label>
    <select name="arzt_id" required>
<!--gibt name des Auswahlfeldes an, das beim Absenden des Formulars an den Server gesendet wird-->
<!--required bedeutet, dass man etwas auswählen muss, bevor man das Formular überhaupt absenden kann-->
        <option value="">Arzt auswählen</option>
        <?php foreach ($aerzte as $arzt): ?>
            <option value="<?= $arzt['id'] ?>">
                <?= htmlspecialchars($arzt['titel'] . ' ' . $arzt['vorname'] . ' ' . $arzt['nachname'] . ' (' . $arzt['fachrichtung'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
 
    <br><br>
 
<!--hier wird das Datum, die Startzeit und der Grund für den Termin eingegeben-->
    <label>Datum:</label>
    <input type="date" name="datum" required>
 
    <br><br>
 
    <label>Startzeit:</label>
    <input type="time" name="uhrzeit" required>
 
    <br><br>
 
    <label>Grund:</label>
    <input type="text" name="grund" maxlength="255" required>
 
    <br><br>
 
<!--submit button, um den Termin zu speichern und ein Link, um den Vorgang abzubrechen und zurück zur Terminübersicht zu gelangen-->
    <button type="submit">Termin speichern</button>
    <a href="termine.php">Abbrechen</a>
 
</form>
 