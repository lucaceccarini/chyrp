<?php

/*
 * This file is part of Chyrp.
 *
 * (c) 2014 Arian Xhezairi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Twig Template Engine Setup.
 *
 * @package    chyrp
 * @author     Arian Xhezairi <email@chyrp.net>
 */
class Twig
{
    /**
     * Templates path on filesystem
     * @var string
     */
    public $templatePaths = array();
    private $loader       = null;

    private function __construct()
    {
        Twig_Autoloader::register();
        Twig_Extensions_Autoloader::register();

        $this->twig = new Twig_Environment($this->getLoader(), $this->getOptions());
        $this->loadExtensions();
    }

    public function addTemplatePath($path = null)
    {
        $this->loader->prependPath($path);
    }

    private function getLoader()
    {
        $this->setTemplatePaths();
        $this->loader = new Twig_Loader_Filesystem($this->templatePaths);

        return $this->loader;
    }

    private function setTemplatePaths()
    {
        if (ADMIN) {
            $adminTheme = fallback(Config::current()->admin_theme, "default");
            $this->templatePaths[] = ADMIN_THEMES_DIR.'/'.$adminTheme;
        } else {
            $this->templatePaths[] = THEME_DIR;
        }
    }

    private function loadExtensions()
    {
        if ($this->getDebug())
            $this->twig->addExtension(new Twig_Extension_Debug());

        $this->twig->addExtension(new Chyrp_Twig_Extension());
    }

    private function getOptions()
    {
        return array('cache' => $this->getCache(),
                     'debug' => $this->getDebug(),
                     'autoescape' => false);
    }

    private function getDebug()
    {
        return DEBUG ? true : false ;
    }

    private function getCache()
    {
        $cache = (is_writable(INCLUDES_DIR."/caches") && !DEBUG &&
            !PREVIEWING && !defined('CACHE_TWIG') || CACHE_TWIG);

        return ($cache ? INCLUDES_DIR."/caches" : false);
    }

    public function display($file = null, $context = null)
    {
        return $this->twig->display($file, $context);
    }

    /**
     * Function: current
     * Returns a singleton reference to the current configuration.
     */
    public static function & current() {
        static $instance = null;
        return $instance = (empty($instance)) ? new self() : $instance ;
    }
}
