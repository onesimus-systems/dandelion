<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

// Register modules and commands with api commander
$apiCommander->registerModule('categories', __NAMESPACE__.'\CategoriesAPI',
    [
        ['create', null, ['http_method' => 'post']],
        ['edit', null, ['http_method' => 'post']],
        ['delete', null, ['http_method' => 'post']]
    ]);

$apiCommander->registerModule('cheesto', __NAMESPACE__.'\CheestoAPI',
    [
        ['read', null, ['http_method' => 'get']],
        ['statustexts', 'statusTexts', ['http_method' => 'get']],
        ['update', null, ['http_method' => 'post']]
    ]);

$apiCommander->registerModule('comments', __NAMESPACE__.'\CommentsAPI',
    [
        ['add', null, ['http_method' => 'post']],
        ['get', null, ['http_method' => 'post']]
    ]);

$apiCommander->registerModule('groups', __NAMESPACE__.'\GroupsAPI',
    [
        ['getlist', 'getList', ['http_method' => 'get']],
        ['getgroup', 'getGroup', ['http_method' => 'get']],
        ['edit', null, ['http_method' => 'post']],
        ['create', null, ['http_method' => 'post']],
        ['delete', null, ['http_method' => 'post']],
        ['getuserrights', 'getUserRights', ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('keymanager', __NAMESPACE__.'\KeyManagerAPI',
    [
        ['get', null, ['http_method' => 'get']],
        ['generate', null, ['http_method' => 'post']],
        ['revoke', null, ['http_method' => 'post']],
        ['test', null, ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('logs', __NAMESPACE__.'\LogsAPI',
    [
        ['read', null, ['http_method' => 'get']],
        ['create', null, ['http_method' => 'post']],
        ['edit', null, ['http_method' => 'post']],
        ['search', null, ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('users', __NAMESPACE__.'\UsersAPI',
    [
        ['resetpassword', 'resetPassword', ['http_method' => 'post']],
        ['create', null, ['http_method' => 'post']],
        ['edit', null, ['http_method' => 'post']],
        ['delete', null, ['http_method' => 'post']],
        ['getusers', 'getUsers', ['http_method' => 'get']],
        ['getuser', 'getUser', ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('usersettings', __NAMESPACE__.'\UserSettingsAPI',
    [
        ['saveloglimit', 'saveLogLimit', ['http_method' => 'post']],
        ['savetheme', 'saveTheme', ['http_method' => 'post']],
        ['getthemelist', 'getThemeList', ['http_method' => 'get']]
    ]);
