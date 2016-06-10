<?php

class Application_Model_Variavel extends Application_Model_Abstract {

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Variavel();
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

    public function getVariaveis($idProjeto) {
        $select = $this->_dbTable->select();
        $select->from($this->_dbTable)
               ->where("id_projeto = {$idProjeto}")
               ->where("ativo = '1'")
               ->order(array("objetiva ASC",
                             "nome ASC"));
        return $this->_dbTable->fetchAll($select);
    }

    public function geraDadosGraficos($idVariavel, $graficoFinal=false) {

        $variavel = $this->find($idVariavel);

        $modelTermo = new Application_Model_Termo();
        $termos = $modelTermo->getTermos($idVariavel);

        $series = array();
        $escalaTermos = array();
        foreach ($termos as $termo) {
            $escalaTermos['name'] = $termo->nome;
            if ($graficoFinal === true) {
                $escalaTermos['enableMouseTracking'] = false;
            }
            for ($valor = (float) $variavel->inicio_universo; $valor <= (float) $variavel->fim_universo; $valor++) {
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
    }
}