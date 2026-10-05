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
$string['partnerlogin_desc'] = 'Lorsqu\'un visiteur choisit une université partenaire dans le menu "Select Partner" de la page de connexion, le panneau à côté du formulaire affiche la photo, le logo et le nom de cette université au lieu du panneau DIVERSE. Le logo est celui que chaque université dépose pour ses utilisateurs (tenant > Appearance > Logos). Sans photo, le panneau affiche le motif DIVERSE avec le nom de l\'université.';
$string['partnerlogin_none'] = 'Aucune université partenaire n\'est encore proposée sur la page de connexion.';
$string['loginphoto'] = 'Photo de connexion : {$a}';
$string['loginphoto_desc'] = 'Une photo au format paysage, d\'au moins 1600 pixels de large. Elle remplit le panneau à côté du formulaire de connexion ; le bas est assombri sous le nom de l\'université.';

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
$string['stat_partners'] = 'Universités partenaires';
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
$string['footer_platform'] = 'Plateforme';
$string['footer_privacy'] = 'Résumé de confidentialité';

// Login page brand panel.
$string['login_tagline'] = 'Apprenez avec des partenaires au-delà des frontières';
$string['login_intro'] = 'Formations conjointes et projets d\'innovation pratiques de nos universités partenaires, en un seul endroit.';
$string['login_partner_intro'] = 'Connectez-vous avec votre compte {$a}.';
$string['login_partner_member'] = 'Membre de l\'Alliance universitaire européenne DIVERSE';

// Privacy API.
$string['privacy:metadata'] = 'Le thème DIVERSE ne stocke aucune donnée personnelle sur les utilisateurs.';
