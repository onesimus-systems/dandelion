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
        ['create', null, [
            'http_method' => 'post',
            'parameters' => [
                'pid' => [null, 'int', 'required'],
                'description' => ['', 'string', 'required']
            ]
        ]],
        ['edit', null, [
            'http_method' => 'post',
            'parameters' => [
                'cid' => [null, 'int', 'required'],
                'description' => ['', 'string', 'required']
            ]
        ]],
        ['delete', null, [
            'http_method' => 'post',
            'parameters' => [
                'cid' => [null, 'int', 'required']
            ]
        ]]
    ]);

$apiCommander->registerModule('cheesto', __NAMESPACE__.'\CheestoAPI',
    [
        ['read', null, [
            'http_method' => 'get',
            'parameters' => [
                'uid' => [null, 'int']
            ]
        ]],

        ['statustexts', 'statusTexts', ['http_method' => 'get']],

        ['update', null, [
            'http_method' => 'post',
            'parameters' => [
                'message' => ['', 'string'],
                'status' => ['Available', 'string'],
                'returntime' => ['00:00:00', 'string'],
                'uid' => [null, 'int']
            ]
        ]]
    ]);

$apiCommander->registerModule('comments', __NAMESPACE__.'\CommentsAPI',
    [
        ['add', null, [
            'http_method' => 'post',
            'parameters' => [
                'logid' => [null, 'int', 'required'],
                'comment' => ['', 'string', 'required']
            ]
        ]],
        ['get', null, [
            'http_method' => 'get',
            'parameters' => [
                'logid' => [null, 'int', 'required'],
                'order' => ['new', 'string']
            ]
        ]]
    ]);

$apiCommander->registerModule('groups', __NAMESPACE__.'\GroupsAPI',
    [
        ['getlist', 'getList', ['http_method' => 'get']],
        ['getgroup', 'getGroup', [
            'http_method' => 'get',
            'parameters' => [
                'groupid' => [null, 'int', 'required']
            ]
        ]],
        ['edit', null, [
            'http_method' => 'post',
            'parameters' => [
                'groupid' => [null, 'int', 'required'],
                'rights' => [null, 'string', 'required']
            ]
        ]],
        ['create', null, [
            'http_method' => 'post',
            'parameters' => [
                'name' => [null, 'string', 'required'],
                'rights' => ['', 'string']
            ]
        ]],
        ['delete', null, [
            'http_method' => 'post',
            'parameters' => [
                'groupid' => [null, 'int', 'required']
            ]
        ]],
        ['getuserrights', 'getUserRights', ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('key', __NAMESPACE__.'\KeyManagerAPI',
    [
        ['get', null, ['http_method' => 'get']],
        ['generate', null, ['http_method' => 'post']],
        ['revoke', null, [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [0, 'int']
            ]
        ]],
        ['test', null, ['http_method' => 'get']]
    ]);

$apiCommander->registerModule('logs', __NAMESPACE__.'\LogsAPI',
    [
        ['read', null, [
            'http_method' => 'get',
            'parameters' => [
                'logid' => [null, 'int'],
                'offset' => [0, 'int'],
                'limit' => [null, 'int']
            ]
        ]],
        ['create', null, [
            'http_method' => 'post',
            'parameters' => [
                'title' => [null, 'string', 'required'],
                'body' => [null, 'string', 'required'],
                'cat' => [null, 'string', 'required']
            ]
        ]],
        ['edit', null, [
            'http_method' => 'post',
            'parameters' => [
                'logid' => [null, 'int', 'required'],
                'title' => [null, 'string', 'required'],
                'body' => [null, 'string', 'required'],
                'cat' => [null, 'string', 'required']
            ]
        ]],
        ['search', null, [
            'http_method' => 'get',
            'parameters' => [
                'query' => [null, 'string', 'required'],
                'offset' => [0, 'int'],
                'limit' => [null, 'int']
            ]
        ]]
    ]);

$apiCommander->registerModule('users', __NAMESPACE__.'\UsersAPI',
    [
        ['resetpassword', 'resetPassword', [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [null, 'int'],
                'pw' => [null, 'string', 'required']
            ]
        ]],
        ['create', null, [
            'http_method' => 'post',
            'parameters' => [
                'username' => [null, 'string', 'required'],
                'password' => [null, 'string', 'required'],
                'fullname' => [null, 'string', 'required'],
                'role' => [null, 'int', 'required'],
                'cheesto' => [true, 'bool']
            ]
        ]],
        ['edit', null, [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [null, 'int', 'required'],
                'fullname' => [null, 'string'],
                'role' => [null, 'int'],
                'prompt' => [null, 'int'],
                'theme' => [null, 'string']
            ]
        ]],
        ['delete', null, [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [null, 'int', 'required']
            ]
        ]],
        ['enable', null, [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [null, 'int', 'required']
            ]
        ]],
        ['disable', null, [
            'http_method' => 'post',
            'parameters' => [
                'uid' => [null, 'int', 'required']
            ]
        ]],
        ['getusers', 'getUsers', ['http_method' => 'get']],
        ['getuser', 'getUser', [
            'http_method' => 'get',
            'parameters' => [
                'uid' => [null, 'int', 'required']
            ]
        ]]
    ]);

$apiCommander->registerModule('usersettings', __NAMESPACE__.'\UserSettingsAPI',
    [
        ['saveloglimit', 'saveLogLimit', [
            'http_method' => 'post',
            'parameters' => [
                'limit' => [25, 'int']
            ]
        ]],
        ['savetheme', 'saveTheme', [
            'http_method' => 'post',
            'parameters' => [
                'theme' => ['', 'string']
            ]
        ]],
        ['getthemelist', 'getThemeList', ['http_method' => 'get']]
    ]);
