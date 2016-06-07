<?php

class Aplicacao_Plugins_ScriptLoader extends Zend_Controller_Plugin_Abstract
{
    static function loadCss($view, $scripts) {
        foreach ($scripts as $css) {
            $view->headLink()->appendStylesheet($css);
        }
    }

    static function loadJavascript($view, $scripts) {
        foreach ($scripts as $javascript) {
            $view->inlineScript()->appendFile($javascript);
        }
    }
}