<?php

namespace Igsem\Docs\Controllers;

use Igsem\Docs\Services\DocsService;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View\Simple;



/**
 * Class DocsController
 * Methods to render openapi UI and generate JSON definitions
 * @package Igsem\Docs\Controllers
 */
class DocsController extends Controller
{
    /**
     * Parses your application for openapi annotations and returns a json. This is used by the openapi UI
     *
     * @return string
     */
    public function indexAction()
    {
        /** @var array $config */
        $config = $this->di->get('openapi');
        $options = DocsService::getOptions($config);
        $openapi = \OpenApi\scan($config['path'], $options);
        $openapi->servers = $config['servers'];
        $openapi->info = [
          "title"       => $config['title'],
          "version"     => $config['version'],
          "description" => $config['description'],
          "contact"     => [
            "email" => $config['email']
          ]
        ];
        return $openapi->toJson();
    }

    /**
     * Manually render a view which will load openapi definitions and display the openapi UI
     *
     * We don't know how the view will be handled, therefore we don't use the view from DI
     *
     * @return string
     */
    public function docsAction()
    {
        $view = new Simple();
        return $view->render(__DIR__ . '/../views/Docs/docs', [
            'url' => $this->di->get('openapi')['jsonUri'],
        ]);
    }
}