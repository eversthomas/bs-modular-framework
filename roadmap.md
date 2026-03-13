# BS_Modular-Framework – Roadmap

## Phase 1 – Fundament & Plugin-Gerüst
- [x] Plugin-Header-Datei (`bs-modular-framework.php`) mit korrekten WP-Metadaten
- [x] `composer.json` mit PSR-4-Autoloading für `BS\ModularFramework`
- [x] Core-Klassen: `Plugin.php`, `Loader.php`, `Activator.php`, `Deactivator.php`, `Migrator.php`
- [x] `Capabilities.php` für eigene Berechtigungen
- [x] Aktivierungs-Hook mit DB-Anlage für `modules`, `fields`, `entries`, `field_values`
- [x] Datenbankversion als Option speichern
- [x] `uninstall.php` mit sauberem Entfernen aller Plugin-Daten
- [x] `readme.txt` Grundgerüst
- [x] Basisstruktur für Admin-Menü registrierbar

**Validierung Phase 1:**
- [x] Plugin lässt sich aktivieren/deaktivieren ohne PHP-Fehler
- [x] DB-Tabellen werden korrekt angelegt
- [x] Deinstallationslogik ist vorhanden
- [x] Autoloading funktioniert
- [x] PHPUnit Smoke-Test für Plugin-Bootstrap vorhanden

---

## Phase 2 – Domain-Modell, Repositories & Feldtypen
- [x] Domain-Klassen: `Module`, `FieldDefinition`, `Entry`, `FieldValue`
- [x] Abstrakte `Repository.php`
- [x] `ModuleRepository.php`
- [x] `FieldRepository.php`
- [x] `EntryRepository.php`
- [x] `FieldValueRepository.php`
- [x] `FieldTypeRegistry.php`
- [x] `FieldTypeInterface.php` + `AbstractFieldType.php`
- [x] Feldtypen: Text, Textarea, Number, Email, URL, Date, Select, Checkbox, Image
- [x] `Validator.php` und `Sanitizer.php`
- [x] Validierungs- und Sanitization-Logik pro Feldtyp

**Validierung Phase 2:**
- [ ] Unit-Tests für Feldtypen vorhanden
- [x] Repositories können Daten lesen/schreiben
- [ ] Ungültige Werte werden korrekt abgelehnt
- [ ] Select speichert nur erlaubte Optionen
- [ ] Image-Feld akzeptiert Medien-ID als gültiges Format

---

## Phase 3 – Modulverwaltung im Admin
- [x] `AdminMenu.php`
- [x] `ModuleAdminPage.php`
- [x] `ModuleListTable.php`
- [x] View `modules-list.php`
- [x] View `module-form.php`
- [x] Module anlegen, bearbeiten, löschen
- [x] Nonces und Capability-Checks in allen Modul-Aktionen
- [x] Tabellenansicht mit Such-/Sortiergrundlage, sofern sinnvoll
- [x] WordPress-Standard-Notices für Erfolg/Fehler

**Validierung Phase 3:**
- [x] Modul kann im Admin angelegt werden
- [x] Modul kann bearbeitet werden
- [x] Modul kann gelöscht werden
- [x] Keine PHP-Warnings/Notices im WP_DEBUG-Modus
- [x] UI ist klar und WordPress-konform

---

## Phase 4 – Feldverwaltung je Modul
- [x] `FieldAdminPage.php`
- [x] View `fields-list.php`
- [x] View `field-form.php`
- [x] Felder pro Modul anlegen, bearbeiten, löschen
- [x] Feldsortierung speichern
- [x] Pflichtfeld-Option
- [x] Select-Konfiguration über einfache Text-/Listenlogik
- [ ] Bildfeld-Konfiguration integrieren
- [x] Feld-Keys innerhalb eines Moduls eindeutig validieren

**Validierung Phase 4:**
- [x] Feld kann einem Modul hinzugefügt werden
- [x] Feldtypen werden korrekt gespeichert
- [x] Select-Feld akzeptiert und validiert Optionen korrekt
- [x] Doppelte Feld-Keys werden verhindert
- [x] Bildfeld ist als Typ auswählbar

---

## Phase 5 – Eintragsverwaltung im Admin
- [x] `EntryAdminPage.php`
- [x] `EntryListTable.php`
- [x] View `entries-list.php`
- [x] View `entry-form.php`
- [x] Formular wird dynamisch aus Felddefinitionen erzeugt
- [x] Einträge anlegen, bearbeiten, löschen
- [x] Validierung je Feldtyp
- [x] Speicherung in `entries` + `field_values`
- [x] Bildfeld mit WordPress-Medienmanager nutzbar
- [x] Admin-JS für Medienmanager minimal integrieren
- [x] `MediaManager.php` + `media-manager.js`

**Validierung Phase 5:**
- [x] Eintrag kann angelegt werden
- [x] Eintrag kann bearbeitet werden
- [x] Eintrag kann gelöscht werden
- [x] Pflichtfelder werden erzwungen
- [x] Bild kann über Medienmanager gewählt werden
- [x] Gespeicherte Werte werden korrekt wieder geladen
- [x] Keine JS-Konsolenfehler im Normalbetrieb

---

## Phase 5b – UX, Navigation & Klarheit
- [ ] Hauptmenü-Bezeichnung von „Modular Framework“ zu „Module“ anpassen
- [ ] Submenü „Felder“ in übersichtliche Feldliste mit Modul-Filter umbauen
- [ ] Submenü „Einträge“ in übersichtliche Eintragsliste mit Modul-Filter umbauen
- [ ] Zeilenaktionen in der Modulliste klar und schrittweise benennen (z. B. „Felder definieren“, „Einträge verwalten“)
- [ ] Einleitende Beschreibungstexte auf Modul-, Feld- und Eintrags-Seiten ergänzen
- [ ] Kontext („für Modul: …“) in Überschriften visuell hervorheben
- [ ] Weiche Fallbacks statt `wp_die()` bei fehlender `module_id` (Rückleitungen zur Modulliste)
- [ ] Leere Zustände (keine Module/Felder/Einträge) mit erklärenden Texten + primären Aktionen gestalten
- [ ] Pflichtfelder visuell klar kennzeichnen und erläutern

**Validierung Phase 5b:**
- [ ] User versteht ohne Doku den Flow „Modul → Felder → Einträge“
- [ ] Globales „Felder“-Menü ist ohne Vorwissen nutzbar (Modul-Auswahl klar)
- [ ] Globales „Einträge“-Menü ist ohne Vorwissen nutzbar (Modul-Auswahl klar)
- [ ] Es treten im regulären Nutzungsfluss keine harten Abbrüche (`wp_die`) mehr auf

---

## Phase 6 – Polishing, Sicherheit & Stabilisierung
- [ ] Vollständige Escaping-Prüfung
- [ ] Vollständige Nonce-Prüfung
- [ ] Vollständige Capability-Prüfung
- [ ] `admin.css` nur minimal und gezielt
- [ ] Übersetzbarkeit aller Strings vorbereiten
- [ ] `.pot`-Datei vorbereiten
- [ ] PHPCS-Kompatibilität verbessern
- [ ] Basis-Tests vervollständigen
- [ ] DB-Indizes auf sinnvolle Kernspalten prüfen
- [ ] Code dokumentieren

**Validierung Phase 6:**
- [ ] Keine offenen Sicherheitslücken in offensichtlichen Formularpfaden
- [ ] WP_DEBUG ohne Notices/Warnings
- [ ] PHPCS weitgehend sauber
- [ ] Plugin-MVP ist manuell testbar und stabil

---

## Gesamt-Status
- [x] Phase 1 abgeschlossen
- [ ] Phase 2 abgeschlossen
- [x] Phase 3 abgeschlossen
- [x] Phase 4 abgeschlossen
- [x] Phase 5 abgeschlossen
- [ ] Phase 5b abgeschlossen
- [ ] Phase 6 abgeschlossen


Bemerkungen: Wir müssen auch noch sicherstellen, dass mögliche erstellte Listen im Module, von anderen Plugins oder Funktionen benutzt werden können, sowie einen Weg, diese auch gezielt auf Seiten oder in Beiträgen nutzbar zu machen.
