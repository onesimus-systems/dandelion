<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Logging;
use Dandelion\Application;

use League\Plates\Engine;

class Template
{
    private $app;
    private $template;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->template = new Engine($this->app->paths['app'].'/templates');
        $this->template->addFolder('layouts', $this->app->paths['app'].'/templates/layouts');

        $this->registerFunction('getCssSheets', function($sheets = []) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadCssSheets'), $sheets);
        });

        $this->registerFunction('loadJS', function($js) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadJS'), $js);
        });
    }

    public function render($page, $title = '')
    {
        $title = $title ?: ucfirst($page);

        $this->addData([
            'appTitle' => $this->app->config['appTitle'],
            'tagline' => $this->app->config['tagline'],
            'appVersion' => Application::VERSION,
            'pageTitle' => $title,
            'hostname' => $this->app->config['hostname']
        ]);

        try {
            $templateStr = $this->template->render($page);
            return $templateStr;
        } catch (\Exception $e) {
            $this->app->response->setStatus(404);
            $this->app->logger->info("404 Page not found: Template '{temp}' missing", ['temp' => $page]);
            return $this->render('404notfound', 'Page Not Found');
        }
    }

    public function registerFunction($name, \Closure $closure)
    {
        $this->template->registerFunction($name, $closure);
    }

    public function addData(array $data)
    {
        $this->template->addData($data);
    }

    public function addFolder($template, $folder)
    {
        $this->template->addFolder($template, $folder);
    }
}
