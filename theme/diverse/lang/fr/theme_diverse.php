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
 * Theme DIVERSE - French language pack (used for Belgian French / fr)
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE est le thème de la plateforme d\'apprentissage de la DIVERSE European University : un thème enfant de Boost Union avec la palette de couleurs et la typographie de DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'Paramètres du thème DIVERSE (thème enfant de Boost Union).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Héritage';
$string['inheritanceinherit'] = 'Hériter';
$string['inheritanceduplicate'] = 'Dupliquer';
$string['inheritanceoptionsexplanation'] = 'Dans la plupart des cas, l\'héritage fonctionne parfaitement. Si vous rencontrez des problèmes avec des fonctionnalités de Boost Union, basculez ce paramètre sur \'Dupliquer\' et signalez le problème sur GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Héritage Pre-SCSS';
$string['prescssinheritancesetting_desc'] = 'Ce paramètre contrôle si le code Pre-SCSS de Boost Union doit être hérité ou dupliqué.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Héritage Extra-SCSS';
$string['extrascssinheritancesetting_desc'] = 'Ce paramètre contrôle si le code Extra-SCSS de Boost Union doit être hérité ou dupliqué.';
// ... Partner login pages.
$string['partnerlogin'] = 'Pages de connexion des partenaires';
$string['partnerlogin_desc'] = 'Le panneau à côté du formulaire de connexion affiche une photo, un nom et une phrase : ceux de DIVERSE tant qu\'aucun partenaire n\'est choisi, et ceux d\'une université partenaire quand un visiteur la choisit dans le menu "Select Partner". Sans photo, le motif DIVERSE est affiché. Le logo du partenaire remplace celui de DIVERSE dans le formulaire ; à défaut, le logo que l\'université a déposé pour ses utilisateurs (tenant > Appearance > Logos) est utilisé.';
$string['partnerlogin_none'] = 'Aucune université partenaire n\'est encore proposée sur la page de connexion.';
$string['loginphoto'] = 'Photo de connexion : {$a}';
$string['loginphoto_desc'] = 'Une photo au format paysage, d\'au moins 1600 pixels de large. Elle remplit le panneau à côté du formulaire de connexion ; le bas est assombri sous le nom de l\'université.';
$string['loginlogo'] = 'Logo de connexion : {$a}';
$string['loginlogo_desc'] = 'Affiché en haut du formulaire de connexion quand cette université est choisie. Un logo horizontal sur fond transparent ou blanc (PNG ou SVG) convient le mieux.';

// Landing page (site home for visitors).
$string['nav_about'] = 'À propos';
$string['nav_partners'] = 'Partenaires';
$string['nav_courses'] = 'Cours';
$string['landing_eyebrow'] = 'Alliance des universités européennes';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, éducation et durabilité pour un avenir résilient.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) réunit universités, innovateurs et entreprises pour accompagner la transformation verte et numérique de l\'Europe.';
$string['landing_getstarted'] = 'Commencer';
$string['landing_meetpartners'] = 'Découvrir les partenaires';
$string['landing_keyfigures'] = 'Chiffres clés';
$string['stat_projectpartners'] = 'Partenaires du projet';
$string['stat_countries'] = 'Pays';
$string['stat_courses'] = 'Cours disponibles';
$string['about_eyebrow'] = 'À propos de GT4T';
$string['about_title'] = 'Du savoir aux solutions concrètes';
$string['about_intro'] = 'GT4T renforce le rôle des universités dans la transition verte et numérique en reliant l\'enseignement supérieur à l\'industrie et aux écosystèmes d\'innovation. Sa mission : transformer les idées et les connaissances en solutions concrètes pour une Europe plus durable, inclusive et tournée vers l\'avenir.';
$string['about_support'] = 'Soutenu par l\'EIT HEI Initiative, accompagné et cofinancé par EIT Climate-KIC, et coordonné par Satakunta University of Applied Sciences (SAMK, Finlande).';
$string['focus_item1_title'] = 'Économie circulaire';
$string['focus_item1_desc'] = 'Production plus propre, matériaux durables et utilisation responsable des ressources.';
$string['focus_item2_title'] = 'Transformation numérique';
$string['focus_item2_desc'] = 'Compétences en intelligence artificielle, analyse de données, automatisation et fabrication intelligente.';
$string['focus_item3_title'] = 'Transition énergétique';
$string['focus_item3_desc'] = 'Innovation et entrepreneuriat au service du passage de l\'Europe à une énergie propre.';
$string['partners_eyebrow'] = 'Partenaires';
$string['partners_title'] = 'Qui participe à GT4T';
$string['partners_intro'] = 'Universités, pôles d\'innovation et entreprises de toute l\'Europe travaillent ensemble dans le projet.';
$string['partners_lead'] = 'Partenaire coordinateur';
$string['courses_title'] = 'Explorer les cours';
$string['courses_all'] = 'Tous les {$a} cours';
$string['footer_about'] = 'Alliance des universités européennes DIVERSE : innovation, éducation et durabilité pour un avenir résilient.';
$string['footer_explore'] = 'Découvrir';
$string['footer_copyright'] = '© {$a} Alliance universitaire européenne DIVERSE';
$string['footer_platform'] = 'Plateforme';
$string['footer_privacy'] = 'Résumé de confidentialité';

// Login page brand panel.
$string['login_intro'] = 'Formations conjointes et projets d\'innovation pratiques de nos universités partenaires, en un seul endroit.';
$string['login_partner_intro'] = 'Connectez-vous avec votre compte {$a}.';
$string['login_partner_member'] = 'Membre de l\'Alliance universitaire européenne DIVERSE';
$string['signup_intro'] = 'Créez votre compte et rejoignez les cours communs des universités partenaires de DIVERSE.';
$string['signup_partner_intro'] = 'Créez votre compte {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Navigation principale';
$string['messages_select'] = 'Sélectionnez une conversation pour la lire ici.';
$string['mycourses_all'] = 'Tous';
$string['mycourses_notstarted'] = 'Non commencés';
$string['mycourses_inprogress'] = 'En cours';
$string['mycourses_completed'] = 'Terminés';
$string['mycourses_favourites'] = 'Favoris';
$string['mycourses_none'] = 'Rien ici pour le moment.';
$string['card_favourite'] = 'Ajouter {$a} aux favoris';
$string['card_unfavourite'] = 'Retirer {$a} des favoris';
$string['dash_kpi_courses'] = 'Cours suivis';
$string['dash_kpi_completed'] = 'Terminés';
$string['dash_kpi_due'] = 'À rendre cette semaine';
$string['dash_kpi_progress'] = 'Progression moyenne';
$string['dash_activity'] = 'Votre activité cette semaine';
$string['dash_activity_day'] = '{$a->day} : {$a->count}';
$string['dash_activity_none'] = 'Aucune activité cette semaine.';
$string['dash_hero_next'] = 'Suivant : {$a}';
$string['steps_eyebrow'] = 'Comment ça marche';
$string['steps_title'] = 'Commencez à apprendre en trois étapes';
$string['step1_title'] = 'Choisissez votre université';
$string['step1_desc'] = 'Sur la page de connexion, choisissez votre université partenaire ou GT4T.';
$string['step2_title'] = 'Connectez-vous avec votre compte';
$string['step2_desc'] = 'Utilisez le compte fourni par votre université ou créez-en un.';
$string['step3_title'] = 'Reprenez là où vous vous étiez arrêté';
$string['step3_desc'] = 'Votre tableau de bord affiche votre prochaine activité, vos échéances et vos notes.';
$string['cta_title'] = 'Prêt à apprendre ?';
$string['cta_text'] = 'Des cours de {$a->partners} partenaires dans {$a->countries} pays, au même endroit.';
$string['nav_discover'] = 'Découvrir';
$string['nav_guide'] = 'Guide de la plateforme';
$string['nav_search'] = 'Que voulez-vous apprendre ?';
$string['dash_resume'] = 'Continuez à apprendre';
$string['dash_recommended'] = 'Plus de cours pour vous';
$string['dash_allcategories'] = 'Tous les cours';
$string['card_activities'] = 'Activités : {$a}';
$string['card_people'] = 'Personnes : {$a}';
$string['card_enrolled'] = 'Inscrit';
$string['card_open'] = 'Inscription ouverte';
$string['lesson_previous'] = 'Précédent';
$string['lesson_next'] = 'Suivant';
$string['shell_mypartner'] = 'Mon partenaire';
$string['shell_siteadmin'] = 'Administration du site';
$string['dash_hello'] = 'Bon retour, {$a}';
$string['dash_summary_overdue'] = 'En retard : {$a}. Commencez ici.';
$string['dash_summary_grading'] = 'Devoirs en attente de votre note : {$a}.';
$string['dash_summary_todo'] = 'À rendre dans les 30 prochains jours : {$a}.';
$string['dash_summary_clear'] = 'Rien à rendre pour le moment.';
$string['dash_lastvisit'] = 'dernière visite il y a {$a}';
$string['dash_progress'] = '{$a} % terminé';
$string['dash_todo'] = 'À faire';
$string['dash_todo_empty'] = 'Rien à faire pour le moment.';
$string['dash_overdue'] = 'En retard';
$string['dash_thisweek'] = 'Cette semaine';
$string['dash_later'] = 'Plus tard';
$string['dash_allwork'] = 'Voir les {$a} éléments';
$string['dash_recentgrades'] = 'Notes récentes';
$string['dash_allgrades'] = 'Toutes les notes';
$string['dash_nogrades'] = 'Vos notes et commentaires apparaîtront ici.';
$string['dash_allcourses'] = 'Tous les cours';
$string['dash_nocourses'] = 'Vous n\'êtes encore inscrit à aucun cours.';
$string['dash_teaching'] = 'Mon enseignement';
$string['dash_needsgrading'] = 'En attente de notation';
$string['dash_course_students'] = '{$a} étudiants';
$string['dash_stat_teaching'] = 'Cours que vous enseignez';
$string['dash_stat_students'] = 'Étudiants';
$string['dash_stat_waiting'] = 'Devoirs à noter';
$string['dash_site'] = 'Aperçu du site';
$string['dash_stat_users'] = 'Utilisateurs actifs';
$string['dash_stat_recent'] = 'Actifs ces 7 derniers jours';
$string['dash_stat_courses'] = 'Cours visibles';
$string['dash_stat_partners'] = 'Partenaires';
$string['dash_stat_pending'] = 'Comptes en attente d\'approbation';
$string['dash_partners'] = 'Partenaires';
$string['dash_partner'] = 'Partenaire';
$string['dash_actions'] = 'Tâches courantes';
$string['dash_browseusers'] = 'Parcourir les utilisateurs';
$string['dash_managecourses'] = 'Gérer les cours';
$string['dash_logs'] = 'Journaux du site';
$string['dash_system'] = 'Système';
$string['dash_cron_ran'] = 'Les tâches planifiées ont été exécutées il y a {$a}';
$string['dash_cron_never'] = 'Les tâches planifiées n\'ont pas encore été exécutées';
$string['dash_pending_review'] = 'Examiner les comptes en attente d\'approbation';
$string['hero_stats'] = '{$a->sections} sections, {$a->activities} activités';
$string['hero_continue'] = 'Continuer';
$string['hero_start'] = 'Commencer';

// Dashboard cards.
$string['dash_news_label'] = 'Nouveautés';
$string['dash_news_title'] = 'Guide de la plateforme : comment fonctionne DIVERSE';
$string['dash_news_text'] = 'De courtes vidéos pour trouver des cours, rejoindre les classes en direct et interroger l\'assistant.';
$string['dash_news_action'] = 'Regarder';
$string['dash_progress_title'] = 'Progression';
$string['dash_progress_help'] = 'Activités avec suivi d\'achèvement dans vos cours';
$string['dash_progress_total'] = 'Activités au total';
$string['dash_progress_done'] = 'terminé';
$string['dash_state_completed'] = 'Terminées';
$string['dash_state_started'] = 'En cours';
$string['dash_state_overdue'] = 'En retard';
$string['dash_state_notstarted'] = 'Non commencées';
$string['dash_week_help'] = 'Activités terminées chaque jour';
$string['dash_week_activities'] = 'Activités';
$string['dash_week_more'] = '{$a} activités de plus que la semaine dernière';
$string['dash_week_fewer'] = '{$a} activités de moins que la semaine dernière';
$string['dash_week_same'] = 'Autant d\'activités que la semaine dernière';
$string['dash_deadlines'] = 'Échéances';
$string['dash_due_in'] = 'Dans {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'N°';
$string['dash_col_progress'] = 'Progression';
$string['dash_col_next'] = 'Ensuite';
$string['dash_col_teacher'] = 'Enseignant·e';
$string['dash_notvisited'] = 'pas encore visité';
$string['dash_goal_title'] = 'Objectif de la semaine';
$string['dash_goal_help'] = 'Le nombre d\'activités que vous voulez terminer chaque semaine';
$string['dash_goal_change'] = 'Modifier l\'objectif';
$string['dash_goal_choice'] = '{$a} par semaine';
$string['dash_goal_this'] = 'Activités cette semaine';
$string['dash_goal_reached'] = 'Objectif atteint !';
$string['dash_goal_keep'] = 'Continuez !';
$string['dash_goal_left'] = 'encore {$a}';
$string['dash_goal_weeks'] = '{$a} semaines';
$string['dash_goal_total'] = 'Terminées au total';
$string['dash_goal_average'] = 'Moyenne par semaine';
$string['dash_goal_streak'] = 'Série';
$string['dash_nothing_to_grade'] = 'Rien n\'attend d\'être évalué.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Ouvrir';

// My courses: search, order and view.
$string['mycourses_search'] = 'Rechercher dans mes cours';
$string['mycourses_nomatch'] = 'Aucun cours ne correspond à votre recherche.';
$string['mycourses_lastvisited'] = 'Dernière visite';
$string['mycourses_view'] = 'Affichage';
$string['mycourses_cards'] = 'Cartes';
$string['mycourses_list'] = 'Liste';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'À suivre';
$string['course_aside'] = 'Ce cours';
$string['course_live_next'] = 'Prochaine classe en direct';
$string['course_live_room'] = 'Salle en direct';
$string['course_live_none'] = 'Aucune classe en direct n\'est encore prévue.';
$string['course_live_join'] = 'Rejoindre la classe';
$string['course_nodeadlines'] = 'Rien n\'est à rendre dans ce cours.';
$string['course_assistant'] = 'Assistant du cours';
$string['course_assistant_text'] = 'Posez une question sur les lectures, les échéances ou un mot inconnu.';
$string['course_assistant_open'] = 'Demander à l\'assistant';
$string['course_teachers'] = 'Enseignants';
$string['course_message'] = 'Envoyer un message à {$a}';

// Colour palette (user menu).
$string['palette_togreen'] = 'Couleurs vertes (GT4T)';
$string['palette_toorange'] = 'Couleurs orange (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'Le thème DIVERSE ne stocke aucune donnée personnelle sur les utilisateurs.';
$string['privacy:metadata:preference:palette'] = 'La palette de couleurs choisie par l\'utilisateur pour la plateforme.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'L\'objectif hebdomadaire choisi sur le tableau de bord : le nombre d\'activités à terminer chaque semaine.';
