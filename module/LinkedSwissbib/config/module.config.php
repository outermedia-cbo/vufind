<?php


/**
 *
 * @category linked-swissbib
 * @package  /
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */


namespace Swissbib\Module\Config;




$config = [
    'router' => [
        'routes' => []
    ],
    'controllers' => [
        'invokables' => [
            'exploration'    => 'LinkedSwissbib\Controller\ElasticsearchController'
        ]
    ],
    'service_manager' => [
        'factories' => [
            'LinkedSwissbib\SearchOptionsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchOptionsPluginManager',
            'LinkedSwissbib\SearchParamsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchParamsPluginManager',
            'LinkedSwissbib\SearchResultsPluginManager' => 'LinkedSwissbib\Service\Factory::getSearchResultsPluginManager'
            ]

    ],
    'vufind' => [
        'plugin_managers' => [

            'search_options' => [
                'abstract_factories' => ['LinkedSwissbib\Search\Options\PluginFactory'],
            ],
            'search_params' => [
                'abstract_factories' => ['LinkedSwissbib\Search\Params\PluginFactory'],
            ],
            'search_results' => [
                'abstract_factories' => ['LinkedSwissbib\Search\Results\PluginFactory'],
            ],

        ]
    ]


];


// Define static routes -- Controller/Action strings
$staticRoutes = [
    'Exploration/Search', 'Elasticsearch/Results'
    ];

$routeGenerator = new \VuFind\Route\RouteGenerator();
$routeGenerator->addStaticRoutes($config, $staticRoutes);


return $config;

