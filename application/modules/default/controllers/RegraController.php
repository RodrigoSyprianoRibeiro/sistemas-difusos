<?php

class Default_RegraController extends Aplicacao_Controller_Action {

    public function salvarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelRegra = new Application_Model_Regra();
            if ($modelRegra->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Regra cadastrada.'));
            } else {
                echo "Não foi possível <strong>cadastrar</strong> a regra.";
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelRegra = new Application_Model_Regra();
            if ($modelRegra->delete($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Regra removida.'));
            } else {
                echo "Não foi possível <strong>remover</strong> a regra.";
            }
        }
    }

    public function carregarAction() {
        $this->_helper->layout->disableLayout();

        $id = (int) $this->_request->getParam("id",0);
        $modelProjeto = new Application_Model_Projeto();
        $projeto = $modelProjeto->find($id);

        if ($projeto) {
            $modelTermo = new Application_Model_Termo();
            $this->view->termosAntecedentes = $modelTermo->getAllTermosAntecedentes($id);
            $this->view->termosConsequentes = $modelTermo->getAllTermosConsequentes($id);

            $modelRegra = new Application_Model_Regra();
            $regras = $modelRegra->getRegras($id);

            $modelRegraTermoAntecedente = new Application_Model_RegraTermoAntecedente();

            $retorno = array();
            foreach ($regras as $regra) {
                $termosAntecedentes = $modelRegraTermoAntecedente->getTermosAntecedentes($regra->id);

                $antecedentes = array();
                foreach ($termosAntecedentes as $termoAntecedente) {
                    $antecedentes[] = "<b>".$termoAntecedente->variavel." é ".$termoAntecedente->termo_antecedente."</b>";
                }

                $retorno[] = (object) array('id' => $regra->id,
                                            'descricao' => "Se ".implode(" ".$regra->operador." ", $antecedentes)." então <b>".$regra->variavel_objetiva." é ".$regra->termo_consequente."</b>.");
            }
            $this->view->regras = $retorno;
        }
    }

    public function validarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelRegra = new Application_Model_Regra();
            $regras = $modelRegra->getRegras($this->data['id_projeto']);

            $mensagem = '';
            if (count($regras) > 1) {
                $mensagem .= 'sucesso';
            } else {
                $mensagem .= '- Adicione pelo menos uma regra <br/>';
            }
            echo $mensagem;
        }
    }
}