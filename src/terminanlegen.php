
<?php
require_once 'db.php';

$meldung = "";
$fehler = "";

// Termin speichern
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_sv = $_POST["patient_sv"] ?? "";
    $arzt_id = $_POST["arzt_id"] ?? "";
    $datum = $_POST["datum"] ?? "";
    $uhrzeit = $_POST["uhrzeit"] ?? "";
    $grund = $_POST["grund"] ?? "";

    // Pflichtfelder überprüfen
    if (empty($patient_sv) || empty($arzt_id) ||
        empty($datum) || empty($uhrzeit)) {

        $fehler = "Bitte alle Pflichtfelder ausfüllen.";

    } else {
        try {
            $sql = "INSERT INTO termin
                    (datum, uhrzeit, grund, patient_sv, arzt_id)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $datum,
                $uhrzeit,
                $grund,
                $patient_sv,
                $arzt_id
            ]);

            $meldung = "Der Termin wurde erfolgreich angelegt.";

        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                $fehler = "Dieser Arzt hat zu dieser Zeit bereits einen Termin.";
            } else {
                $fehler = "Fehler beim Speichern des Termins.";
            }
        }
    }
}

// Patient:innen aus der Datenbank laden
$stmt = $pdo->query(
    "SELECT sv_nummer, vorname, nachname
     FROM patient
     ORDER BY nachname, vorname"
);
$patienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ärzt:innen aus der Datenbank laden
$stmt = $pdo->query(
    "SELECT id, titel, vorname, nachname, fachrichtung
     FROM arzt
     ORDER BY nachname, vorname"
);
$aerzte = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Termin anlegen</title>
</head>

<body>

<h1>Neuen Termin anlegen</h1>

<?php if ($meldung): ?>
    <p style="color: green;">
        <?= htmlspecialchars($meldung) ?>
    </p>
<?php endif; ?>

<?php if ($fehler): ?>
    <p style="color: red;">
        <?= htmlspecialchars($fehler) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label>Patient:in:</label><br>

    <select name="patient_sv" required>
        <option value="">Patient:in auswählen</option>

        <?php foreach ($patienten as $patient): ?>
            <option value="<?= htmlspecialchars($patient["sv_nummer"]) ?>">
                <?= htmlspecialchars(
                    $patient["vorname"] . " " .
                    $patient["nachname"]
                ) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <br><br>

    <label>Arzt:</label><br>

    <select name="arzt_id" required>
        <option value="">Arzt auswählen</option>

        <?php foreach ($aerzte as $arzt): ?>
            <option value="<?= $arzt["id"] ?>">
                <?= htmlspecialchars(
                    $arzt["titel"] . " " .
                    $arzt["vorname"] . " " .
                    $arzt["nachname"] . " – " .
                    $arzt["fachrichtung"]
                ) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <br><br>

    <label>Datum:</label><br>
    <input type="date" name="datum" required>

    <br><br>

    <label>Uhrzeit:</label><br>
    <input type="time" name="uhrzeit" required>

    <br><br>

    <label>Grund des Termins:</label><br>
    <input type="text" name="grund">

    <br><br>

    <button type="submit">Termin speichern</button>

</form>

</body>
</html>
