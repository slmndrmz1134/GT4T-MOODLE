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
 * Theme DIVERSE - Spanish language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE es el tema de la plataforma de aprendizaje de la DIVERSE European University: un tema hijo de Boost Union con la paleta de colores y tipografía de DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'Configuración del tema DIVERSE (tema hijo de Boost Union).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Herencia';
$string['inheritanceinherit'] = 'Heredar';
$string['inheritanceduplicate'] = 'Duplicar';
$string['inheritanceoptionsexplanation'] = 'En la mayoría de los casos, la herencia funciona perfectamente. Si encuentra problemas con las funciones de Boost Union, cambie esta configuración a \'Duplicar\' e informe del problema en GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Herencia Pre-SCSS';
$string['prescssinheritancesetting_desc'] = 'Esta configuración controla si el código Pre-SCSS de Boost Union debe heredarse o duplicarse.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Herencia Extra-SCSS';
$string['extrascssinheritancesetting_desc'] = 'Esta configuración controla si el código Extra-SCSS de Boost Union debe heredarse o duplicarse.';
// ... Partner login pages.
$string['partnerlogin'] = 'Páginas de acceso de los socios';
$string['partnerlogin_desc'] = 'El panel junto al formulario de acceso muestra una foto, un nombre y una frase: los de DIVERSE mientras no se elige ningún socio, y los de una universidad socia cuando un visitante la elige en el menú "Select Partner". Sin foto se muestra el patrón de DIVERSE. El logotipo del socio sustituye al de DIVERSE en el formulario; si no hay, se usa el logotipo que la universidad subió para sus usuarios (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Todavía no hay universidades socias en la página de acceso.';
$string['loginphoto'] = 'Foto de acceso: {$a}';
$string['loginphoto_desc'] = 'Una foto horizontal de al menos 1600 píxeles de ancho. Ocupa el panel junto al formulario de acceso; la parte inferior se oscurece bajo el nombre de la universidad.';
$string['loginlogo'] = 'Logotipo de acceso: {$a}';
$string['loginlogo_desc'] = 'Se muestra en la parte superior del formulario de acceso cuando se elige esta universidad. Funciona mejor un logotipo horizontal con fondo transparente o blanco (PNG o SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Acerca de';
$string['nav_partners'] = 'Socios';
$string['nav_courses'] = 'Cursos';
$string['landing_eyebrow'] = 'Alianza Universitaria Europea';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovación, educación y sostenibilidad para un futuro resiliente.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) reúne a universidades, innovadores y empresas para impulsar la transformación verde y digital de Europa.';
$string['landing_getstarted'] = 'Comenzar';
$string['landing_meetpartners'] = 'Conoce a los socios';
$string['landing_keyfigures'] = 'Cifras clave';
$string['stat_projectpartners'] = 'Socios del proyecto';
$string['stat_countries'] = 'Países';
$string['stat_courses'] = 'Cursos disponibles';
$string['about_eyebrow'] = 'Acerca de GT4T';
$string['about_title'] = 'Del conocimiento a soluciones prácticas';
$string['about_intro'] = 'GT4T refuerza el papel de las universidades en la transición verde y digital conectando la educación superior con la industria y los ecosistemas de innovación. Su misión es convertir ideas y conocimiento en soluciones prácticas para una Europa más sostenible, inclusiva y preparada para el futuro.';
$string['about_support'] = 'Con el apoyo de la EIT HEI Initiative, orientado y cofinanciado por EIT Climate-KIC y coordinado por Satakunta University of Applied Sciences (SAMK, Finlandia).';
$string['focus_item1_title'] = 'Economía circular';
$string['focus_item1_desc'] = 'Producción más limpia, materiales sostenibles y uso responsable de los recursos.';
$string['focus_item2_title'] = 'Transformación digital';
$string['focus_item2_desc'] = 'Competencias en inteligencia artificial, análisis de datos, automatización y fabricación inteligente.';
$string['focus_item3_title'] = 'Transición energética';
$string['focus_item3_desc'] = 'Innovación y emprendimiento que apoyan el paso de Europa a la energía limpia.';
$string['partners_eyebrow'] = 'Socios';
$string['partners_title'] = 'Quién participa en GT4T';
$string['partners_intro'] = 'Universidades, centros de innovación y empresas de toda Europa trabajan juntos en el proyecto.';
$string['partners_lead'] = 'Socio coordinador';
$string['courses_title'] = 'Explorar cursos';
$string['courses_all'] = 'Todos los {$a} cursos';
$string['footer_about'] = 'Alianza Universitaria Europea DIVERSE: innovación, educación y sostenibilidad para un futuro resiliente.';
$string['footer_explore'] = 'Explorar';
$string['footer_copyright'] = '© {$a} Alianza Universitaria Europea DIVERSE';
$string['footer_platform'] = 'Plataforma';
$string['footer_privacy'] = 'Resumen de privacidad';

// Login page brand panel.
$string['login_intro'] = 'Formación conjunta y proyectos de innovación prácticos de nuestras universidades asociadas, en un solo lugar.';
$string['login_partner_intro'] = 'Accede con tu cuenta de {$a}.';
$string['login_partner_member'] = 'Miembro de la Alianza Universitaria Europea DIVERSE';
$string['signup_intro'] = 'Crea tu cuenta y únete a los cursos compartidos de las universidades asociadas de DIVERSE.';
$string['signup_partner_intro'] = 'Crea tu cuenta de {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Navegación principal';
$string['messages_select'] = 'Seleccione una conversación para leerla aquí.';
$string['mycourses_all'] = 'Todos';
$string['mycourses_notstarted'] = 'Sin empezar';
$string['mycourses_inprogress'] = 'En curso';
$string['mycourses_completed'] = 'Completados';
$string['mycourses_favourites'] = 'Favoritos';
$string['mycourses_none'] = 'Aún no hay nada aquí.';
$string['card_favourite'] = 'Añadir {$a} a favoritos';
$string['card_unfavourite'] = 'Quitar {$a} de favoritos';
$string['dash_kpi_courses'] = 'Cursos matriculados';
$string['dash_kpi_completed'] = 'Completados';
$string['dash_kpi_due'] = 'Para esta semana';
$string['dash_kpi_progress'] = 'Progreso medio';
$string['dash_activity'] = 'Tu actividad esta semana';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Aún no hay actividad esta semana.';
$string['dash_hero_next'] = 'Siguiente: {$a}';
$string['steps_eyebrow'] = 'Cómo funciona';
$string['steps_title'] = 'Empieza a aprender en tres pasos';
$string['step1_title'] = 'Elige tu universidad';
$string['step1_desc'] = 'En la página de acceso, elige tu universidad socia o GT4T.';
$string['step2_title'] = 'Accede con tu cuenta';
$string['step2_desc'] = 'Usa la cuenta que te dio tu universidad o crea una nueva.';
$string['step3_title'] = 'Continúa donde lo dejaste';
$string['step3_desc'] = 'Tu panel muestra tu siguiente actividad, tus plazos y tus calificaciones.';
$string['cta_title'] = '¿Listo para aprender?';
$string['cta_text'] = 'Cursos de {$a->partners} socios de {$a->countries} países, en un solo lugar.';
$string['nav_discover'] = 'Descubrir';
$string['nav_search'] = '¿Qué quieres aprender?';
$string['dash_resume'] = 'Sigue aprendiendo';
$string['dash_recommended'] = 'Más cursos para ti';
$string['dash_allcategories'] = 'Todos los cursos';
$string['card_activities'] = 'Actividades: {$a}';
$string['card_people'] = 'Personas: {$a}';
$string['card_enrolled'] = 'Matriculado';
$string['card_open'] = 'Matrícula abierta';
$string['lesson_previous'] = 'Anterior';
$string['lesson_next'] = 'Siguiente';
$string['shell_mypartner'] = 'Mi socio';
$string['shell_siteadmin'] = 'Administración del sitio';
$string['dash_hello'] = 'Bienvenido de nuevo, {$a}';
$string['dash_summary_overdue'] = 'Tareas atrasadas: {$a}. Empieza por aquí.';
$string['dash_summary_grading'] = 'Entregas pendientes de tu calificación: {$a}.';
$string['dash_summary_todo'] = 'Vence en los próximos 30 días: {$a}.';
$string['dash_summary_clear'] = 'No hay nada pendiente por ahora.';
$string['dash_lastvisit'] = 'última visita hace {$a}';
$string['dash_progress'] = '{$a} % completado';
$string['dash_todo'] = 'Por hacer';
$string['dash_todo_empty'] = 'No hay nada que hacer por ahora.';
$string['dash_overdue'] = 'Atrasado';
$string['dash_thisweek'] = 'Esta semana';
$string['dash_later'] = 'Más adelante';
$string['dash_allwork'] = 'Ver los {$a} elementos';
$string['dash_recentgrades'] = 'Calificaciones recientes';
$string['dash_allgrades'] = 'Todas las calificaciones';
$string['dash_nogrades'] = 'Tus calificaciones y comentarios aparecerán aquí.';
$string['dash_allcourses'] = 'Todos los cursos';
$string['dash_nocourses'] = 'Aún no estás matriculado en ningún curso.';
$string['dash_teaching'] = 'Mi docencia';
$string['dash_needsgrading'] = 'Pendiente de calificar';
$string['dash_course_students'] = '{$a} estudiantes';
$string['dash_stat_teaching'] = 'Cursos que impartes';
$string['dash_stat_students'] = 'Estudiantes';
$string['dash_stat_waiting'] = 'Entregas por calificar';
$string['dash_site'] = 'Resumen del sitio';
$string['dash_stat_users'] = 'Usuarios activos';
$string['dash_stat_recent'] = 'Activos en los últimos 7 días';
$string['dash_stat_courses'] = 'Cursos visibles';
$string['dash_stat_partners'] = 'Socios';
$string['dash_stat_pending'] = 'Cuentas pendientes de aprobación';
$string['dash_partners'] = 'Socios';
$string['dash_partner'] = 'Socio';
$string['dash_actions'] = 'Tareas habituales';
$string['dash_browseusers'] = 'Ver usuarios';
$string['dash_managecourses'] = 'Gestionar cursos';
$string['dash_logs'] = 'Registros del sitio';
$string['dash_system'] = 'Sistema';
$string['dash_cron_ran'] = 'Las tareas programadas se ejecutaron hace {$a}';
$string['dash_cron_never'] = 'Las tareas programadas aún no se han ejecutado';
$string['dash_pending_review'] = 'Revisar cuentas pendientes de aprobación';
$string['hero_stats'] = '{$a->sections} secciones, {$a->activities} actividades';
$string['hero_continue'] = 'Continuar';
$string['hero_start'] = 'Empezar';

// Privacy API.
$string['privacy:metadata'] = 'El tema DIVERSE no almacena datos personales de ningún usuario.';
