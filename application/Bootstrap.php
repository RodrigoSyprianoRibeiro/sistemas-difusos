<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected $usuarioLogado;

    protected function _initAutoloader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace("Aplicacao");
        return $autoloader;
    }

    protected function _initPlugins() {
        $bootstrap = $this->getApplication();

        if ($bootstrap instanceof Zend_Application)
            $bootstrap = $this;

        $bootstrap->bootstrap("FrontController");
        $front = $bootstrap->getResource("FrontController");

        $front->registerPlugin(new Aplicacao_Plugins_Layout());
    }

    protected function _initC() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);

        $db = Zend_Db_Table::getDefaultAdapter();
        Zend_Registry::set('db', $db);
    }

    protected function _initViews() {
        $this->bootstrap("view");
        $view = $this->getResource("view");

        $view->doctype('HTML5');
        $view->headTitle('Sistemas Difusos')->setSeparator(' | ');
        $view->headMeta()->appendHttpEquiv('Content-type', 'text/html; charset=UTF-8');

        Zend_Registry::set('view', $view);
    }

    protected function _initConfig() {
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
    }
}