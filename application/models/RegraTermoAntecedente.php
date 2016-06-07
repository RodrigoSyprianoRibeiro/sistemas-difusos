<?php

class Application_Model_RegraTermoAntecedente extends Application_Model_Abstract {

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_RegraTermoAntecedente();
    }

    public function _insert(array $data) {
        return $this->_dbTable->insert($data);
    }

    public function _update(array $data) {
        return $this->_dbTable->update($data, array('id=?'=>$data['id']));
    }

    public function _delete(array $data) {
        return $this->_dbTable->delete(array('id_regra=?'=>$data['id']));
    }

    public function getTermosAntecedentes($idRegra) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from($this->_dbTable,
                      array())
               ->join(array('t'=>'termo'),
                      't.id = regra_termo_antecedente.id_termo_antecedente',
                      't.nome AS termo_antecedente')
               ->join(array('v'=>'variavel'),
                      'v.id = t.id_variavel',
                      'v.nome AS variavel')
               ->where("regra_termo_antecedente.id_regra = {$idRegra}");
        return $this->_dbTable->fetchAll($select);
    }
}