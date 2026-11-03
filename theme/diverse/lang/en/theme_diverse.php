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
 * Theme DIVERSE - Language pack
 *
 * @package    theme_diverse
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as it is perfectly well ordered, just not alphabetically.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// General.
$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE is the theme of the DIVERSE European University learning platform: a Boost Union child theme with the DIVERSE colour palette and typography.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE theme settings (Boost Union child theme).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in DIVERSE as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';
// ... Partner login pages.
$string['partnerlogin'] = 'Partner login pages';
$string['partnerlogin_desc'] = 'The panel next to the login form shows a photo, a name and one sentence: DIVERSE\'s while no partner is chosen, and a partner university\'s when a visitor chooses it in the "Select Partner" menu. Without a photo the DIVERSE pattern is shown. A partner\'s logo replaces the DIVERSE logo on the login form; without one, the logo the university uploaded for its users (tenant > Appearance > Logos) is used.';
$string['partnerlogin_none'] = 'No partner universities are listed on the login page yet.';
$string['loginphoto'] = 'Login photo: {$a}';
$string['loginphoto_desc'] = 'A landscape photo, at least 1600 pixels wide. It fills the panel next to the login form; the bottom part is darkened under the university\'s name.';
$string['loginlogo'] = 'Login logo: {$a}';
$string['loginlogo_desc'] = 'Shown at the top of the login form when this university is chosen. A wide logo on a transparent or white background (PNG or SVG) works best.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Landing page (site home for visitors).
$string['nav_about'] = 'About';
$string['nav_partners'] = 'Partners';
$string['nav_courses'] = 'Courses';
$string['landing_eyebrow'] = 'European University Alliance';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, education and sustainability for a resilient future.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) brings universities, innovators and businesses together to drive Europe\'s green and digital transformation.';
$string['landing_getstarted'] = 'Get started';
$string['landing_meetpartners'] = 'Meet the partners';
$string['landing_keyfigures'] = 'Key figures';
$string['stat_projectpartners'] = 'Project partners';
$string['stat_countries'] = 'Countries';
$string['stat_courses'] = 'Courses available';
$string['about_eyebrow'] = 'About GT4T';
$string['about_title'] = 'From knowledge to practical solutions';
$string['about_intro'] = 'GT4T strengthens the role of universities in the green and digital transition by connecting higher education with industry and innovation ecosystems. Its mission is to turn ideas and knowledge into practical solutions for a more sustainable, inclusive and future-ready Europe.';
$string['about_support'] = 'Supported by the EIT HEI Initiative, guided and co-funded by EIT Climate-KIC, and coordinated by Satakunta University of Applied Sciences (SAMK, Finland).';
$string['focus_item1_title'] = 'Circular economy';
$string['focus_item1_desc'] = 'Cleaner production, sustainable materials and responsible use of resources.';
$string['focus_item2_title'] = 'Digital transformation';
$string['focus_item2_desc'] = 'Skills in artificial intelligence, data analytics, automation and smart manufacturing.';
$string['focus_item3_title'] = 'Energy transition';
$string['focus_item3_desc'] = 'Innovation and entrepreneurship that support Europe\'s move to clean energy.';
$string['partners_eyebrow'] = 'Partners';
$string['partners_title'] = 'Who takes part in GT4T';
$string['partners_intro'] = 'Universities, innovation hubs and companies from across Europe work together in the project.';
$string['partners_lead'] = 'Lead partner';
$string['courses_title'] = 'Explore courses';
$string['courses_all'] = 'All {$a} courses';
$string['footer_about'] = 'The DIVERSE European University Alliance: innovation, education and sustainability for a resilient future.';
$string['footer_explore'] = 'Explore';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Privacy summary';

// Login page brand panel.
$string['login_intro'] = 'Joint training and hands-on innovation projects from our partner universities, in one place.';
$string['login_partner_intro'] = 'Log in with your {$a} account.';
$string['login_partner_member'] = 'Member of the DIVERSE European University Alliance';
$string['signup_intro'] = 'Create your account and join the shared courses of the DIVERSE partner universities.';
$string['signup_partner_intro'] = 'Create your {$a} account.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Main navigation';
$string['messages_select'] = 'Select a conversation to read it here.';
$string['mycourses_all'] = 'All';
$string['mycourses_notstarted'] = 'Not started';
$string['mycourses_inprogress'] = 'In progress';
$string['mycourses_completed'] = 'Completed';
$string['mycourses_favourites'] = 'Favourites';
$string['mycourses_none'] = 'Nothing here yet.';
$string['card_favourite'] = 'Add {$a} to favourites';
$string['card_unfavourite'] = 'Remove {$a} from favourites';
$string['dash_kpi_courses'] = 'Enrolled courses';
$string['dash_kpi_completed'] = 'Completed';
$string['dash_kpi_due'] = 'Due this week';
$string['dash_kpi_progress'] = 'Average progress';
$string['dash_activity'] = 'Your activity this week';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'No activity yet this week.';
$string['dash_hero_next'] = 'Next: {$a}';
$string['steps_eyebrow'] = 'How it works';
$string['steps_title'] = 'Start learning in three steps';
$string['step1_title'] = 'Choose your university';
$string['step1_desc'] = 'On the login page, pick your partner university or GT4T.';
$string['step2_title'] = 'Log in with your account';
$string['step2_desc'] = 'Use the account your university gave you, or create one.';
$string['step3_title'] = 'Continue where you left off';
$string['step3_desc'] = 'Your dashboard shows your next activity, your deadlines and your grades.';
$string['cta_title'] = 'Ready to start learning?';
$string['cta_text'] = 'Courses from {$a->partners} partners in {$a->countries} countries, in one place.';
$string['nav_discover'] = 'Discover';
$string['nav_guide'] = 'Platform guide';
$string['nav_search'] = 'What do you want to learn?';
$string['dash_resume'] = 'Keep learning';
$string['dash_recommended'] = 'More courses for you';
$string['dash_allcategories'] = 'All courses';
$string['card_activities'] = 'Activities: {$a}';
$string['card_people'] = 'People: {$a}';
$string['card_enrolled'] = 'Enrolled';
$string['card_open'] = 'Open enrolment';
$string['lesson_previous'] = 'Previous';
$string['lesson_next'] = 'Next';
$string['shell_mypartner'] = 'My partner';
$string['shell_siteadmin'] = 'Site admin';
$string['dash_hello'] = 'Welcome back, {$a}';
$string['dash_summary_overdue'] = 'Overdue items: {$a}. Start there.';
$string['dash_summary_grading'] = 'Submissions waiting for your grade: {$a}.';
$string['dash_summary_todo'] = 'Due in the next 30 days: {$a}.';
$string['dash_summary_clear'] = 'Nothing is due at the moment.';
$string['dash_lastvisit'] = 'last visit {$a} ago';
$string['dash_progress'] = '{$a}% complete';
$string['dash_todo'] = 'To do';
$string['dash_todo_empty'] = 'Nothing to do right now.';
$string['dash_overdue'] = 'Overdue';
$string['dash_thisweek'] = 'This week';
$string['dash_later'] = 'Later';
$string['dash_allwork'] = 'See all {$a} items';
$string['dash_recentgrades'] = 'Recent grades';
$string['dash_allgrades'] = 'All grades';
$string['dash_nogrades'] = 'Your grades and feedback will appear here.';
$string['dash_allcourses'] = 'All courses';
$string['dash_nocourses'] = 'You are not enrolled in any courses yet.';
$string['dash_teaching'] = 'Teaching';
$string['dash_needsgrading'] = 'Waiting to be graded';
$string['dash_course_students'] = '{$a} students';
$string['dash_stat_teaching'] = 'Courses you teach';
$string['dash_stat_students'] = 'Students';
$string['dash_stat_waiting'] = 'Submissions to grade';
$string['dash_site'] = 'Site overview';
$string['dash_stat_users'] = 'Active users';
$string['dash_stat_recent'] = 'Seen in the last 7 days';
$string['dash_stat_courses'] = 'Visible courses';
$string['dash_stat_partners'] = 'Partners';
$string['dash_stat_pending'] = 'Accounts awaiting approval';
$string['dash_partners'] = 'Partners';
$string['dash_partner'] = 'Partner';
$string['dash_actions'] = 'Common tasks';
$string['dash_browseusers'] = 'Browse users';
$string['dash_managecourses'] = 'Manage courses';
$string['dash_logs'] = 'Site logs';
$string['dash_system'] = 'System';
$string['dash_cron_ran'] = 'Scheduled tasks ran {$a} ago';
$string['dash_cron_never'] = 'Scheduled tasks have not run yet';
$string['dash_pending_review'] = 'Review accounts awaiting approval';
$string['hero_stats'] = '{$a->sections} sections, {$a->activities} activities';
$string['hero_continue'] = 'Continue';
$string['hero_start'] = 'Start';

// Dashboard cards.
$string['dash_news_label'] = 'What\'s new';
$string['dash_news_title'] = 'Platform guide: how DIVERSE works';
$string['dash_news_text'] = 'Short videos on finding courses, joining live classes and asking the course assistant.';
$string['dash_news_action'] = 'Watch now';
$string['dash_progress_title'] = 'Learning progress';
$string['dash_progress_help'] = 'Activities with completion tracking in your courses';
$string['dash_progress_total'] = 'Total activities';
$string['dash_progress_done'] = 'completed';
$string['dash_state_completed'] = 'Completed';
$string['dash_state_started'] = 'In progress';
$string['dash_state_overdue'] = 'Overdue';
$string['dash_state_notstarted'] = 'Not started';
$string['dash_week_help'] = 'Activities you completed each day';
$string['dash_week_activities'] = 'Activities';
$string['dash_week_more'] = '{$a} more activities than last week';
$string['dash_week_fewer'] = '{$a} fewer activities than last week';
$string['dash_week_same'] = 'As many activities as last week';
$string['dash_deadlines'] = 'Deadlines';
$string['dash_due_in'] = 'Due in {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'No';
$string['dash_col_progress'] = 'Progress';
$string['dash_col_next'] = 'Next';
$string['dash_col_teacher'] = 'Teacher';
$string['dash_notvisited'] = 'not visited yet';
$string['dash_goal_title'] = 'Weekly goal';
$string['dash_goal_help'] = 'How many activities you want to finish each week';
$string['dash_goal_change'] = 'Change goal';
$string['dash_goal_choice'] = '{$a} a week';
$string['dash_goal_this'] = 'Activities this week';
$string['dash_goal_reached'] = 'Goal reached!';
$string['dash_goal_keep'] = 'Keep it up!';
$string['dash_goal_left'] = '{$a} to go';
$string['dash_goal_weeks'] = '{$a} weeks';
$string['dash_goal_total'] = 'Completed in total';
$string['dash_goal_average'] = 'Weekly average';
$string['dash_goal_streak'] = 'Streak';
$string['dash_nothing_to_grade'] = 'Nothing is waiting to be graded.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Open';

// My courses: search, order and view.
$string['mycourses_search'] = 'Search my courses';
$string['mycourses_nomatch'] = 'No course matches your search.';
$string['mycourses_lastvisited'] = 'Last visited';
$string['mycourses_view'] = 'View';
$string['mycourses_cards'] = 'Cards';
$string['mycourses_list'] = 'List';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'Up next';
$string['course_aside'] = 'This course';
$string['course_live_next'] = 'Next live class';
$string['course_live_room'] = 'Live classroom';
$string['course_live_none'] = 'No live class is planned yet.';
$string['course_live_join'] = 'Join the live class';
$string['course_nodeadlines'] = 'Nothing is due in this course.';
$string['course_assistant'] = 'Course assistant';
$string['course_assistant_text'] = 'Ask about the readings, the deadlines or a word you do not know.';
$string['course_assistant_open'] = 'Ask the assistant';
$string['course_teachers'] = 'Teachers';
$string['course_message'] = 'Send a message to {$a}';

// Colour palette (user menu).
$string['palette_togreen'] = 'Green colours (GT4T)';
$string['palette_toorange'] = 'Orange colours (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'The DIVERSE theme does not store any personal data about any user.';
$string['privacy:metadata:preference:palette'] = 'The colour palette the user chose for the platform.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'The weekly goal the user chose on the dashboard: how many activities to finish each week.';
