<?php
/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 15.05.2016
 * Time: 1:26
 */
if (!defined('MODX_BASE_PATH')) {die();}

$_lang = array();
$_lang['form.protectSubmit'] = 'Message has been sent. There\'s no need to send it again';
$_lang['form.submitLimit'] = 'You may send message again in ';
$_lang['form.minutes'] = 'min';
$_lang['form.dateFormat'] = 'm.d.Y at H:i:s';
$_lang['form.default_successTpl'] = '@CODE:Message was successfully sent [+form.date.value+]';
return $_lang;