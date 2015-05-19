<?php

namespace Swissbib\Module\Config;



$config = [
    'router' => [
        'routes' => []
    ],
    'controllers' => [
        'invokables' => [
            'exploration'    => 'LinkedSwissbib\Controller\ElasticsearchController'
        ]
    ]
];


// Define static routes -- Controller/Action strings
$staticRoutes = [
    'Exploration/Search'
    ];

$routeGenerator = new \VuFind\Route\RouteGenerator();
$routeGenerator->addStaticRoutes($config, $staticRoutes);


return $config;

