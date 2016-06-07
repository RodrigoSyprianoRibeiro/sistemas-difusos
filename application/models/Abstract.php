<?php

abstract class Application_Model_Abstract {

    protected $_dbTable;

    public function find($id) {
        return $this->_dbTable->find($id)->current();
    }

    public function save(array $data) {
        if (isset($data['id']))
            return $this->_update($data);
        else
            return $this->_insert($data);
    }

    public function delete(array $data) {
        if (isset($data['id']))
            return $this->_delete($data);
    }

    public function getAll() {
        $select = $this->_dbTable->select();
        return $this->_dbTable->fetchAll($select);
    }

    public function countAll() {
        $select = $this->_dbTable->select();
        return (int) count($this->_dbTable->fetchAll($select));
    }

    public function busca($id) {
        $select = $this->_dbTable->select();
        $select->where("id = {$id}");
        return $this->_dbTable->fetchRow($select);
    }

    public function fetchAll($params=null) {
        $order = isset($params['order']) ? $params['order'] : '1 ASC';

        $select = $this->_dbTable->select();
        $select->order($order);

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($params['pagina']);
        return $paginator;
    }

    public function getDados(array $data, $select, $colunas) {

        /*
         * Ordering
         */
        $camposOrder = array();
        if (isset($data['iSortCol_0'])) {
            for ($i=0; $i<intval($data['iSortingCols']); $i++) {
                if ($data['bSortable_'.intval($data['iSortCol_'.$i])] == "true") {
                    $camposOrder[] = $colunas[intval($data['iSortCol_'.$i])]." ".($data['sSortDir_'.$i]==='asc' ? 'asc' : 'desc');
                }
            }
        }

        /*
         * Filtering
         */
        $camposWhere = array();
        if (isset($data['sSearch']) && $data['sSearch'] != "") {
            for ($i=0; $i<count($colunas); $i++) {
                $camposWhere[] = $colunas[$i]." LIKE '%".addslashes($data['sSearch'])."%'";
            }
        }

        /*
         * Individual column filtering
         */
//        for ($i=0; $i<count($colunas); $i++) {
//            if (isset($data['bSearchable_'.$i]) && $data['bSearchable_'.$i] == "true" && $data['sSearch_'.$i] != '') {
//                if ($sWhere == "") {
//                    $sWhere = "WHERE ";
//                } else {
//                    $sWhere .= " AND ";
//                }
//                $sWhere .= $colunas[$i]." LIKE '%".addslashes($data['sSearch_'.$i])."%' ";
//            }
//        }

        if (count($camposWhere) > 0) {
            $select->where('('.implode($camposWhere, ' OR ').')');
        }
        if (count($camposOrder) > 0) {
            $select->order($camposOrder);
        }

        $iTotalDisplayRecords = (int) count($this->_dbTable->fetchAll($select));

        /*
         * Paging
         */
        $select->limit((int) $data['iDisplayLength'], (int) $data['iDisplayStart']);

        return (object) array(
                            'iTotalDisplayRecords' => $iTotalDisplayRecords,
                            'dados' => $this->_dbTable->fetchAll($select));
    }

    abstract public function _insert(array $data);

    abstract public function _update(array $data);

    abstract public function _delete(array $data);
}