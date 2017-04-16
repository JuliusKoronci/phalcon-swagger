<?php

namespace Igsem\Docs\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View\Simple;


class DocsController extends Controller
{
    /**
     * @return string
     */
    public function indexAction()
    {
        /** @var array $config */
        $config = $this->di->get('swagger');
        $swagger = \Swagger\scan($config['path']);
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