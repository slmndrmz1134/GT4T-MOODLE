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
 * DIVERSE AI assistant - Language strings (English)
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'API key';
$string['apikey_desc_anthropic'] = 'From console.anthropic.com > API keys (starts with sk-ant-).';
$string['apikey_desc_gemini'] = 'From aistudio.google.com > Get API key (starts with AIza). Use the key of a project with billing enabled: on the free tier Google may use the questions to improve its products.';
$string['apikey_desc_openai'] = 'From platform.openai.com > API keys (starts with sk-; billing must be set up). A ChatGPT subscription is not an API key.';
$string['apikey_desc_openaicompatible'] = 'The key of the service at the API address below.';
$string['apikey_placeholder_anthropic'] = 'sk-ant-...';
$string['apikey_placeholder_gemini'] = 'AIza...';
$string['apikey_placeholder_openai'] = 'sk-...';
$string['apikey_placeholder_openaicompatible'] = 'API key';
$string['apikey_placeholder_set'] = 'A key is saved. Paste a new key to replace it.';
$string['apikey_remove'] = 'Remove the saved key';
$string['apikey_saved'] = 'A key is saved (encrypted).';
$string['back'] = 'Back';
$string['closepanel'] = 'Close';
$string['connectionheading'] = 'AI service';
$string['connectionheading_desc'] = 'Choose the service, paste its API key and save. Keys are stored encrypted and never shown again; an empty key field keeps the saved key. A Claude (sk-ant-…) or Gemini (AIza…) key pasted in any key field switches to that service by itself. After saving, the connection is checked (shown above) and the Model menu lists the models the key can use.';
$string['delete'] = 'Delete';
$string['deleteconversation'] = 'Delete chat';
$string['deleteconversation_confirm'] = 'Delete the chat "{$a}"? This cannot be undone.';
$string['deletehistory'] = 'Delete all my chats';
$string['deletehistory_confirm'] = 'Delete all your saved chats in every course? This cannot be undone.';
$string['deletehistory_desc'] = 'Deletes every chat saved on the server, in all courses.';
$string['diverse_assistant:use'] = 'Use the AI course assistant';
$string['emptytitle'] = 'Ask anything about this course.';
$string['enabled'] = 'Enable the assistant';
$string['enabled_desc'] = 'Shows the AI assistant tab on course pages to everyone allowed to use it (students and teachers by default; capability local/diverse_assistant:use).';
$string['endpoint'] = 'API address';
$string['endpoint_desc'] = 'Address of the OpenAI-compatible API, up to and including the version, e.g. https://api.mistral.ai/v1. Local addresses (e.g. Ollama) must also be allowed under Site administration > General > Security > HTTP security.';
$string['endpointineu'] = 'This service processes data in the EU';
$string['endpointineu_desc'] = 'Tick only if the service contractually guarantees that requests are processed in the European Union. Needed when "EU only" is on.';
$string['errorauth'] = 'The AI service rejected the API key.';
$string['errorbusy'] = 'The AI service is very busy right now. Please try again in a moment.';
$string['errorconnection'] = 'The AI service could not be reached. Please try again later.';
$string['errorempty'] = 'The AI service sent an empty answer. Please try again.';
$string['errorforbidden'] = 'The AI service refused the request for this API key.';
$string['errorgeneric'] = 'Something went wrong. Please try again.';
$string['errorlimit'] = 'You have reached the limit of {$a} questions per hour. Please try again later.';
$string['errormodel'] = 'The AI service does not know the chosen model or address.';
$string['errornoendpoint'] = 'No API address is set for the AI service.';
$string['errornomessage'] = 'Please write a question.';
$string['errornomodel'] = 'No model is set for the AI service.';
$string['errornotconfigured'] = 'The AI service is not set up yet.';
$string['errornotfound'] = 'This chat does not exist any more.';
$string['errorratelimit'] = 'The AI service is busy or its usage limit was reached. Please try again later.';
$string['errorrefusal'] = 'The AI declined to answer this question. Please ask it in a different way.';
$string['errorservice'] = 'The AI service reported an error. Please try again later.';
$string['errortoolong'] = 'The question is too long (at most {$a} characters).';
$string['euonly'] = 'EU only';
$string['euonly_desc'] = 'When ticked, the assistant only works with a connection that guarantees processing in the European Union: OpenAI with the EU data residency address, or a compatible service confirmed to be in the EU. The Claude and Gemini APIs offer no EU-only option. Otherwise students see that the assistant is not available.';
$string['footnote'] = 'AI can make mistakes. Do not share personal information.';
$string['history'] = 'Saved chats';
$string['historydeleted'] = 'Your saved chats were deleted.';
$string['historyempty'] = 'No saved chats in this course yet.';
$string['limitsheading'] = 'Limits';
$string['maxcontextchars'] = 'Course materials sent (characters)';
$string['maxcontextchars_desc'] = 'The course materials are sent with every question. Longer materials are cut off at this length. 60,000 characters are about 15,000 tokens.';
$string['model'] = 'Model';
$string['model_desc'] = 'After the connection check the menu lists the chat models of the key. Recommended: gpt-6-luna (OpenAI), claude-opus-5 (Claude), gemini-3.8-flash (Gemini).';
$string['model_recommended'] = '{$a} (recommended)';
$string['newchat'] = 'New chat';
$string['notice_accept'] = 'I understand, start';
$string['notice_body'] = 'This assistant uses artificial intelligence ({$a}). Your questions and the materials of this course are sent to this service to create the answers. Your name and e-mail address are not sent.';
$string['notice_mistakes'] = 'Answers can be wrong or incomplete. Check important information in the course materials.';
$string['notice_personal'] = 'Do not write personal information (yours or other people\'s) in your questions.';
$string['notice_retention'] = 'You decide under Settings whether your chats are saved, and for how long. By default nothing is saved.';
$string['notice_title'] = 'Before you start';
$string['openairegion'] = 'Data region';
$string['openairegion_desc'] = 'Use the EU address only if the OpenAI project of the key was created with European data residency.';
$string['openairegion_eu'] = 'European Union (eu.api.openai.com)';
$string['openairegion_global'] = 'Default (api.openai.com)';
$string['paneltitle'] = 'AI assistant';
$string['placeholder'] = 'Ask about this course…';
$string['pluginname'] = 'DIVERSE AI assistant';
$string['privacy:metadata:aiservice'] = 'To answer, the question is sent to the AI service chosen by the site administrator, together with the earlier messages of the chat and the course materials the user can see. The user\'s name, e-mail address and Moodle id are not sent.';
$string['privacy:metadata:aiservice:coursematerials'] = 'Teacher-written course materials the user can see';
$string['privacy:metadata:aiservice:history'] = 'The earlier messages of the chat';
$string['privacy:metadata:aiservice:message'] = 'The question';
$string['privacy:metadata:conv'] = 'Chats the user chose to save; deleted when the period the user chose ends.';
$string['privacy:metadata:conv:courseid'] = 'The course the chat is about';
$string['privacy:metadata:conv:timecreated'] = 'When the chat started';
$string['privacy:metadata:conv:timemodified'] = 'Time of the last message';
$string['privacy:metadata:conv:title'] = 'Start of the first question';
$string['privacy:metadata:conv:userid'] = 'The user who owns the chat';
$string['privacy:metadata:msg'] = 'Messages of saved chats';
$string['privacy:metadata:msg:content'] = 'The text of the message';
$string['privacy:metadata:msg:role'] = 'Whether the message is a question or an answer';
$string['privacy:metadata:msg:timecreated'] = 'When the message was written';
$string['privacy:metadata:preference:notice'] = 'When the user read the notice shown before the first chat';
$string['privacy:metadata:preference:retention'] = 'How long the user\'s chats are kept';
$string['privacy:metadata:use'] = 'One row per answered question, without any text, for the hourly limit and the usage totals; kept for 30 days.';
$string['privacy:metadata:use:completiontokens'] = 'Length of the answer, in tokens';
$string['privacy:metadata:use:courseid'] = 'The course';
$string['privacy:metadata:use:prompttokens'] = 'Length of the request, in tokens';
$string['privacy:metadata:use:timecreated'] = 'When the question was asked';
$string['privacy:metadata:use:userid'] = 'The user who asked';
$string['privacyheading'] = 'Data protection';
$string['privacyheading_desc'] = 'Students decide themselves whether and for how long their chats are saved (default: not saved); administrators cannot read chats. The service receives the question, the chat and the course materials, but never the student\'s name, e-mail address or Moodle id.';
$string['provider'] = 'Service';
$string['provider_anthropic'] = 'Claude (Anthropic)';
$string['provider_desc'] = 'Which AI service answers the questions. Each service keeps its own key, so you can switch back and forth.';
$string['provider_gemini'] = 'Google Gemini';
$string['provider_openai'] = 'OpenAI';
$string['provider_openaicompatible'] = 'OpenAI-compatible service (custom address)';
$string['quizlock'] = 'Pause during quizzes';
$string['quizlock_desc'] = 'While a student has an unfinished quiz attempt in the course, the assistant does not answer.';
$string['reason_disabled'] = 'The AI assistant is turned off.';
$string['reason_euonly'] = 'The AI assistant is not available: the site only allows services that process data in the EU.';
$string['reason_nocapability'] = 'You are not allowed to use the AI assistant in this course.';
$string['reason_notconfigured'] = 'The AI assistant is not set up yet.';
$string['reason_quiz'] = 'The AI assistant is paused while you have an unfinished quiz attempt in this course.';
$string['reasoningeffort'] = 'Thinking effort';
$string['reasoningeffort_default'] = 'Model default';
$string['reasoningeffort_desc'] = 'How much the model thinks before answering: more effort gives more careful but slower and more expensive answers. Sent only to models that support it (GPT-5 and newer, o-series; Claude Opus 4.5+, Sonnet 4.6+; Gemini). "None" keeps the model\'s default where thinking cannot be turned off.';
$string['reasoningeffort_high'] = 'High';
$string['reasoningeffort_low'] = 'Low (recommended for chat)';
$string['reasoningeffort_medium'] = 'Medium';
$string['reasoningeffort_none'] = 'None';
$string['retention_days'] = '{$a} days';
$string['retention_desc'] = 'Only you decide this. A shorter period deletes older chats at once. With "Do not save" the chat stays only in this browser tab.';
$string['retention_forever'] = 'Until I delete them';
$string['retention_notsaved'] = 'Do not save';
$string['retention_oneday'] = '1 day';
$string['retention_title'] = 'Keep my chats';
$string['retentionsaved'] = 'Your choice was saved.';
$string['retentionstatus'] = 'Chats: {$a}';
$string['retry'] = 'Try again';
$string['send'] = 'Send';
$string['settings'] = 'Settings';
$string['status_checked'] = 'checked {$a}';
$string['status_disabled'] = 'The assistant is turned off: students do not see it yet.';
$string['status_error'] = 'No connection:';
$string['status_euwarning'] = '"EU only" is on, but this connection does not guarantee processing in the EU: students cannot use the assistant.';
$string['status_freetier'] = 'This Gemini key is on Google\'s free tier: only a few questions per minute per model are allowed, and Google may use the questions to improve its products. Enable billing for the key\'s Google Cloud project before students use the assistant.';
$string['status_lasterror'] = 'Last unanswered question ({$a->time}, {$a->model}): {$a->error}';
$string['status_lastfallback'] = '{$a->time}: {$a->from} was overloaded, so {$a->to} answered instead.';
$string['status_modelchanged'] = 'The model "{$a->old}" is not available with this service, so "{$a->new}" was chosen. You can choose another one in the Model menu below.';
$string['status_modelmissing'] = 'This key cannot use the model "{$a}". Choose another one in the Model menu below and save.';
$string['status_models'] = '{$a} chat models available';
$string['status_nokey'] = 'No API key yet. Paste one below and save.';
$string['status_ok'] = 'Connected: {$a->provider}, model {$a->model}';
$string['status_pending'] = 'The connection is checked when this settings page is opened.';
$string['status_switched'] = 'The pasted key belongs to {$a}: it was saved for that service, which is now the active one.';
$string['status_usage'] = 'Last 24 hours: {$a->dayquestions} questions. Last 7 days: {$a->weekquestions} questions from {$a->weekusers} users, {$a->weektokens} tokens.';
$string['stop'] = 'Stop';
$string['stopped'] = 'Stopped.';
$string['suggestion_concepts'] = 'List the key concepts';
$string['suggestion_page'] = 'Explain this page simply';
$string['suggestion_quiz'] = 'Ask me 3 questions to test myself';
$string['suggestion_summary'] = 'Summarise this course';
$string['task_cleanup'] = 'Delete expired AI assistant chats';
$string['testconnection'] = 'Check connection now';
$string['thinking'] = 'Thinking…';
$string['toggle'] = 'AI assistant';
$string['truncated'] = 'The answer was cut off because it was too long.';
$string['userhourlylimit'] = 'Questions per user per hour';
$string['userhourlylimit_desc'] = 'Protects against high costs. 0 means no limit.';
