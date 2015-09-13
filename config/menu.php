<?php

return [
    'sidebar' => [
        [
            'route' => '',
            'icon' => 'fa-users',
            'label' => 'Пользователи',
            'match' => ['admin/users*', 'admin/roles*'],
            'items' => [
                [
                    'label' => 'Пользователи',
                    'route' => 'admin.users.index',
                    'match' => 'admin/users*'
                ]
            ]
        ],
        [
            'route' => 'admin.contracts.index',
            'icon' => '',
            'label' => 'Контракты',
            'match' => ['admin/contracts*'],
            'items' => [
                [
                    'label' => 'Контракты',
                    'route' => 'admin.contracts.index',
                    'match' => 'admin/contracts*'
                ],
                [
                    'label' => 'Моя выборка',
                    'route' => 'admin.contracts.match',
                    'match' => 'admin/contracts/match'
                ]
            ]
        ],
        [
            'route' => '',
            'icon' => 'fa-cogs',
            'match' => ['admin/countries*', 'admin/cities*'],
            'label' => 'Система',
            'items' => [

            ]
        ]
    ]
];