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
 * Theme DIVERSE - Portuguese language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE é o tema da plataforma de aprendizagem da DIVERSE European University: um tema filho do Boost Union com a paleta de cores e tipografia do DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'Configurações do tema DIVERSE (tema filho do Boost Union).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Herança';
$string['inheritanceinherit'] = 'Herdar';
$string['inheritanceduplicate'] = 'Duplicar';
$string['inheritanceoptionsexplanation'] = 'Na maioria dos casos, a herança funciona perfeitamente. Se encontrar problemas com funcionalidades do Boost Union, mude esta configuração para \'Duplicar\' e reporte o problema no GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Herança de Pre-SCSS';
$string['prescssinheritancesetting_desc'] = 'Esta configuração controla se o código Pre-SCSS do Boost Union deve ser herdado ou duplicado.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Herança de Extra-SCSS';
$string['extrascssinheritancesetting_desc'] = 'Esta configuração controla se o código Extra-SCSS do Boost Union deve ser herdado ou duplicado.';
// ... Partner login pages.
$string['partnerlogin'] = 'Páginas de acesso dos parceiros';
$string['partnerlogin_desc'] = 'O painel ao lado do formulário de acesso mostra uma fotografia, um nome e uma frase: os da DIVERSE enquanto nenhum parceiro está escolhido, e os de uma universidade parceira quando um visitante a escolhe no menu "Select Partner". Sem fotografia, é mostrado o padrão DIVERSE. O logótipo do parceiro substitui o da DIVERSE no formulário; se não existir, é usado o logótipo que a universidade carregou para os seus utilizadores (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Ainda não há universidades parceiras na página de acesso.';
$string['loginphoto'] = 'Fotografia de acesso: {$a}';
$string['loginphoto_desc'] = 'Uma fotografia horizontal com pelo menos 1600 píxeis de largura. Preenche o painel ao lado do formulário de acesso; a parte inferior é escurecida sob o nome da universidade.';
$string['loginlogo'] = 'Logótipo de acesso: {$a}';
$string['loginlogo_desc'] = 'Mostrado no topo do formulário de acesso quando esta universidade é escolhida. Funciona melhor um logótipo horizontal com fundo transparente ou branco (PNG ou SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Sobre';
$string['nav_partners'] = 'Parceiros';
$string['nav_courses'] = 'Cursos';
$string['landing_eyebrow'] = 'Aliança de Universidades Europeias';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Inovação, educação e sustentabilidade para um futuro resiliente.';
$string['landing_intro'] = 'O GreenTech4Transformation (GT4T) reúne universidades, inovadores e empresas para impulsionar a transformação verde e digital da Europa.';
$string['landing_getstarted'] = 'Começar';
$string['landing_meetpartners'] = 'Conheça os parceiros';
$string['landing_keyfigures'] = 'Números-chave';
$string['stat_projectpartners'] = 'Parceiros do projeto';
$string['stat_countries'] = 'Países';
$string['stat_courses'] = 'Cursos disponíveis';
$string['about_eyebrow'] = 'Sobre o GT4T';
$string['about_title'] = 'Do conhecimento a soluções práticas';
$string['about_intro'] = 'O GT4T reforça o papel das universidades na transição verde e digital, ligando o ensino superior à indústria e aos ecossistemas de inovação. A sua missão é transformar ideias e conhecimento em soluções práticas para uma Europa mais sustentável, inclusiva e preparada para o futuro.';
$string['about_support'] = 'Apoiado pela EIT HEI Initiative, orientado e cofinanciado pela EIT Climate-KIC e coordenado pela Satakunta University of Applied Sciences (SAMK, Finlândia).';
$string['focus_item1_title'] = 'Economia circular';
$string['focus_item1_desc'] = 'Produção mais limpa, materiais sustentáveis e utilização responsável dos recursos.';
$string['focus_item2_title'] = 'Transformação digital';
$string['focus_item2_desc'] = 'Competências em inteligência artificial, análise de dados, automação e fabrico inteligente.';
$string['focus_item3_title'] = 'Transição energética';
$string['focus_item3_desc'] = 'Inovação e empreendedorismo que apoiam a passagem da Europa para a energia limpa.';
$string['partners_eyebrow'] = 'Parceiros';
$string['partners_title'] = 'Quem participa no GT4T';
$string['partners_intro'] = 'Universidades, centros de inovação e empresas de toda a Europa trabalham em conjunto no projeto.';
$string['partners_lead'] = 'Parceiro coordenador';
$string['courses_title'] = 'Explorar cursos';
$string['courses_all'] = 'Todos os {$a} cursos';
$string['footer_about'] = 'Aliança de Universidades Europeias DIVERSE: inovação, educação e sustentabilidade para um futuro resiliente.';
$string['footer_explore'] = 'Explorar';
$string['footer_copyright'] = '© {$a} Aliança Universitária Europeia DIVERSE';
$string['footer_platform'] = 'Plataforma';
$string['footer_privacy'] = 'Resumo de privacidade';

// Login page brand panel.
$string['login_intro'] = 'Formações conjuntas e projetos de inovação práticos das nossas universidades parceiras, num único lugar.';
$string['login_partner_intro'] = 'Inicie sessão com a sua conta {$a}.';
$string['login_partner_member'] = 'Membro da Aliança Universitária Europeia DIVERSE';
$string['signup_intro'] = 'Crie a sua conta e participe nos cursos partilhados das universidades parceiras da DIVERSE.';
$string['signup_partner_intro'] = 'Crie a sua conta {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Navegação principal';
$string['messages_select'] = 'Selecione uma conversa para a ler aqui.';
$string['mycourses_all'] = 'Todos';
$string['mycourses_notstarted'] = 'Não iniciados';
$string['mycourses_inprogress'] = 'Em curso';
$string['mycourses_completed'] = 'Concluídos';
$string['mycourses_favourites'] = 'Favoritos';
$string['mycourses_none'] = 'Ainda não há nada aqui.';
$string['card_favourite'] = 'Adicionar {$a} aos favoritos';
$string['card_unfavourite'] = 'Remover {$a} dos favoritos';
$string['dash_kpi_courses'] = 'Cursos inscritos';
$string['dash_kpi_completed'] = 'Concluídos';
$string['dash_kpi_due'] = 'Para entregar esta semana';
$string['dash_kpi_progress'] = 'Progresso médio';
$string['dash_activity'] = 'A sua atividade esta semana';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Ainda não há atividade esta semana.';
$string['dash_hero_next'] = 'Seguinte: {$a}';
$string['steps_eyebrow'] = 'Como funciona';
$string['steps_title'] = 'Comece a aprender em três passos';
$string['step1_title'] = 'Escolha a sua universidade';
$string['step1_desc'] = 'Na página de acesso, escolha a sua universidade parceira ou GT4T.';
$string['step2_title'] = 'Entre com a sua conta';
$string['step2_desc'] = 'Use a conta que a sua universidade lhe deu ou crie uma nova.';
$string['step3_title'] = 'Continue onde parou';
$string['step3_desc'] = 'O seu painel mostra a próxima atividade, os prazos e as classificações.';
$string['cta_title'] = 'Pronto para aprender?';
$string['cta_text'] = 'Cursos de {$a->partners} parceiros em {$a->countries} países, num só lugar.';
$string['nav_discover'] = 'Descobrir';
$string['nav_guide'] = 'Guia da plataforma';
$string['nav_search'] = 'O que quer aprender?';
$string['dash_resume'] = 'Continue a aprender';
$string['dash_recommended'] = 'Mais cursos para si';
$string['dash_allcategories'] = 'Todos os cursos';
$string['card_activities'] = 'Atividades: {$a}';
$string['card_people'] = 'Pessoas: {$a}';
$string['card_enrolled'] = 'Inscrito';
$string['card_open'] = 'Inscrição aberta';
$string['lesson_previous'] = 'Anterior';
$string['lesson_next'] = 'Seguinte';
$string['shell_mypartner'] = 'O meu parceiro';
$string['shell_siteadmin'] = 'Administração do site';
$string['dash_hello'] = 'Bem-vindo de volta, {$a}';
$string['dash_summary_overdue'] = 'Em atraso: {$a}. Comece por aqui.';
$string['dash_summary_grading'] = 'Submissões à espera da sua nota: {$a}.';
$string['dash_summary_todo'] = 'Prazo nos próximos 30 dias: {$a}.';
$string['dash_summary_clear'] = 'Não há nada pendente de momento.';
$string['dash_lastvisit'] = 'última visita há {$a}';
$string['dash_progress'] = '{$a}% concluído';
$string['dash_todo'] = 'A fazer';
$string['dash_todo_empty'] = 'Não há nada para fazer de momento.';
$string['dash_overdue'] = 'Em atraso';
$string['dash_thisweek'] = 'Esta semana';
$string['dash_later'] = 'Mais tarde';
$string['dash_allwork'] = 'Ver todos os {$a} itens';
$string['dash_recentgrades'] = 'Notas recentes';
$string['dash_allgrades'] = 'Todas as notas';
$string['dash_nogrades'] = 'As suas notas e comentários aparecerão aqui.';
$string['dash_allcourses'] = 'Todos os cursos';
$string['dash_nocourses'] = 'Ainda não está inscrito em nenhum curso.';
$string['dash_teaching'] = 'O meu ensino';
$string['dash_needsgrading'] = 'À espera de avaliação';
$string['dash_course_students'] = '{$a} estudantes';
$string['dash_stat_teaching'] = 'Cursos que leciona';
$string['dash_stat_students'] = 'Estudantes';
$string['dash_stat_waiting'] = 'Submissões por avaliar';
$string['dash_site'] = 'Resumo do site';
$string['dash_stat_users'] = 'Utilizadores ativos';
$string['dash_stat_recent'] = 'Ativos nos últimos 7 dias';
$string['dash_stat_courses'] = 'Cursos visíveis';
$string['dash_stat_partners'] = 'Parceiros';
$string['dash_stat_pending'] = 'Contas à espera de aprovação';
$string['dash_partners'] = 'Parceiros';
$string['dash_partner'] = 'Parceiro';
$string['dash_actions'] = 'Tarefas frequentes';
$string['dash_browseusers'] = 'Ver utilizadores';
$string['dash_managecourses'] = 'Gerir cursos';
$string['dash_logs'] = 'Registos do site';
$string['dash_system'] = 'Sistema';
$string['dash_cron_ran'] = 'As tarefas agendadas foram executadas há {$a}';
$string['dash_cron_never'] = 'As tarefas agendadas ainda não foram executadas';
$string['dash_pending_review'] = 'Rever contas à espera de aprovação';
$string['hero_stats'] = '{$a->sections} secções, {$a->activities} atividades';
$string['hero_continue'] = 'Continuar';
$string['hero_start'] = 'Começar';

// Dashboard cards.
$string['dash_news_label'] = 'Novidades';
$string['dash_news_title'] = 'Guia da plataforma: como funciona o DIVERSE';
$string['dash_news_text'] = 'Vídeos curtos sobre encontrar cursos, participar em aulas ao vivo e perguntar ao assistente.';
$string['dash_news_action'] = 'Ver agora';
$string['dash_progress_title'] = 'Progresso de aprendizagem';
$string['dash_progress_help'] = 'Atividades com acompanhamento de conclusão nos teus cursos';
$string['dash_progress_total'] = 'Total de atividades';
$string['dash_progress_done'] = 'concluído';
$string['dash_state_completed'] = 'Concluídas';
$string['dash_state_started'] = 'Em curso';
$string['dash_state_overdue'] = 'Em atraso';
$string['dash_state_notstarted'] = 'Por começar';
$string['dash_week_help'] = 'Atividades que concluíste em cada dia';
$string['dash_week_activities'] = 'Atividades';
$string['dash_week_more'] = 'Mais {$a} atividades do que na semana passada';
$string['dash_week_fewer'] = 'Menos {$a} atividades do que na semana passada';
$string['dash_week_same'] = 'Tantas atividades como na semana passada';
$string['dash_deadlines'] = 'Prazos';
$string['dash_due_in'] = 'Prazo em {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'N.º';
$string['dash_col_progress'] = 'Progresso';
$string['dash_col_next'] = 'A seguir';
$string['dash_col_teacher'] = 'Professor';
$string['dash_notvisited'] = 'ainda não visitado';
$string['dash_goal_title'] = 'Objetivo semanal';
$string['dash_goal_help'] = 'Quantas atividades queres concluir por semana';
$string['dash_goal_change'] = 'Alterar objetivo';
$string['dash_goal_choice'] = '{$a} por semana';
$string['dash_goal_this'] = 'Atividades esta semana';
$string['dash_goal_reached'] = 'Objetivo atingido!';
$string['dash_goal_keep'] = 'Continua!';
$string['dash_goal_left'] = 'faltam {$a}';
$string['dash_goal_weeks'] = '{$a} semanas';
$string['dash_goal_total'] = 'Concluídas no total';
$string['dash_goal_average'] = 'Média semanal';
$string['dash_goal_streak'] = 'Sequência';
$string['dash_nothing_to_grade'] = 'Não há nada à espera de avaliação.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Abrir';

// My courses: search, order and view.
$string['mycourses_search'] = 'Pesquisar os meus cursos';
$string['mycourses_nomatch'] = 'Nenhum curso corresponde à pesquisa.';
$string['mycourses_lastvisited'] = 'Última visita';
$string['mycourses_view'] = 'Vista';
$string['mycourses_cards'] = 'Cartões';
$string['mycourses_list'] = 'Lista';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'A seguir';
$string['course_aside'] = 'Este curso';
$string['course_live_next'] = 'Próxima aula ao vivo';
$string['course_live_room'] = 'Sala ao vivo';
$string['course_live_none'] = 'Ainda não há aulas ao vivo previstas.';
$string['course_live_join'] = 'Entrar na aula';
$string['course_nodeadlines'] = 'Não há prazos neste curso.';
$string['course_assistant'] = 'Assistente do curso';
$string['course_assistant_text'] = 'Pergunta sobre as leituras, os prazos ou uma palavra que não conheças.';
$string['course_assistant_open'] = 'Perguntar ao assistente';
$string['course_teachers'] = 'Professores';
$string['course_message'] = 'Enviar mensagem a {$a}';

// Colour palette: the switch in the top bar.
$string['palette'] = 'Cores';
$string['palette_orange'] = 'Laranja';
$string['palette_green'] = 'Verde';

// My courses: the line under the title.
$string['mycourses_summary'] = '{$a->courses} cursos · {$a->inprogress} em curso';

// Dashboard cards: time, row menu, overdue button.
$string['dash_goal_timeweek'] = 'Tempo esta semana';
$string['dash_goal_time'] = '{$a->h} h {$a->m} min';
$string['dash_opencourse'] = 'Abrir curso';
$string['dash_submit'] = 'Entregar';

// Course page: progress of a section.
$string['course_section_done'] = '{$a->done} de {$a->total} concluídas';

// Login page: the way back to the public site.
$string['login_back'] = 'Voltar ao início';

// Colour palette (user menu).
$string['palette_togreen'] = 'Cores verdes (GT4T)';
$string['palette_toorange'] = 'Cores laranja (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'O tema DIVERSE não armazena dados pessoais sobre nenhum utilizador.';
$string['privacy:metadata:preference:palette'] = 'A paleta de cores que o utilizador escolheu para a plataforma.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'O objetivo semanal escolhido no painel: quantas atividades concluir por semana.';
