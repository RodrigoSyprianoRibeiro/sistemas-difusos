<?php

class Default_ProjetoController extends Aplicacao_Controller_Action {

    public function indexAction() {

        $this->view->headTitle()->prepend('Projetos');

        Aplicacao_Plugins_ScriptLoader::loadCss($this->view, array(
            'assets/css/jquery.gritter.css'
        ));

        Aplicacao_Plugins_ScriptLoader::loadJavascript($this->view, array(
            'assets/js/bootbox.js',
            'assets/js/jquery.gritter.js',
            'assets/js/pages/projeto/index.js'
        ));
    }

    public function visualizarAction() {

        $id = (int) $this->_request->getParam("id",0);
        $modelProjeto = new Application_Model_Projeto();
        $projeto = $modelProjeto->find($id);

        if ($projeto) {
            $modelVariavel = new Application_Model_Variavel();

            $this->view->variaveis = $modelVariavel->getVariaveis($id);
            $this->view->projeto = $projeto;
        } else {
            $this->_redirect ('/');
        }

        Aplicacao_Plugins_ScriptLoader::loadCss($this->view, array(
            'assets/css/jquery.gritter.css',
            'assets/css/select2.css'
        ));

        Aplicacao_Plugins_ScriptLoader::loadJavascript($this->view, array(
            'assets/js/fuelux/fuelux.wizard.js',
            'assets/js/jquery.validate.js',
            'assets/js/bootbox.js',
            'assets/js/jquery.gritter.js',
            'assets/js/fuelux/fuelux.spinner.js',
            'assets/js/select2.js',
            'assets/js/highcharts/highcharts.js',
            'assets/js/highcharts/modules/exporting.js',
            'assets/js/grafico/linebasic.js',
            'assets/js/pages/projeto/visualizar.js'
        ));
    }

    public function novoAction() {
        $this->_helper->layout->disableLayout();
    }

    public function salvarAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelProjeto = new Application_Model_Projeto();
            if ($modelProjeto->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Projeto cadastrado.'));
            } else {
                echo "Não foi possível <strong>cadastrar</strong> o projeto.";
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelProjeto = new Application_Model_Projeto();
            $this->data['ativo'] = 0;
            if ($modelProjeto->save($this->data)) {
                echo json_encode(array('title' => '<strong>Sucesso</strong>','text' => 'Projeto removido.'));
            } else {
                echo "Não foi possível <strong>remover</strong> o projeto.";
            }
        }
    }

    public function carregarAction() {
        $this->_helper->layout->disableLayout();

        $modelProjeto = new Application_Model_Projeto();
        $this->view->projetos = $modelProjeto->getProjetos();
    }

    public function simularAction() {
        $this->_helper->layout->disableLayout();

        $id = (int) $this->_request->getParam("id",0);
        $modelProjeto = new Application_Model_Projeto();
        $projeto = $modelProjeto->find($id);

        if ($projeto) {
            $modelVariavel = new Application_Model_Variavel();
            $modelTermo = new Application_Model_Termo();

            $variaveis = $modelVariavel->getVariaveis($id);

            $retornoVariaveis = array();
            foreach ($variaveis as $variavel) {
                $retornoVariaveis[] = (object) array('id' => $variavel->id,
                                                     'nome' => $variavel->nome,
                                                     'inicio_universo' => $variavel->inicio_universo,
                                                     'fim_universo' => $variavel->fim_universo,
                                                     'unidade_medida' => $variavel->unidade_medida,
                                                     'objetiva' => $variavel->objetiva,
                                                     'termos' => $modelTermo->getTermos($variavel->id));
            }
            $this->view->variaveis = $retornoVariaveis;

            $modelRegra = new Application_Model_Regra();
            $regras = $modelRegra->getRegras($id);

            $modelRegraTermoAntecedente = new Application_Model_RegraTermoAntecedente();

            $retornoRegras = array();
            foreach ($regras as $regra) {
                $termosAntecedentes = $modelRegraTermoAntecedente->getTermosAntecedentes($regra->id);

                $antecedentes = array();
                foreach ($termosAntecedentes as $termoAntecedente) {
                    $antecedentes[] = "<b>".$termoAntecedente->variavel." é ".$termoAntecedente->termo_antecedente."</b>";
                }

                $retornoRegras[] = (object) array('id' => $regra->id,
                                                  'descricao' => "Se ".implode(" ".$regra->operador." ", $antecedentes)." então <b>".$regra->variavel." é ".$regra->termo_consequente."</b>.");
            }
            $this->view->regras = $retornoRegras;
        }
    }

    public function inferirAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->isPost()) {
            $modelProjeto = new Application_Model_Projeto();
            echo json_encode($modelProjeto->inferir($this->data));
        }
    }
}