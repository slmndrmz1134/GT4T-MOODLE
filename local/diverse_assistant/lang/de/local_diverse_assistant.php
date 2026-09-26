<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * DIVERSE AI assistant - Language strings (German)
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'API-Schlüssel';
$string['apikey_desc_anthropic'] = 'Unter console.anthropic.com > API keys (beginnt mit sk-ant-).';
$string['apikey_desc_gemini'] = 'Unter aistudio.google.com > Get API key (beginnt mit AIza). Verwenden Sie den Schlüssel eines Projekts mit aktivierter Abrechnung: Im kostenlosen Kontingent kann Google die Fragen zur Verbesserung seiner Produkte nutzen.';
$string['apikey_desc_openai'] = 'Unter platform.openai.com > API keys (beginnt mit sk-; Zahlungsdaten müssen hinterlegt sein). Ein ChatGPT-Abo ist kein API-Schlüssel.';
$string['apikey_desc_openaicompatible'] = 'Der Schlüssel des Dienstes unter der API-Adresse unten.';
$string['apikey_placeholder_anthropic'] = 'sk-ant-...';
$string['apikey_placeholder_gemini'] = 'AIza...';
$string['apikey_placeholder_openai'] = 'sk-...';
$string['apikey_placeholder_openaicompatible'] = 'API-Schlüssel';
$string['apikey_placeholder_set'] = 'Ein Schlüssel ist gespeichert. Fügen Sie einen neuen ein, um ihn zu ersetzen.';
$string['apikey_remove'] = 'Gespeicherten Schlüssel entfernen';
$string['apikey_saved'] = 'Ein Schlüssel ist gespeichert (verschlüsselt).';
$string['apply_error_changed'] = 'Der Text wurde geändert, nachdem der Assistent diesen Vorschlag gemacht hat, daher wurde er nicht übernommen. Fragen Sie den Assistenten erneut, damit er mit der aktuellen Fassung arbeitet.';
$string['apply_error_formonly'] = 'Diese Aktivität kann nur in ihrem Bearbeitungsformular geändert werden. Verwenden Sie „Im Formular öffnen“.';
$string['apply_error_missing'] = 'Die Aktivität oder der Abschnitt existiert nicht mehr.';
$string['apply_error_status'] = 'Dieser Vorschlag wurde bereits bearbeitet.';
$string['back'] = 'Zurück';
$string['closepanel'] = 'Schließen';
$string['connectionheading'] = 'KI-Dienst';
$string['connectionheading_desc'] = 'Wählen Sie den Dienst, fügen Sie seinen API-Schlüssel ein und speichern Sie. Schlüssel werden verschlüsselt gespeichert und nie wieder angezeigt; ein leeres Schlüsselfeld behält den gespeicherten Schlüssel. Ein Claude- (sk-ant-…) oder Gemini-Schlüssel (AIza…) wechselt in jedem Schlüsselfeld automatisch zu diesem Dienst. Nach dem Speichern wird die Verbindung geprüft (oben angezeigt), und das Menü „Modell“ zeigt die Modelle, die der Schlüssel nutzen kann.';
$string['delete'] = 'Löschen';
$string['deleteconversation'] = 'Chat löschen';
$string['deleteconversation_confirm'] = 'Den Chat „{$a}“ löschen? Dies kann nicht rückgängig gemacht werden.';
$string['deletehistory'] = 'Alle meine Chats löschen';
$string['deletehistory_confirm'] = 'Alle Ihre gespeicherten Chats in allen Kursen löschen? Dies kann nicht rückgängig gemacht werden.';
$string['deletehistory_desc'] = 'Löscht alle auf dem Server gespeicherten Chats in allen Kursen und die Änderungen, die der Assistent Ihnen vorgeschlagen hat.';
$string['diverse_assistant:teach'] = 'KI-Assistenten zum Schreiben und Bearbeiten von Kursinhalten nutzen';
$string['diverse_assistant:use'] = 'KI-Kursassistent verwenden';
$string['emptytitle'] = 'Fragen Sie alles zu diesem Kurs.';
$string['enabled'] = 'Assistent aktivieren';
$string['enabled_desc'] = 'Zeigt den Reiter des KI-Assistenten auf Kursseiten allen, die ihn nutzen dürfen (standardmäßig Studierende und Lehrende; Fähigkeit local/diverse_assistant:use). Lehrende, die den Kurs bearbeiten dürfen, erhalten den Autorenassistenten, der neue Seiten und Änderungen vorschlägt (Fähigkeit local/diverse_assistant:teach).';
$string['endpoint'] = 'API-Adresse';
$string['endpoint_desc'] = 'Adresse der OpenAI-kompatiblen API einschließlich Version, z. B. https://api.mistral.ai/v1. Lokale Adressen (z. B. Ollama) müssen zusätzlich unter Website-Administration > Allgemein > Sicherheit > HTTP-Sicherheit erlaubt werden.';
$string['endpointineu'] = 'Dieser Dienst verarbeitet Daten in der EU';
$string['endpointineu_desc'] = 'Nur ankreuzen, wenn der Dienst vertraglich garantiert, dass Anfragen in der Europäischen Union verarbeitet werden. Erforderlich, wenn „Nur EU“ aktiviert ist.';
$string['errorauth'] = 'Der KI-Dienst hat den API-Schlüssel abgelehnt.';
$string['errorbusy'] = 'Der KI-Dienst ist gerade stark ausgelastet. Bitte versuchen Sie es gleich noch einmal.';
$string['errorconnection'] = 'Der KI-Dienst ist nicht erreichbar. Bitte versuchen Sie es später erneut.';
$string['errorempty'] = 'Der KI-Dienst hat eine leere Antwort gesendet. Bitte versuchen Sie es erneut.';
$string['errorforbidden'] = 'Der KI-Dienst hat die Anfrage für diesen API-Schlüssel verweigert.';
$string['errorgeneric'] = 'Etwas ist schiefgelaufen. Bitte versuchen Sie es erneut.';
$string['errorlimit'] = 'Sie haben das Limit von {$a} Fragen pro Stunde erreicht. Bitte versuchen Sie es später erneut.';
$string['errormodel'] = 'Der KI-Dienst kennt das gewählte Modell oder die Adresse nicht.';
$string['errornoendpoint'] = 'Für den KI-Dienst ist keine API-Adresse eingetragen.';
$string['errornomessage'] = 'Bitte schreiben Sie eine Frage.';
$string['errornomodel'] = 'Für den KI-Dienst ist kein Modell eingetragen.';
$string['errornotconfigured'] = 'Der KI-Dienst ist noch nicht eingerichtet.';
$string['errornotfound'] = 'Dieser Chat existiert nicht mehr.';
$string['errorratelimit'] = 'Der KI-Dienst ist ausgelastet oder sein Nutzungslimit ist erreicht. Bitte versuchen Sie es später erneut.';
$string['errorrefusal'] = 'Die KI hat die Antwort auf diese Frage abgelehnt. Bitte formulieren Sie die Frage anders.';
$string['errorservice'] = 'Der KI-Dienst hat einen Fehler gemeldet. Bitte versuchen Sie es später erneut.';
$string['errortoolong'] = 'Die Frage ist zu lang (höchstens {$a} Zeichen).';
$string['euonly'] = 'Nur EU';
$string['euonly_desc'] = 'Wenn angekreuzt, funktioniert der Assistent nur mit einer Verbindung, die die Verarbeitung in der Europäischen Union garantiert: OpenAI mit der EU-Datenresidenz-Adresse oder ein kompatibler Dienst, dessen EU-Standort bestätigt ist. Die Claude- und Gemini-APIs bieten keine reine EU-Option. Andernfalls sehen Studierende, dass der Assistent nicht verfügbar ist.';
$string['field_content'] = 'Inhalt';
$string['field_description'] = 'Beschreibung';
$string['field_name'] = 'Name';
$string['field_sectionname'] = 'Abschnittsname';
$string['field_summary'] = 'Abschnittsbeschreibung';
$string['field_text'] = 'Text';
$string['footnote'] = 'KI kann Fehler machen. Geben Sie keine persönlichen Daten an.';
$string['history'] = 'Gespeicherte Chats';
$string['historydeleted'] = 'Ihre gespeicherten Chats wurden gelöscht.';
$string['historyempty'] = 'In diesem Kurs gibt es noch keine gespeicherten Chats.';
$string['insertedineditor'] = 'In den Editor eingefügt.';
$string['insertineditor'] = 'In den Editor einfügen';
$string['limitsheading'] = 'Grenzen';
$string['maxcontextchars'] = 'Gesendete Kursmaterialien (Zeichen)';
$string['maxcontextchars_desc'] = 'Die Kursmaterialien werden mit jeder Frage gesendet. Längere Materialien werden bei dieser Länge abgeschnitten. 60.000 Zeichen sind etwa 15.000 Token.';
$string['model'] = 'Modell';
$string['model_desc'] = 'Nach der Verbindungsprüfung zeigt das Menü die Chat-Modelle des Schlüssels. Empfohlen: gpt-6-luna (OpenAI), claude-opus-5 (Claude), gemini-3.8-flash (Gemini).';
$string['model_recommended'] = '{$a} (empfohlen)';
$string['newchat'] = 'Neuer Chat';
$string['noeditor'] = 'Klicken Sie zuerst in ein Textfeld des Formulars und fügen Sie dann ein.';
$string['notice_accept'] = 'Verstanden, starten';
$string['notice_body'] = 'Dieser Assistent verwendet künstliche Intelligenz ({$a}). Ihre Fragen und die Materialien dieses Kurses werden an diesen Dienst gesendet, um die Antworten zu erstellen. Ihr Name und Ihre E-Mail-Adresse werden nicht gesendet.';
$string['notice_mistakes'] = 'Antworten können falsch oder unvollständig sein. Prüfen Sie wichtige Informationen in den Kursmaterialien.';
$string['notice_personal'] = 'Schreiben Sie keine persönlichen Daten (Ihre eigenen oder die anderer) in Ihre Fragen.';
$string['notice_retention'] = 'Sie entscheiden unter Einstellungen, ob und wie lange Ihre Chats gespeichert werden. Standardmäßig wird nichts gespeichert.';
$string['notice_teacher'] = 'Lehrendenmodus: Der Assistent sieht die Texte dieses Kurses (auch verborgene Aktivitäten) und kann neue Seiten und Änderungen vorschlagen. Im Kurs ändert sich nichts, bevor Sie einen Vorschlag übernehmen, und eine übernommene Änderung können Sie {$a} Tage lang rückgängig machen.';
$string['notice_title'] = 'Bevor Sie beginnen';
$string['openairegion'] = 'Datenregion';
$string['openairegion_desc'] = 'Verwenden Sie die EU-Adresse nur, wenn das OpenAI-Projekt des Schlüssels mit europäischer Datenresidenz angelegt wurde.';
$string['openairegion_eu'] = 'Europäische Union (eu.api.openai.com)';
$string['openairegion_global'] = 'Standard (api.openai.com)';
$string['paneltitle'] = 'KI-Assistent';
$string['placeholder'] = 'Fragen Sie zu diesem Kurs…';
$string['pluginname'] = 'DIVERSE KI-Assistent';
$string['prefill_done'] = 'Der Vorschlag des Assistenten wurde in das Formular eingetragen. Prüfen Sie ihn und speichern Sie.';
$string['prefill_failed'] = 'Der Vorschlag konnte nicht in dieses Formular eingetragen werden.';
$string['privacy:metadata:aiservice'] = 'Für die Antwort wird die Frage zusammen mit den früheren Nachrichten des Chats und den für die Person sichtbaren Kursmaterialien an den vom Website-Administrator gewählten KI-Dienst gesendet. Name, E-Mail-Adresse und Moodle-ID werden nicht gesendet.';
$string['privacy:metadata:aiservice:coursecontent'] = 'Im Lehrendenmodus: die gespeicherten Texte des Kurses (Namen, Beschreibungen, Seiten, Abschnittsbeschreibungen), auch verborgener Aktivitäten';
$string['privacy:metadata:aiservice:coursematerials'] = 'Von Lehrenden verfasste Kursmaterialien, die die Person sehen kann';
$string['privacy:metadata:aiservice:history'] = 'Die früheren Nachrichten des Chats';
$string['privacy:metadata:aiservice:message'] = 'Die Frage';
$string['privacy:metadata:conv'] = 'Chats, deren Speicherung die Person gewählt hat; gelöscht, wenn der gewählte Zeitraum endet.';
$string['privacy:metadata:conv:courseid'] = 'Der Kurs, um den es im Chat geht';
$string['privacy:metadata:conv:timecreated'] = 'Beginn des Chats';
$string['privacy:metadata:conv:timemodified'] = 'Zeit der letzten Nachricht';
$string['privacy:metadata:conv:title'] = 'Anfang der ersten Frage';
$string['privacy:metadata:conv:userid'] = 'Die Person, der der Chat gehört';
$string['privacy:metadata:msg'] = 'Nachrichten gespeicherter Chats';
$string['privacy:metadata:msg:content'] = 'Der Text der Nachricht';
$string['privacy:metadata:msg:role'] = 'Ob die Nachricht eine Frage oder eine Antwort ist';
$string['privacy:metadata:msg:timecreated'] = 'Zeitpunkt der Nachricht';
$string['privacy:metadata:preference:notice'] = 'Zeitpunkt, zu dem die Person den Hinweis vor dem ersten Chat gelesen hat';
$string['privacy:metadata:preference:retention'] = 'Wie lange die Chats der Person aufbewahrt werden';
$string['privacy:metadata:prop'] = 'Änderungen, die der Assistent einer lehrenden Person vorgeschlagen hat; 30 Tage nach der letzten Bearbeitung aufbewahrt, damit übernommene Änderungen rückgängig gemacht werden können.';
$string['privacy:metadata:prop:courseid'] = 'Der Kurs';
$string['privacy:metadata:prop:original'] = 'Die Kurstexte vor der Änderung';
$string['privacy:metadata:prop:proposed'] = 'Die vorgeschlagenen Texte';
$string['privacy:metadata:prop:status'] = 'Ob der Vorschlag offen, übernommen, rückgängig gemacht oder abgelehnt ist';
$string['privacy:metadata:prop:timecreated'] = 'Wann die Änderung vorgeschlagen wurde';
$string['privacy:metadata:prop:timemodified'] = 'Wann der Vorschlag zuletzt bearbeitet wurde';
$string['privacy:metadata:prop:userid'] = 'Die lehrende Person, der die Änderung vorgeschlagen wurde';
$string['privacy:metadata:use'] = 'Eine Zeile pro beantworteter Frage, ohne Text, für das Stundenlimit und die Nutzungssummen; 30 Tage aufbewahrt.';
$string['privacy:metadata:use:completiontokens'] = 'Länge der Antwort in Token';
$string['privacy:metadata:use:courseid'] = 'Der Kurs';
$string['privacy:metadata:use:prompttokens'] = 'Länge der Anfrage in Token';
$string['privacy:metadata:use:timecreated'] = 'Zeitpunkt der Frage';
$string['privacy:metadata:use:userid'] = 'Die fragende Person';
$string['privacyheading'] = 'Datenschutz';
$string['privacyheading_desc'] = 'Studierende entscheiden selbst, ob und wie lange ihre Chats gespeichert werden (Standard: nicht gespeichert); Administratoren können Chats nicht lesen. Der Dienst erhält Frage, Chat und Kursmaterialien, aber nie Name, E-Mail-Adresse oder Moodle-ID.';
$string['proposal_after'] = 'Vorschlag';
$string['proposal_applied'] = 'In den Kurs übernommen.';
$string['proposal_apply'] = 'Übernehmen';
$string['proposal_before'] = 'Aktuell';
$string['proposal_discard'] = 'Ablehnen';
$string['proposal_discarded'] = 'Vorschlag abgelehnt.';
$string['proposal_error_activity'] = 'Die Aktivität {$a} existiert in diesem Kurs nicht.';
$string['proposal_error_invalid'] = 'Der Assistent hat einen unvollständigen Vorschlag gesendet. Bitte fragen Sie erneut.';
$string['proposal_error_nochange'] = 'Der Vorschlag für „{$a}“ ändert nichts.';
$string['proposal_error_nopermission'] = 'Sie dürfen diese Änderung nicht vornehmen.';
$string['proposal_error_notincluded'] = 'Der Assistent konnte die Texte von „{$a}“ nicht vollständig sehen und kann sie daher hier nicht ändern. Öffnen Sie diese Aktivität und fragen Sie dort.';
$string['proposal_error_section'] = 'Der Abschnitt {$a} existiert in diesem Kurs nicht.';
$string['proposal_error_toomany'] = 'Nur die ersten {$a} Vorschläge einer Antwort wurden behalten.';
$string['proposal_fillform'] = 'Ins Formular eintragen';
$string['proposal_formhint'] = 'Diese Aktivität hat Einstellungen, die nur ihr Bearbeitungsformular richtig speichert: Öffnen Sie sie im Formular, prüfen Sie und speichern Sie.';
$string['proposal_missing'] = 'Die Aktivität oder der Abschnitt existiert nicht mehr.';
$string['proposal_newlabel'] = 'Neuer Text- und Medienbereich';
$string['proposal_newpage'] = 'Neue Seite: {$a}';
$string['proposal_openform'] = 'Im Formular öffnen';
$string['proposal_status_applied'] = 'Übernommen';
$string['proposal_status_discarded'] = 'Abgelehnt';
$string['proposal_status_pending'] = 'Wartet auf Ihre Entscheidung';
$string['proposal_status_undone'] = 'Rückgängig gemacht';
$string['proposal_undo'] = 'Rückgängig';
$string['proposal_undo_confirm_new'] = 'Die von diesem Vorschlag erstellte Aktivität löschen? Sie kommt in den Papierkorb, wenn dieser aktiviert ist.';
$string['proposal_undo_confirm_update'] = 'Die Texte von vor dieser Änderung wiederherstellen?';
$string['proposal_undone'] = 'Die Änderung wurde rückgängig gemacht.';
$string['proposal_updateactivity'] = '{$a->type}: {$a->name}';
$string['proposal_updatesection'] = 'Abschnitt: {$a}';
$string['proposal_view'] = 'Ansehen';
$string['proposal_where'] = 'In: {$a}';
$string['provider'] = 'Dienst';
$string['provider_anthropic'] = 'Claude (Anthropic)';
$string['provider_desc'] = 'Welcher KI-Dienst die Fragen beantwortet. Jeder Dienst behält seinen eigenen Schlüssel, sodass Sie jederzeit wechseln können.';
$string['provider_gemini'] = 'Google Gemini';
$string['provider_openai'] = 'OpenAI';
$string['provider_openaicompatible'] = 'OpenAI-kompatibler Dienst (eigene Adresse)';
$string['quizlock'] = 'Während Tests pausieren';
$string['quizlock_desc'] = 'Solange Studierende einen nicht abgeschlossenen Testversuch im Kurs haben, antwortet der Assistent nicht.';
$string['reason_disabled'] = 'Der KI-Assistent ist ausgeschaltet.';
$string['reason_euonly'] = 'Der KI-Assistent ist nicht verfügbar: Die Website erlaubt nur Dienste, die Daten in der EU verarbeiten.';
$string['reason_nocapability'] = 'Sie dürfen den KI-Assistenten in diesem Kurs nicht verwenden.';
$string['reason_notconfigured'] = 'Der KI-Assistent ist noch nicht eingerichtet.';
$string['reason_quiz'] = 'Der KI-Assistent pausiert, solange Sie einen nicht abgeschlossenen Testversuch in diesem Kurs haben.';
$string['reasoningeffort'] = 'Denkaufwand';
$string['reasoningeffort_default'] = 'Standard des Modells';
$string['reasoningeffort_desc'] = 'Wie viel das Modell vor der Antwort nachdenkt: Mehr Aufwand ergibt sorgfältigere, aber langsamere und teurere Antworten. Wird nur an Modelle gesendet, die ihn unterstützen (GPT-5 und neuer, o-Serie; Claude Opus 4.5+, Sonnet 4.6+; Gemini). „Keiner“ behält den Standard des Modells, wo sich das Nachdenken nicht abschalten lässt.';
$string['reasoningeffort_high'] = 'Hoch';
$string['reasoningeffort_low'] = 'Niedrig (für Chats empfohlen)';
$string['reasoningeffort_medium'] = 'Mittel';
$string['reasoningeffort_none'] = 'Keiner';
$string['retention_days'] = '{$a} Tage';
$string['retention_desc'] = 'Das entscheiden nur Sie. Ein kürzerer Zeitraum löscht ältere Chats sofort. Mit „Nicht speichern“ bleibt der Chat nur in diesem Browser-Tab.';
$string['retention_forever'] = 'Bis ich sie lösche';
$string['retention_notsaved'] = 'Nicht speichern';
$string['retention_oneday'] = '1 Tag';
$string['retention_title'] = 'Meine Chats aufbewahren';
$string['retentionsaved'] = 'Ihre Auswahl wurde gespeichert.';
$string['retentionstatus'] = 'Chats: {$a}';
$string['retry'] = 'Erneut versuchen';
$string['send'] = 'Senden';
$string['settings'] = 'Einstellungen';
$string['status_checked'] = 'geprüft {$a}';
$string['status_disabled'] = 'Der Assistent ist ausgeschaltet: Studierende sehen ihn noch nicht.';
$string['status_error'] = 'Keine Verbindung:';
$string['status_euwarning'] = '„Nur EU“ ist aktiviert, aber diese Verbindung garantiert keine Verarbeitung in der EU: Studierende können den Assistenten nicht nutzen.';
$string['status_freetier'] = 'Dieser Gemini-Schlüssel nutzt das kostenlose Kontingent von Google: Pro Modell sind nur wenige Fragen pro Minute erlaubt, und Google kann die Fragen zur Verbesserung seiner Produkte nutzen. Aktivieren Sie die Abrechnung für das Google-Cloud-Projekt des Schlüssels, bevor Studierende den Assistenten nutzen.';
$string['status_lasterror'] = 'Zuletzt unbeantwortete Frage ({$a->time}, {$a->model}): {$a->error}';
$string['status_lastfallback'] = '{$a->time}: {$a->from} war überlastet, daher hat {$a->to} geantwortet.';
$string['status_modelchanged'] = 'Das Modell „{$a->old}“ ist bei diesem Dienst nicht verfügbar, daher wurde „{$a->new}“ gewählt. Sie können unten im Menü „Modell“ ein anderes wählen.';
$string['status_modelmissing'] = 'Dieser Schlüssel kann das Modell „{$a}“ nicht nutzen. Wählen Sie unten im Menü „Modell“ ein anderes und speichern Sie.';
$string['status_models'] = '{$a} Chat-Modelle verfügbar';
$string['status_nokey'] = 'Noch kein API-Schlüssel. Fügen Sie unten einen ein und speichern Sie.';
$string['status_ok'] = 'Verbunden: {$a->provider}, Modell {$a->model}';
$string['status_pending'] = 'Die Verbindung wird geprüft, wenn diese Einstellungsseite geöffnet wird.';
$string['status_switched'] = 'Der eingefügte Schlüssel gehört zu {$a}: Er wurde für diesen Dienst gespeichert, der jetzt aktiv ist.';
$string['status_usage'] = 'Letzte 24 Stunden: {$a->dayquestions} Fragen. Letzte 7 Tage: {$a->weekquestions} Fragen von {$a->weekusers} Personen, {$a->weektokens} Token.';
$string['stop'] = 'Stopp';
$string['stopped'] = 'Angehalten.';
$string['suggestion_concepts'] = 'Nenne die wichtigsten Begriffe';
$string['suggestion_page'] = 'Erkläre diese Seite einfach';
$string['suggestion_quiz'] = 'Stelle mir 3 Fragen zum Selbsttest';
$string['suggestion_summary'] = 'Fasse diesen Kurs zusammen';
$string['task_cleanup'] = 'Abgelaufene Chats und Vorschläge des KI-Assistenten löschen';
$string['teacher_emptytitle'] = 'Woran sollen wir in diesem Kurs arbeiten?';
$string['teacher_placeholder'] = 'Bitten Sie um eine neue Seite, eine Überarbeitung, eine Übersetzung…';
$string['teacher_suggestion_examples'] = 'Ergänze diese Seite um Beispiele und eine kurze Zusammenfassung';
$string['teacher_suggestion_newpage'] = 'Entwirf eine Seite für das nächste Thema des Kurses';
$string['teacher_suggestion_review'] = 'Prüfe die Struktur des Kurses und schlage Verbesserungen vor';
$string['teacher_suggestion_simplify'] = 'Mache diesen Text klarer und leichter lesbar';
$string['teacher_suggestion_summaries'] = 'Schreibe kurze Beschreibungen für die Abschnitte';
$string['teacher_suggestion_translate'] = 'Mache diesen Text mehrsprachig: Englisch, Türkisch, Deutsch und Kroatisch';
$string['teachermode'] = 'Lehrendenmodus';
$string['testconnection'] = 'Verbindung jetzt prüfen';
$string['thinking'] = 'Denkt nach…';
$string['toggle'] = 'KI-Assistent';
$string['truncated'] = 'Die Antwort wurde abgeschnitten, weil sie zu lang war.';
$string['undo_error_changed'] = 'Der Inhalt wurde nach dem Übernehmen des Vorschlags geändert und kann daher nicht automatisch rückgängig gemacht werden.';
$string['userhourlylimit'] = 'Fragen pro Person und Stunde';
$string['userhourlylimit_desc'] = 'Schützt vor hohen Kosten. 0 bedeutet kein Limit.';
$string['writingproposal'] = 'Vorschlag wird geschrieben…';
