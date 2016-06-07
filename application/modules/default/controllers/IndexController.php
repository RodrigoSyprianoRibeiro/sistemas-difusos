<?php

class Default_IndexController extends Aplicacao_Controller_Action {

    public function indexAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_redirect ('projeto');
    }
}