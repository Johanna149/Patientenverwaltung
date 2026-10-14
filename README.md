# Patientenverwaltung

## Projektbeschreibung

Im Rahmen dieses Projekts entwickeln wir eine PHP-Webanwendung zur Verwaltung von Patient:innen, Ärzt:innen und Terminen für die Gruppenpraxis „Gesundheitszentrum Donaublick“.

Die Anwendung soll die bisherige Verwaltung mit Karteikarten und Papierkalender ersetzen und eine übersichtliche, gemeinsame Datenverwaltung ermöglichen.

## Funktionen

- Patient:innen anlegen, anzeigen, bearbeiten und löschen
- Ärzt:innen inklusive Fachrichtung verwalten
- Termine für Patient:innen und Ärzt:innen erstellen
- Doppelbuchungen eines Arztes verhindern
- Serverseitige Validierung der Formulare
- Suche nach Patient:innen (Zusatzfunktion)
- Tagesansicht der Termine eines Arztes (Zusatzfunktion)

## Verwendete Technologien

- PHP 8.2
- Apache
- MySQL 8.0
- Docker und Docker Compose
- PDO für den Datenbankzugriff
- Git und GitHub für die Versionsverwaltung

## Voraussetzungen

- Docker Desktop bzw. Docker Engine
- Docker Compose
- Git (optional, zum Klonen des Repositorys)

## Installation und Start

1. Repository herunterladen oder klonen.
2. Die Datei `.env.example` kopieren und als `.env` speichern.
3. Die Zugangsdaten in der `.env`-Datei überprüfen bzw. anpassen.
4. Im Projektverzeichnis folgenden Befehl ausführen:

   ```bash
   docker compose up -d
