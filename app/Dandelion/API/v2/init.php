<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\v2;

// Register modules and commands with api commander
$apiCommander->registerModule('dandelion', __NAMESPACE__.'\DandelionAPI',
    [
        ['version', null, ['http_method' => 'get']],
    ]);

$apiCommander->registerModule('auth', __NAMESPACE__.'\AuthAPI',
    [
        ['login', null, [
            'http_method' => 'post',
            'parameters' => [
                'username' => [null, 'string', 'required'],
                'password' => [null, 'string', 'required'],
            ],
        ]],
    ]);
