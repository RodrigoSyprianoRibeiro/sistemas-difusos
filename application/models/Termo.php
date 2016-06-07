<?php

class Application_Model_Termo extends Application_Model_Abstract {

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Termo();
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

    public function getTermos($idVariavel) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from($this->_dbTable)
               ->join(array('v'=>'variavel'),
                      'v.id = termo.id_variavel',
                      array('v.nome AS variavel'))
               ->where("termo.id_variavel = {$idVariavel}")
               ->where("termo.ativo = '1'")
               ->order("termo.inicio_suporte ASC");
        return $this->_dbTable->fetchAll($select);
    }

    public function getAllTermosAntecedentes($idProjeto) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from($this->_dbTable)
               ->join(array('v'=>'variavel'),
                      'v.id = termo.id_variavel',
                      array('v.nome AS variavel'))
               ->where("v.id_projeto = {$idProjeto}")
               ->where("v.objetiva = '0'")
               ->where("v.ativo = '1'")
               ->where("termo.ativo = '1'")
               ->order(array("v.nome ASC",
                             "termo.inicio_suporte ASC"));
        return $this->_dbTable->fetchAll($select);
    }

    public function getAllTermosConsequentes($idProjeto) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from($this->_dbTable)
               ->join(array('v'=>'variavel'),
                      'v.id = termo.id_variavel',
                      array('v.nome AS variavel'))
               ->where("v.id_projeto = {$idProjeto}")
               ->where("v.objetiva = '1'")
               ->where("v.ativo = '1'")
               ->where("termo.ativo = '1'")
               ->order(array("v.nome ASC",
                             "termo.inicio_suporte ASC"));
        return $this->_dbTable->fetchAll($select);
    }
}