<?php

class Application_Model_Projeto extends Application_Model_Abstract {

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Projeto();
    }

    public function _insert(array $data) {
        return $this->_dbTable->insert($data);
    }

    public function _update(array $data) {
        return $this->_dbTable->update($data, array('id=?'=>$data['id']));
    }

    public function _delete(array $data) {
        return $this->_dbTable->delete(array('id=?'=>$data['id']));
    }

    public function getProjetos() {
        $select = $this->_dbTable->select();
        $select->from($this->_dbTable)
               ->where("ativo = '1'")
               ->order("nome ASC");
        return $this->_dbTable->fetchAll($select);
    }

    public function inferir(array $data) {

        $idProjeto = $data['id_projeto'];
        $valoresVariaveis = array_filter($data['variaveis']);

        echo "<pre>";
        print_r($valoresVariaveis);
        die();

        $modelVariavel = new Application_Model_Variavel();
        $modelTermo = new Application_Model_Termo();

        $variaveis = $modelVariavel->getVariaveis($idProjeto);

        foreach ($variaveis as $variavel) {
            $termos = $modelTermo->getTermos($variavel->id);


        }



        $series = array();
        $escalaTermos = array();
        foreach ($termos as $termo) {
            $escalaTermos['name'] = $termo->nome;
            for ($valor = $variavel->inicio_universo; $valor <= $variavel->fim_universo; $valor++) {
                $pertinencia = Aplicacao_Plugins_Util::calcularPertinencia($valor, $termo);
                $escalaTermos['data'][] = array($valor, $pertinencia);
            }
            $series[] = $escalaTermos;
            $escalaTermos = array();
        }

        if (empty($series)) {
            $series = array(array('name' => 'Sem termo', 'data' => null));
        }

        return array('variavel' => $variavel->nome,
                     'min' => $variavel->inicio_universo,
                     'max' => $variavel->fim_universo,
                     'series' => $series);


        return true;
    }
}