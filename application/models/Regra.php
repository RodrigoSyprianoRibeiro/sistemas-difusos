<?php

class Application_Model_Regra extends Application_Model_Abstract {

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Regra();
    }

    public function _insert(array $data) {
        $termosAntecedentes = $data['id_termos_antecedentes'];
        unset($data['id_termos_antecedentes']);

        $data['id_regra'] = $this->_dbTable->insert($data);

        unset($data['id_projeto']);
        unset($data['operador']);
        unset($data['id_termo_consequente']);

        $modelRegraTermoAntecedente = new Application_Model_RegraTermoAntecedente();
        foreach ($termosAntecedentes as $termoAntecedente) {
            $data['id_termo_antecedente'] = $termoAntecedente;
            $modelRegraTermoAntecedente->save($data);
        }

        return $data['id_regra'];
    }

    public function _update(array $data) {
        return $this->_dbTable->update($data, array('id=?'=>$data['id']));
    }

    public function _delete(array $data) {
        $modelRegraTermoAntecedente = new Application_Model_RegraTermoAntecedente();
        $modelRegraTermoAntecedente->delete($data);
        return $this->_dbTable->delete(array('id=?'=>$data['id']));
    }

    public function getRegras($idProjeto) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from($this->_dbTable)
               ->join(array('t'=>'termo'),
                      't.id = regra.id_termo_consequente',
                      't.nome AS termo_consequente')
               ->join(array('v'=>'variavel'),
                      'v.id = t.id_variavel',
                      'v.nome AS variavel')
               ->where("regra.id_projeto = {$idProjeto}")
               ->order("regra.id ASC");
        return $this->_dbTable->fetchAll($select);
    }
}