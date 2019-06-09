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
        // [url path, method name (if different), validation schema]
        ['version', null, ['http_method' => 'get']],
    ]);

$loginJsonSchema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "username": {
            "type": "string"
        },
        "password": {
            "type": "string"
        }
    },
    "required": ["username", "password"]
}
JSON;

$apiCommander->registerModule('auth', __NAMESPACE__.'\AuthAPI',
    [
        ['login', null, [
            'http_method' => 'post',
            'json_schema' => $loginJsonSchema,
        ]],
    ]);

$createLogJsonSchema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "title": {
            "type": "string"
        },
        "body": {
            "type": "string"
        },
        "category": {
            "type": "string"
        }
    },
    "required": ["title", "body", "category"]
}
JSON;

$apiCommander->registerModule('logs', __NAMESPACE__.'\LogsAPI',
    [
        ['', 'read', [
            'http_method' => 'get',
            'parameters' => [
                'logid' => [null, 'int'],
                'offset' => [0, 'int'],
                'limit' => [null, 'int']
            ]
        ]],
        ['', 'create', [
            'http_method' => 'post',
            'json_schema' => $createLogJsonSchema,
        ]],
        ['search', null, [
            'http_method' => 'get',
            'parameters' => [
                'q' => [null, 'string', 'required'],
                'offset' => [0, 'int'],
                'limit' => [null, 'int'],
            ]
        ]]
    ]);
