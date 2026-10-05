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
$string['partnerlogin_desc'] = 'Quando um visitante escolhe uma universidade parceira no menu "Select Partner" da página de acesso, o painel ao lado do formulário mostra a fotografia, o logótipo e o nome dessa universidade em vez do painel DIVERSE. O logótipo é o que cada universidade carrega para os seus utilizadores (tenant > Appearance > Logos). Sem fotografia, o painel mostra o padrão DIVERSE com o nome da universidade.';
$string['partnerlogin_none'] = 'Ainda não há universidades parceiras na página de acesso.';
$string['loginphoto'] = 'Fotografia de acesso: {$a}';
$string['loginphoto_desc'] = 'Uma fotografia horizontal com pelo menos 1600 píxeis de largura. Preenche o painel ao lado do formulário de acesso; a parte inferior é escurecida sob o nome da universidade.';

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
$string['stat_partners'] = 'Universidades parceiras';
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
$string['footer_platform'] = 'Plataforma';
$string['footer_privacy'] = 'Resumo de privacidade';

// Login page brand panel.
$string['login_tagline'] = 'Aprenda com parceiros além-fronteiras';
$string['login_intro'] = 'Formações conjuntas e projetos de inovação práticos das nossas universidades parceiras, num único lugar.';
$string['login_partner_intro'] = 'Inicie sessão com a sua conta {$a}.';
$string['login_partner_member'] = 'Membro da Aliança Universitária Europeia DIVERSE';

// Privacy API.
$string['privacy:metadata'] = 'O tema DIVERSE não armazena dados pessoais sobre nenhum utilizador.';
