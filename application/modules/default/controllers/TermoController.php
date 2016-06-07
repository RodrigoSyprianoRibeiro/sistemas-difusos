<?php

class Default_TermoController extends Aplicacao_Controller_Action {

    public function novoAction() {
        $this->_helper->layout->disableLayout();
    }

    public function salvarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelTermo = new Application_Model_Termo();
            if ($modelTermo->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Termo cadastrado.'));
            } else {
                echo "Não foi possível <strong>cadastrar</strong> o termo.";
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelTermo = new Application_Model_Termo();
            $this->data['ativo'] = 0;
            if ($modelTermo->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Termo removido.'));
            } else {
                echo "Não foi possível <strong>remover</strong> o termo.";
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
            $modelTermo = new Application_Model_Termo();

            $variaveis = $modelVariavel->getVariaveis($id);

            $retorno = array();
            foreach ($variaveis as $variavel) {
                $retorno[] = (object) array('id' => $variavel->id,
                                            'nome' => $variavel->nome,
                                            'inicio_universo' => $variavel->inicio_universo,
                                            'fim_universo' => $variavel->fim_universo,
                                            'unidade_medida' => $variavel->unidade_medida,
                                            'objetiva' => $variavel->objetiva,
                                            'termos' => $modelTermo->getTermos($variavel->id));
            }
            $this->view->variaveis = $retorno;
        }
    }

    public function validarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelVariavel = new Application_Model_Variavel();
            $variaveis = $modelVariavel->getVariaveis($this->data['id_projeto']);

            $faltaTermo = false;

            $modelTermo = new Application_Model_Termo();
            foreach ($variaveis as $variavel) {
                if (count($modelTermo->getTermos($variavel->id)) == 0) {
                    $faltaTermo = true;
                }
            }
            $mensagem = '';
            if ($faltaTermo === false) {
                $mensagem .= 'sucesso';
            } else {
                $mensagem .= '- Adicione pelo menos um termo para cada variável <br/>';
            }
            echo $mensagem;
        }
    }
}