<?php

abstract class Aplicacao_Controller_Action extends Zend_Controller_Action
{
    protected $data;

    public function init() {
        $this->flashMessenger = $this->_helper->FlashMessenger;
        $this->view->messages = $this->flashMessenger->getMessages();
        $this->view->controler = $this->_request->getControllerName();

        if ($this->_request->isPost()) {
            $this->data = $this->_request->getPost();
            if (isset($this->data['submit']))
                unset($this->data['submit']);
        }
    }
}