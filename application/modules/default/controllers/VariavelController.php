<?php

class Default_VariavelController extends Aplicacao_Controller_Action {

    public function novoAction() {
        $this->_helper->layout->disableLayout();

        $id = (int) $this->_request->getParam("id",0);
        $modelProjeto = new Application_Model_Projeto();
        $projeto = $modelProjeto->find($id);

        if ($projeto) {
            $this->view->projeto = $projeto;
            $modelUnidadeMedida = new Application_Model_UnidadeMedida();
            $this->view->unidades_medidas = $modelUnidadeMedida->getUnidadesMedidas();
        }
    }

    public function salvarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelVariavel = new Application_Model_Variavel();
            if ($modelVariavel->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Variável cadastrada.'));
            } else {
                echo "Não foi possível <strong>cadastrar</strong> a variável.";
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelVariavel = new Application_Model_Variavel();
            $this->data['ativo'] = 0;
            if ($modelVariavel->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Variável removida.'));
            } else {
                echo "Não foi possível <strong>remover</strong> a variável.";
            }
        }
    }

    public function carregarAction() {
        $this->_helper->layout->disableLayout();

        $id = (int) $this->_request->getParam("id",0);
        $modelProjeto = new Application_Model_Projeto();
        $projeto = $modelProjeto->find($id);

        if ($projeto) {
            $modelVariavel = new Application_Model_Variavel();
            $this->view->variaveis = $modelVariavel->getVariaveis($id);
        }
    }

    public function validarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelVariavel = new Application_Model_Variavel();
            $variaveis = $modelVariavel->getVariaveis($this->data['id_projeto']);

            $temObjetiva = false;
            $temNaoObjetiva = false;

            foreach ($variaveis as $variavel) {
                if ($variavel->objetiva == 1) {
                    $temObjetiva = true;
                } else {
                    $temNaoObjetiva = true;
                }
            }
            $mensagem = '';
            if ($temObjetiva === true && $temNaoObjetiva === true) {
                $mensagem .= 'sucesso';
            } else {
                if ($temObjetiva === false) {
                    $mensagem .= '- Adicione pelo menos uma variável objetiva <br/>';
                }
                if ($temNaoObjetiva === false) {
                    $mensagem .= '- Adicione pelo menos uma variável não objetiva <br/>';
                }
            }
            echo $mensagem;
        }
    }

    public function getdadosgraficoAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelVariavel = new Application_Model_Variavel();
            echo json_encode($modelVariavel->geraDadosGraficos($this->data['id']));
        }
    }
}