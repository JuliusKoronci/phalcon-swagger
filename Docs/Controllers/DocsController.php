<?php

namespace Igsem\Docs\Controllers;

use Igsem\Docs\Services\DocsService;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View\Simple;

/**
 * Class DocsController
 * Methods to render Swagger UI and generate JSON definitions
 * @package Igsem\Docs\Controllers
 */
class DocsController extends Controller
{
    /**
     * Parses your application for swagger annotations and returns a json. This is used by the Swagger UI
     *
     * @return string
     */
    public function indexAction()
    {
        /** @var array $config */
        $config = $this->di->get('swagger');
        $options = DocsService::getOptions($config);
        $swagger = \Swagger\scan($config['path'], $options);
        $swagger->host = $config['host'];
        $swagger->schemes = $config['schemes'];
        $swagger->basePath = $config['basePath'];
        $swagger->info->version = $config['version'];
        $swagger->info->title = $config['title'];
        $swagger->info->description = $config['description'];
        $swagger->info->contact->email = $config['email'];

        return $swagger->__toString();
    }

    /**
     * Manually render a view which will load swagger definitions and display the swagger UI
     *
     * We don't know how the view will be handled, therefore we don't use the view from DI
     *
     * @return string
     */
    public function docsAction()
    {
        $view = new Simple();
        return $view->render(__DIR__ . '/../views/Docs/docs', [
            'url' => $this->di->get('swagger')['jsonUri'],
        ]);
    }
}