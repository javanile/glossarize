<?php

namespace Javanile\Glossar;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Source
{
    /**
     *
     */
    protected $vars;

    /**
     *
     */
    protected $config;

    /**
     *
     */
    protected $pattern;

    /**
     *
     */
    protected $path;

    /**
     *
     */
    protected $files;

    /**
     * Config constructor.
     *
     * @param $config
     * @param mixed $pattern
     * @param mixed $path
     */
    public function __construct($config, $pattern, $path)
    {
        $this->vars = [];
        $this->config = $config;
        $this->pattern = $pattern;
        $this->path = $path;
        $this->files = null;
    }

    /**
     * @param $vars
     */
    public function setVars($vars)
    {
        foreach ($vars as $var => $value) {
            $this->set($var, $value);
        }
    }

    /**
     * Get setting value for current source.
     *
     * @param $var
     *
     * @return mixed
     */
    public function get($var)
    {
        return $this->vars[$var];
    }

    /**
     * @param $var
     * @param mixed $value
     */
    public function set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    /**
     *
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     *
     */
    protected function getFiles()
    {
        if ($this->files === null) {
            $cwd = $this->getConfig()->getCwd();
            $this->files = [];
            $files = Glob::glob(Path::makeAbsolute($this->pattern, $this->path));
            foreach ($files as $file) {
                $this->files[] = [
                    'filename' => $file,
                    'relative' => Path::makeRelative($file, $cwd),
                ];
            }
        }

        return $this->files;
    }

    /**
     * @param mixed $dir
     */
    public function scan($dir)
    {
        $source = new self($this->getConfig(), $this->pattern, $this->path.'/'.$dir);

        $source->setVars($this->vars);

        return $source;
    }

    /**
     * @param $var
     * @param mixed $words
     */
    public function ignoreWords($words)
    {
        if (empty($this->vars['ignore-words'])) {
            $this->vars['ignore-words'] = [];
        }

        $this->vars['ignore-words'] = array_merge($this->vars['ignore-words'], $words);

        return $this;
    }

    /**
     * Capture rudiments method called by config file.
     *
     * @param $method
     * @param $args
     *
     * @return Source
     */
    public function __call($method, $args)
    {
        $config = $this->getConfig();
        $rudiments = $config->getRudiments();
        $rudimentClassName = ucfirst($method);

        foreach ($rudiments as $rudiment) {
            $rudimentClass = substr($rudiment, -1) == '\\' ? $rudiment . $rudimentClassName : '';
            if (class_exists($rudimentClass)) {
                $rudimentInstance = new $rudimentClass($config, $this);
                call_user_func_array([$rudimentInstance, $method], $args);
                return $this;
            }
        }
    }
}
