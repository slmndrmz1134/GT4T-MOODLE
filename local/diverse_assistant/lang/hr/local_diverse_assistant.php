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
 * DIVERSE AI assistant - Language strings (Croatian)
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'API ključ';
$string['apikey_desc_anthropic'] = 'Na console.anthropic.com > API keys (počinje sa sk-ant-).';
$string['apikey_desc_gemini'] = 'Na aistudio.google.com > Get API key (počinje sa AIza). Koristite ključ projekta s uključenim plaćanjem: u besplatnoj razini Google može koristiti pitanja za poboljšanje svojih proizvoda.';
$string['apikey_desc_openai'] = 'Na platform.openai.com > API keys (počinje sa sk-; potrebno je postaviti plaćanje). ChatGPT pretplata nije API ključ.';
$string['apikey_desc_openaicompatible'] = 'Ključ servisa na API adresi ispod.';
$string['apikey_placeholder_anthropic'] = 'sk-ant-...';
$string['apikey_placeholder_gemini'] = 'AIza...';
$string['apikey_placeholder_openai'] = 'sk-...';
$string['apikey_placeholder_openaicompatible'] = 'API ključ';
$string['apikey_placeholder_set'] = 'Ključ je spremljen. Zalijepite novi kako biste ga zamijenili.';
$string['apikey_remove'] = 'Ukloni spremljeni ključ';
$string['apikey_saved'] = 'Ključ je spremljen (šifrirano).';
$string['apply_error_changed'] = 'Tekst je promijenjen nakon što je asistent dao ovaj prijedlog, pa nije primijenjen. Ponovno pitajte asistenta kako bi radio s trenutačnom verzijom.';
$string['apply_error_formonly'] = 'Ova se aktivnost može promijeniti samo u svom obrascu za uređivanje. Upotrijebite „Otvori u obrascu”.';
$string['apply_error_missing'] = 'Aktivnost ili tema više ne postoji.';
$string['apply_error_status'] = 'Ovaj je prijedlog već obrađen.';
$string['back'] = 'Natrag';
$string['closepanel'] = 'Zatvori';
$string['connectionheading'] = 'AI servis';
$string['connectionheading_desc'] = 'Odaberite servis, zalijepite njegov API ključ i spremite. Ključevi se pohranjuju šifrirano i više se ne prikazuju; prazno polje ključa zadržava spremljeni ključ. Claude (sk-ant-…) ili Gemini (AIza…) ključ zalijepljen u bilo koje polje sam se prebacuje na taj servis. Nakon spremanja veza se provjerava (prikazano gore), a izbornik Model prikazuje modele koje ključ može koristiti.';
$string['delete'] = 'Izbriši';
$string['deleteconversation'] = 'Izbriši razgovor';
$string['deleteconversation_confirm'] = 'Izbrisati razgovor „{$a}”? Ovo se ne može poništiti.';
$string['deletehistory'] = 'Izbriši sve moje razgovore';
$string['deletehistory_confirm'] = 'Izbrisati sve Vaše spremljene razgovore u svim kolegijima? Ovo se ne može poništiti.';
$string['deletehistory_desc'] = 'Briše sve razgovore spremljene na poslužitelju, u svim kolegijima, i promjene koje Vam je asistent predložio.';
$string['diverse_assistant:teach'] = 'Korištenje AI asistenta za pisanje i uređivanje sadržaja kolegija';
$string['diverse_assistant:use'] = 'Korištenje AI asistenta za kolegij';
$string['emptytitle'] = 'Pitajte bilo što o ovom kolegiju.';
$string['enabled'] = 'Uključi asistenta';
$string['enabled_desc'] = 'Prikazuje karticu AI asistenta na stranicama kolegija svima koji ga smiju koristiti (prema zadanim postavkama studentima i nastavnicima; ovlast local/diverse_assistant:use). Nastavnici koji smiju uređivati kolegij dobivaju asistenta za izradu sadržaja, koji predlaže nove stranice i promjene (ovlast local/diverse_assistant:teach).';
$string['endpoint'] = 'API adresa';
$string['endpoint_desc'] = 'Adresa API-ja kompatibilnog s OpenAI-jem, uključujući verziju, npr. https://api.mistral.ai/v1. Lokalne adrese (npr. Ollama) moraju se dodatno dopustiti pod Administracija sustava > Općenito > Sigurnost > HTTP sigurnost.';
$string['endpointineu'] = 'Ovaj servis obrađuje podatke u EU';
$string['endpointineu_desc'] = 'Označite samo ako servis ugovorom jamči da se zahtjevi obrađuju u Europskoj uniji. Potrebno kada je uključeno „Samo EU”.';
$string['errorauth'] = 'AI servis je odbio API ključ.';
$string['errorbusy'] = 'AI servis je trenutno jako opterećen. Pokušajte ponovno za trenutak.';
$string['errorconnection'] = 'AI servis nije dostupan. Pokušajte ponovno kasnije.';
$string['errorempty'] = 'AI servis je poslao prazan odgovor. Pokušajte ponovno.';
$string['errorforbidden'] = 'AI servis je odbio zahtjev za ovaj API ključ.';
$string['errorgeneric'] = 'Nešto nije u redu. Pokušajte ponovno.';
$string['errorlimit'] = 'Dosegnuli ste ograničenje od {$a} pitanja na sat. Pokušajte ponovno kasnije.';
$string['errormodel'] = 'AI servis ne poznaje odabrani model ili adresu.';
$string['errornoendpoint'] = 'Za AI servis nije postavljena API adresa.';
$string['errornomessage'] = 'Napišite pitanje.';
$string['errornomodel'] = 'Za AI servis nije postavljen model.';
$string['errornotconfigured'] = 'AI servis još nije postavljen.';
$string['errornotfound'] = 'Ovaj razgovor više ne postoji.';
$string['errorratelimit'] = 'AI servis je zauzet ili je dosegnuto ograničenje korištenja. Pokušajte ponovno kasnije.';
$string['errorrefusal'] = 'AI je odbio odgovoriti na ovo pitanje. Postavite pitanje na drugi način.';
$string['errorservice'] = 'AI servis je javio pogrešku. Pokušajte ponovno kasnije.';
$string['errortoolong'] = 'Pitanje je predugo (najviše {$a} znakova).';
$string['euonly'] = 'Samo EU';
$string['euonly_desc'] = 'Ako je označeno, asistent radi samo s vezom koja jamči obradu u Europskoj uniji: OpenAI s adresom za pohranu podataka u EU ili kompatibilni servis za koji je potvrđeno da je u EU. Claude i Gemini API-ji nemaju opciju samo za EU. U suprotnom studenti vide da asistent nije dostupan.';
$string['field_content'] = 'Sadržaj';
$string['field_description'] = 'Opis';
$string['field_name'] = 'Naziv';
$string['field_sectionname'] = 'Naziv teme';
$string['field_summary'] = 'Sažetak teme';
$string['field_text'] = 'Tekst';
$string['footnote'] = 'AI može griješiti. Ne dijelite osobne podatke.';
$string['history'] = 'Spremljeni razgovori';
$string['historydeleted'] = 'Vaši spremljeni razgovori su izbrisani.';
$string['historyempty'] = 'U ovom kolegiju još nema spremljenih razgovora.';
$string['insertedineditor'] = 'Umetnuto u uređivač.';
$string['insertineditor'] = 'Umetni u uređivač';
$string['limitsheading'] = 'Ograničenja';
$string['maxcontextchars'] = 'Poslani materijali kolegija (znakova)';
$string['maxcontextchars_desc'] = 'Materijali kolegija šalju se uz svako pitanje. Duži materijali skraćuju se na ovu duljinu. 60.000 znakova je oko 15.000 tokena.';
$string['model'] = 'Model';
$string['model_desc'] = 'Nakon provjere veze izbornik prikazuje modele za razgovor ključa. Preporučeno: gpt-6-luna (OpenAI), claude-opus-5 (Claude), gemini-3.8-flash (Gemini).';
$string['model_recommended'] = '{$a} (preporučeno)';
$string['newchat'] = 'Novi razgovor';
$string['noeditor'] = 'Najprije kliknite u tekstno polje obrasca, a zatim umetnite.';
$string['notice_accept'] = 'Razumijem, počni';
$string['notice_body'] = 'Ovaj asistent koristi umjetnu inteligenciju ({$a}). Vaša pitanja i materijali ovog kolegija šalju se tom servisu kako bi se izradili odgovori. Vaše ime i adresa e-pošte ne šalju se.';
$string['notice_mistakes'] = 'Odgovori mogu biti netočni ili nepotpuni. Važne informacije provjerite u materijalima kolegija.';
$string['notice_personal'] = 'U pitanja ne upisujte osobne podatke (svoje ni tuđe).';
$string['notice_retention'] = 'Pod Postavkama sami odlučujete hoće li se Vaši razgovori spremati i koliko dugo. Prema zadanim postavkama ništa se ne sprema.';
$string['notice_teacher'] = 'Nastavnički način: asistent vidi tekstove ovog kolegija (i skrivene aktivnosti) i može predložiti nove stranice i promjene. U kolegiju se ništa ne mijenja dok ne primijenite prijedlog, a primijenjenu promjenu možete poništiti još {$a} dana.';
$string['notice_title'] = 'Prije početka';
$string['openairegion'] = 'Regija podataka';
$string['openairegion_desc'] = 'EU adresu koristite samo ako je OpenAI projekt ključa izrađen s europskom pohranom podataka.';
$string['openairegion_eu'] = 'Europska unija (eu.api.openai.com)';
$string['openairegion_global'] = 'Zadano (api.openai.com)';
$string['paneltitle'] = 'AI asistent';
$string['placeholder'] = 'Pitajte o ovom kolegiju…';
$string['pluginname'] = 'DIVERSE AI asistent';
$string['prefill_done'] = 'Prijedlog asistenta upisan je u obrazac. Provjerite ga i spremite.';
$string['prefill_failed'] = 'Prijedlog nije bilo moguće upisati u ovaj obrazac.';
$string['privacy:metadata:aiservice'] = 'Za odgovor se pitanje šalje AI servisu koji je odabrao administrator sustava, zajedno s ranijim porukama razgovora i materijalima kolegija koje korisnik može vidjeti. Ime, adresa e-pošte i Moodle ID korisnika ne šalju se.';
$string['privacy:metadata:aiservice:coursecontent'] = 'U nastavničkom načinu: spremljeni tekstovi kolegija (nazivi, opisi, stranice, sažeci tema), uključujući skrivene aktivnosti';
$string['privacy:metadata:aiservice:coursematerials'] = 'Materijali kolegija koje su napisali nastavnici, a korisnik ih može vidjeti';
$string['privacy:metadata:aiservice:history'] = 'Ranije poruke razgovora';
$string['privacy:metadata:aiservice:message'] = 'Pitanje';
$string['privacy:metadata:conv'] = 'Razgovori koje je korisnik odlučio spremiti; brišu se kada istekne razdoblje koje je korisnik odabrao.';
$string['privacy:metadata:conv:courseid'] = 'Kolegij na koji se razgovor odnosi';
$string['privacy:metadata:conv:timecreated'] = 'Početak razgovora';
$string['privacy:metadata:conv:timemodified'] = 'Vrijeme posljednje poruke';
$string['privacy:metadata:conv:title'] = 'Početak prvog pitanja';
$string['privacy:metadata:conv:userid'] = 'Korisnik kojem razgovor pripada';
$string['privacy:metadata:msg'] = 'Poruke spremljenih razgovora';
$string['privacy:metadata:msg:content'] = 'Tekst poruke';
$string['privacy:metadata:msg:role'] = 'Je li poruka pitanje ili odgovor';
$string['privacy:metadata:msg:timecreated'] = 'Vrijeme pisanja poruke';
$string['privacy:metadata:preference:notice'] = 'Vrijeme kada je korisnik pročitao obavijest prikazanu prije prvog razgovora';
$string['privacy:metadata:preference:retention'] = 'Koliko se dugo čuvaju razgovori korisnika';
$string['privacy:metadata:prop'] = 'Promjene koje je asistent predložio nastavniku; čuvaju se 30 dana nakon posljednje obrade kako bi se primijenjene promjene mogle poništiti.';
$string['privacy:metadata:prop:courseid'] = 'Kolegij';
$string['privacy:metadata:prop:original'] = 'Tekstovi kolegija prije promjene';
$string['privacy:metadata:prop:proposed'] = 'Predloženi tekstovi';
$string['privacy:metadata:prop:status'] = 'Je li prijedlog na čekanju, primijenjen, poništen ili odbijen';
$string['privacy:metadata:prop:timecreated'] = 'Kada je promjena predložena';
$string['privacy:metadata:prop:timemodified'] = 'Kada je prijedlog posljednji put obrađen';
$string['privacy:metadata:prop:userid'] = 'Nastavnik kojem je promjena predložena';
$string['privacy:metadata:use'] = 'Jedan redak po odgovorenom pitanju, bez teksta, za satno ograničenje i ukupnu upotrebu; čuva se 30 dana.';
$string['privacy:metadata:use:completiontokens'] = 'Duljina odgovora u tokenima';
$string['privacy:metadata:use:courseid'] = 'Kolegij';
$string['privacy:metadata:use:prompttokens'] = 'Duljina zahtjeva u tokenima';
$string['privacy:metadata:use:timecreated'] = 'Vrijeme postavljanja pitanja';
$string['privacy:metadata:use:userid'] = 'Korisnik koji je pitao';
$string['privacyheading'] = 'Zaštita podataka';
$string['privacyheading_desc'] = 'Studenti sami odlučuju hoće li se i koliko dugo spremati njihovi razgovori (zadano: ne sprema se); administratori ne mogu čitati razgovore. Servis prima pitanje, razgovor i materijale kolegija, ali nikada ime, adresu e-pošte ili Moodle ID studenta.';
$string['proposal_after'] = 'Prijedlog';
$string['proposal_applied'] = 'Primijenjeno na kolegij.';
$string['proposal_apply'] = 'Primijeni';
$string['proposal_before'] = 'Trenutačno';
$string['proposal_discard'] = 'Odbij';
$string['proposal_discarded'] = 'Prijedlog je odbijen.';
$string['proposal_error_activity'] = 'Aktivnost {$a} ne postoji u ovom kolegiju.';
$string['proposal_error_invalid'] = 'Asistent je poslao nepotpun prijedlog. Molimo pitajte ponovno.';
$string['proposal_error_nochange'] = 'Prijedlog za „{$a}” ništa ne mijenja.';
$string['proposal_error_nopermission'] = 'Nemate ovlast za ovu promjenu.';
$string['proposal_error_notincluded'] = 'Asistent nije mogao vidjeti cijele tekstove aktivnosti „{$a}”, pa ih ne može mijenjati odavde. Otvorite tu aktivnost i pitajte ondje.';
$string['proposal_error_section'] = 'Tema {$a} ne postoji u ovom kolegiju.';
$string['proposal_error_toomany'] = 'Zadržano je samo prvih {$a} prijedloga jednog odgovora.';
$string['proposal_fillform'] = 'Upiši u obrazac';
$string['proposal_formhint'] = 'Ova aktivnost ima postavke koje ispravno sprema samo njezin obrazac za uređivanje: otvorite je u obrascu, provjerite i spremite.';
$string['proposal_missing'] = 'Aktivnost ili tema više ne postoji.';
$string['proposal_newlabel'] = 'Novo područje za tekst i medije';
$string['proposal_newpage'] = 'Nova stranica: {$a}';
$string['proposal_openform'] = 'Otvori u obrascu';
$string['proposal_status_applied'] = 'Primijenjeno';
$string['proposal_status_discarded'] = 'Odbijeno';
$string['proposal_status_pending'] = 'Čeka Vašu odluku';
$string['proposal_status_undone'] = 'Poništeno';
$string['proposal_undo'] = 'Poništi';
$string['proposal_undo_confirm_new'] = 'Izbrisati aktivnost koju je stvorio ovaj prijedlog? Ako je koš za smeće uključen, ide u njega.';
$string['proposal_undo_confirm_update'] = 'Vratiti tekstove od prije ove promjene?';
$string['proposal_undone'] = 'Promjena je poništena.';
$string['proposal_updateactivity'] = '{$a->type}: {$a->name}';
$string['proposal_updatesection'] = 'Tema: {$a}';
$string['proposal_view'] = 'Prikaži';
$string['proposal_where'] = 'U: {$a}';
$string['provider'] = 'Servis';
$string['provider_anthropic'] = 'Claude (Anthropic)';
$string['provider_desc'] = 'Koji AI servis odgovara na pitanja. Svaki servis zadržava svoj ključ, pa se možete prebacivati kad god želite.';
$string['provider_gemini'] = 'Google Gemini';
$string['provider_openai'] = 'OpenAI';
$string['provider_openaicompatible'] = 'Servis kompatibilan s OpenAI-jem (vlastita adresa)';
$string['quizlock'] = 'Pauziraj tijekom testova';
$string['quizlock_desc'] = 'Dok student ima nezavršen pokušaj testa u kolegiju, asistent ne odgovara.';
$string['reason_disabled'] = 'AI asistent je isključen.';
$string['reason_euonly'] = 'AI asistent nije dostupan: sustav dopušta samo servise koji obrađuju podatke u EU.';
$string['reason_nocapability'] = 'Nemate dopuštenje koristiti AI asistenta u ovom kolegiju.';
$string['reason_notconfigured'] = 'AI asistent još nije postavljen.';
$string['reason_quiz'] = 'AI asistent je pauziran dok imate nezavršen pokušaj testa u ovom kolegiju.';
$string['reasoningeffort'] = 'Razina razmišljanja';
$string['reasoningeffort_default'] = 'Zadano za model';
$string['reasoningeffort_desc'] = 'Koliko model razmišlja prije odgovora: veća razina daje pažljivije, ali sporije i skuplje odgovore. Šalje se samo modelima koji to podržavaju (GPT-5 i noviji, serija o; Claude Opus 4.5+, Sonnet 4.6+; Gemini). „Bez” zadržava zadano ponašanje modela gdje se razmišljanje ne može isključiti.';
$string['reasoningeffort_high'] = 'Visoka';
$string['reasoningeffort_low'] = 'Niska (preporučeno za razgovor)';
$string['reasoningeffort_medium'] = 'Srednja';
$string['reasoningeffort_none'] = 'Bez';
$string['retention_days'] = '{$a} dana';
$string['retention_desc'] = 'O tome odlučujete samo Vi. Kraće razdoblje odmah briše starije razgovore. Uz „Ne spremaj” razgovor ostaje samo u ovoj kartici preglednika.';
$string['retention_forever'] = 'Dok ih ne izbrišem';
$string['retention_notsaved'] = 'Ne spremaj';
$string['retention_oneday'] = '1 dan';
$string['retention_title'] = 'Čuvaj moje razgovore';
$string['retentionsaved'] = 'Vaš je odabir spremljen.';
$string['retentionstatus'] = 'Razgovori: {$a}';
$string['retry'] = 'Pokušaj ponovno';
$string['send'] = 'Pošalji';
$string['settings'] = 'Postavke';
$string['status_checked'] = 'provjereno {$a}';
$string['status_disabled'] = 'Asistent je isključen: studenti ga još ne vide.';
$string['status_error'] = 'Nema veze:';
$string['status_euwarning'] = '„Samo EU” je uključeno, ali ova veza ne jamči obradu u EU: studenti ne mogu koristiti asistenta.';
$string['status_freetier'] = 'Ovaj Gemini ključ koristi Googleovu besplatnu razinu: po modelu je dopušteno samo nekoliko pitanja u minuti, a Google može koristiti pitanja za poboljšanje svojih proizvoda. Uključite plaćanje za Google Cloud projekt ključa prije nego što ga studenti počnu koristiti.';
$string['status_lasterror'] = 'Posljednje neodgovoreno pitanje ({$a->time}, {$a->model}): {$a->error}';
$string['status_lastfallback'] = '{$a->time}: {$a->from} je bio preopterećen, pa je odgovorio {$a->to}.';
$string['status_modelchanged'] = 'Model „{$a->old}” nije dostupan kod ovog servisa, pa je odabran „{$a->new}”. Drugi možete odabrati u izborniku Model ispod.';
$string['status_modelmissing'] = 'Ovaj ključ ne može koristiti model „{$a}”. Odaberite drugi u izborniku Model ispod i spremite.';
$string['status_models'] = 'Dostupno modela za razgovor: {$a}';
$string['status_nokey'] = 'Još nema API ključa. Zalijepite ga ispod i spremite.';
$string['status_ok'] = 'Povezano: {$a->provider}, model {$a->model}';
$string['status_pending'] = 'Veza se provjerava kada se otvori ova stranica postavki.';
$string['status_switched'] = 'Zalijepljeni ključ pripada servisu {$a}: spremljen je za taj servis, koji je sada aktivan.';
$string['status_usage'] = 'Posljednja 24 sata: {$a->dayquestions} pitanja. Posljednjih 7 dana: {$a->weekquestions} pitanja od {$a->weekusers} korisnika, {$a->weektokens} tokena.';
$string['stop'] = 'Zaustavi';
$string['stopped'] = 'Zaustavljeno.';
$string['suggestion_concepts'] = 'Navedi ključne pojmove';
$string['suggestion_page'] = 'Objasni ovu stranicu jednostavno';
$string['suggestion_quiz'] = 'Postavi mi 3 pitanja za samoprovjeru';
$string['suggestion_summary'] = 'Sažmi ovaj kolegij';
$string['task_cleanup'] = 'Brisanje isteklih razgovora i prijedloga AI asistenta';
$string['teacher_emptytitle'] = 'Na čemu ćemo raditi u ovom kolegiju?';
$string['teacher_placeholder'] = 'Zatražite novu stranicu, preradu, prijevod…';
$string['teacher_suggestion_examples'] = 'Dodaj ovoj stranici primjere i kratak sažetak';
$string['teacher_suggestion_newpage'] = 'Pripremi nacrt stranice za sljedeću temu kolegija';
$string['teacher_suggestion_review'] = 'Pregledaj strukturu kolegija i predloži poboljšanja';
$string['teacher_suggestion_simplify'] = 'Učini ovaj tekst jasnijim i čitljivijim';
$string['teacher_suggestion_summaries'] = 'Napiši kratke sažetke tema';
$string['teacher_suggestion_translate'] = 'Učini ovaj tekst višejezičnim: engleski, turski, njemački i hrvatski';
$string['teachermode'] = 'Nastavnički način';
$string['testconnection'] = 'Provjeri vezu sada';
$string['thinking'] = 'Razmišlja…';
$string['toggle'] = 'AI asistent';
$string['truncated'] = 'Odgovor je skraćen jer je bio predug.';
$string['undo_error_changed'] = 'Sadržaj je promijenjen nakon primjene prijedloga, pa se ne može automatski poništiti.';
$string['userhourlylimit'] = 'Pitanja po korisniku na sat';
$string['userhourlylimit_desc'] = 'Štiti od visokih troškova. 0 znači bez ograničenja.';
$string['writingproposal'] = 'Pišem prijedlog…';
