# Patientenverwaltung
rojektbeschreibung

Im Rahmen dieses Projekts entwickeln wir eine PHP-Webanwendung zur Verwaltung von Patient, Ärzt und Terminen für die Gruppenpraxis „Gesundheitszentrum Donaublick“.

Die Anwendung soll die bisherige Verwaltung mit Karteikarten und Papierkalender ersetzen und eine übersichtliche, gemeinsame Datenverwaltung ermöglichen.

Funktionen

Patient anlegen, anzeigen, bearbeiten und löschen

Ärzt inklusive Fachrichtung verwalten

Termine für Patient und Ärzt erstellen

Doppelbuchungen eines Arztes verhindern

Serverseitige Validierung der Formulare

Suche nach Patient (Zusatzfunktion)

Tagesansicht der Termine eines Arztes (Zusatzfunktion)

Verwendete Technologien

PHP 8.2

Apache

MySQL 8.0

Docker und Docker Compose

PDO für den Datenbankzugriff

Git und GitHub für die Versionsverwaltung

Voraussetzungen

Docker Desktop bzw. Docker Engine

Docker Compose

Git (optional, zum Klonen des Repositorys)

Installation und Start

Repository herunterladen oder klonen.

Die Datei .env.example kopieren und als .env speichern.

Die Zugangsdaten in der .env-Datei überprüfen bzw. anpassen.

Im Projektverzeichnis folgenden Befehl ausführen:

docker compose up -d

Die Webanwendung im Browser öffnen:

http://localhost:9999

Hinweis: Falls ein anderer Port in der docker-compose.yaml festgelegt ist, muss die URL entsprechend angepasst werden.

Datenbank

Die Datenbank wird über die Datei init.sql eingerichtet. Diese enthält das Datenmodell sowie erfundene Testdaten für Patient, Ärzt und Termine.

Datenschutz

Es werden ausschließlich erfundene Testdaten verwendet. Echte personenbezogene Gesundheitsdaten werden nicht gespeichert.

Projektteam

Johanna Schindler

Lea Müller

Versionsverwaltung

Das Projekt wird gemeinsam über Git und GitHub verwaltet. Neue Funktionen werden in eigenen Branches entwickelt und anschließend über Pull Requests zusammengeführt.
