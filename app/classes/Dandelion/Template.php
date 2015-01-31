<?php
/**
 * Wrapper around Plates library for common items
 */
namespace Dandelion;

use \Dandelion\Logging;
use \Dandelion\Application;
use \League\Plates\Engine;

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
            'pageTitle' => $title
        ]);

        try {
            echo $this->template->render($page);
        } catch (\Exception $e) {
            Logging::errorPage($e, '404: Page not found. Please check your address.');
        }
        return;
    }

    public function registerFunction($name, \Closure $closure)
    {
        $this->template->registerFunction($name, $closure);
    }

    public function addData(array $data)
    {
        $this->template->addData($data);
    }
}
