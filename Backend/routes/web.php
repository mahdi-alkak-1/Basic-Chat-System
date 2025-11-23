<?php

$apis = [

    // Auth
    '/auth/login'     => ['controller' => 'AuthController', 'method' => 'login'],
    '/auth/register'  => ['controller' => 'AuthController', 'method' => 'register'],

    // Conversations
    '/conversation/start' => [
        'controller' => 'ConversationController',
        'method'     => 'startConversation'
    ],

    // 👉 ADD THIS:
    '/conversation/list' => [
        'controller' => 'ConversationController',
        'method'     => 'listConversations'
    ],

    // Messages
    '/messages/send' => [
        'controller' => 'MessageController',
        'method'     => 'sendMessage'
    ],

    '/messages/list' => [
        'controller' => 'MessageController',
        'method'     => 'getMessages'
    ],

    '/messages/mark-delivered' => [
        'controller' => 'MessageController',
        'method'     => 'markDelivered'
    ],

    '/messages/mark-read' => [
        'controller' => 'MessageController',
        'method'     => 'markRead'
    ],
];
