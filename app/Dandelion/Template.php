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
use Dandelion\Utils\Configuration as Config;
use Dandelion\Exception\AbortException;
use Dandelion\Session\SessionManager as Session;

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

        $this->registerFunction('loadCss', function($sheets = []) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadSimpleCss'), $sheets);
        });

        $this->registerFunction('loadJS', function($js) {
            return call_user_func_array(array('\Dandelion\Utils\View', 'loadJS'), $js);
        });
    }

    public function render($page, $title = '')
    {
        $title = $title ?: ucfirst($page);

        $userInfo = Session::get('userInfo');
        $fullname = $userInfo['fullname'] ?? '';

        $this->addData([
            'appTitle' => Config::get('appTitle', ''),
            'tagline' => Config::get('tagline', ''),
            'appVersion' => Application::VERSION,
            'appVersionName' => Application::VER_NAME,
            'pageTitle' => $title,
            'hostname' => Config::get('hostname', ''),
            'userFullname' => $fullname,
        ]);

        try {
            $templateStr = $this->template->render($page);
            return $templateStr;
        } catch (\Exception $e) {
            $this->app->response->setStatus(404);
            $this->app->logger->info("404 Page not found: Template '{temp}' missing", ['temp' => $page]);

            if ($page === '404notfound') {
                // Protect against loops, just in case the template disappears
                $this->app->logger->error('404 loop detected, aborting.');
                throw new AbortException();
            }

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
