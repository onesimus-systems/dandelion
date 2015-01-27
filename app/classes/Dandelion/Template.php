<?php
/**
 * Templating library
 */
namespace Dandelion;

use \Dandelion\View;

class Template extends View
{
	protected function render($template, $data = null)
    {
        try {
            $templatePathname = $this->getTemplatePathname($template);
            if (!is_file($templatePathname)) {
                throw new \RuntimeException("View cannot render `$template` because the template does not exist");
            }
        } catch (\Exception $e) {
            // Eventually show some error page
            return $e;
        }

        $data = array_merge($this->all(), (array) $data);
        extract($data);
        ob_start();
        require $templatePathname;
        $rawTemplate = ob_get_clean();

        return $this->process($rawTemplate, $data);
    }

    private function process($template, $data)
    {
    	foreach ($data as $key => $value) {
    		if (is_array($value)) {
    			continue;
    		}
    		$template = preg_replace("/\{\{$key\}\}/", $value, $template);
    	}
    	return $template;
    }
}