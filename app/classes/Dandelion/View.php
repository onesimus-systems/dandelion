<?php
/**
 * Base template rendering class
 */
namespace Dandelion;

class View
{
    protected $data;

    protected $templateDir;

    public function __construct()
    {
        $this->data = array();
    }

    // Data manipulation functions
    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $data)
    {
        $this->set($key, $data);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }

    public function __unset($key)
    {
        return $this->remove($key);
    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function all()
    {
        return $this->data;
    }

    public function set($key, $data)
    {
        $this->data[$key] = $data;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }

    public function clear()
    {
        $this->data = array();
    }

    /**
     * Set the base directory that contains view templates
     * @param   string $directory
     * @throws  \InvalidArgumentException If directory is not a directory
     */
    public function setTemplatesDirectory($directory)
    {
        $this->templateDir = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    /**
     * Get templates base directory
     * @return string
     */
    public function getTemplatesDirectory()
    {
        return $this->templateDir;
    }

    /**
     * Get fully qualified path to template file using templates base directory
     * @param  string $file The template file pathname relative to templates base directory
     * @return string
     */
    public function getTemplatePathname($file)
    {
        return $this->templateDir . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    }

    /**
     * Rendering templates
     */

    /**
     * Display template
     *
     * This method echoes the rendered template to the current output buffer
     *
     * @param  string   $template   Pathname of template file relative to templates directory
     * @param  array    $data       Any additonal data to be passed to the template.
     */
    public function display($template, $data = null)
    {
        echo $this->fetch($template, $data);
    }

    /**
     * Return the contents of a rendered template file
     *
     * @param    string $template   The template pathname, relative to the template base directory
     * @param    array  $data       Any additonal data to be passed to the template.
     * @return string               The rendered template
     */
    public function fetch($template, $data = null)
    {
        return $this->render($template, $data);
    }

    /**
     * Render a template file
     *
     * NOTE: This method should be overridden by custom view subclasses
     *
     * @param  string $template     The template pathname, relative to the template base directory
     * @param  array  $data         Any additonal data to be passed to the template.
     * @return string               The rendered template
     * @throws \RuntimeException    If resolved template pathname is not a valid file
     */
    protected function render($template, $data = null)
    {
        try {
            $templatePathname = $this->getTemplatePathname($template);
            if (!is_file($templatePathname)) {
                throw new \RuntimeException("View cannot render `$template` because the template does not exist");
            }

            $data = array_merge($this->all(), (array) $data);
            extract($data);
            ob_start();
            require $templatePathname;
            return ob_get_clean();
        } catch (\Exception $e) {
            // Eventually show some error page
            return $e;
        }
    }
}
