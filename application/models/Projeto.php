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
        $modelVariavel = new Application_Model_Variavel();
        $modelTermo = new Application_Model_Termo();
        $modelRegra = new Application_Model_Regra();
        $modelRegraTermoAntecedente = new Application_Model_RegraTermoAntecedente();

        $idProjeto = $data['id_projeto'];
        $valoresVariaveis = array_filter($data['variaveis'], 'strlen');

        $regras = $modelRegra->getRegras($idProjeto);

        $pertinenciasTermosConsequentes = array();
        $retorno = array();
        foreach ($regras as $regra) {
            $termosAntecedentes = $modelRegraTermoAntecedente->getTermosAntecedentes($regra->id);

            $antecedentes = array();
            $pertinenciasTermosRegra = array();
            foreach ($termosAntecedentes as $termoAntecedente) {
                $pertinencia = Aplicacao_Plugins_Util::calcularPertinencia($valoresVariaveis[$termoAntecedente->id_variavel], $termoAntecedente);
                $pertinenciasTermosRegra[] = $pertinencia;
                $antecedentes[] = "<b>".$termoAntecedente->variavel." é ".$termoAntecedente->termo_antecedente." (".$pertinencia.")</b>";
            }
            $pertinenciaRegra = $regra->operador === 'E' ? min($pertinenciasTermosRegra) : max($pertinenciasTermosRegra);
            $pertinenciasTermosConsequentes[$regra->id_variavel_objetiva][$regra->id_termo_consequente] = isset($pertinenciasTermosConsequentes[$regra->id_variavel_objetiva][$regra->id_termo_consequente]) ? max(array($pertinenciasTermosConsequentes[$regra->id_variavel_objetiva][$regra->id_termo_consequente], $pertinenciaRegra)) : $pertinenciaRegra;

            $retorno['regras'][] = "Se ".implode(" ".$regra->operador." ", $antecedentes)." então <b>".$regra->variavel_objetiva." é ".$regra->termo_consequente." (".$pertinenciaRegra.")</b>.";
            $retorno['pertinencias'][$regra->id_termo_consequente] = "<b>".$regra->variavel_objetiva." (".$regra->termo_consequente.") = ".$pertinenciasTermosConsequentes[$regra->id_variavel_objetiva][$regra->id_termo_consequente]."</b>";
        }
        $termosConsequentes = $modelTermo->getAllTermosConsequentes($idProjeto);
        $retorno['centroide'] = Aplicacao_Plugins_Util::calcularCentroide($termosConsequentes, $pertinenciasTermosConsequentes);
        $retorno['unidademedida'] = $regra->sigla_unidade_medida;
    
        $dadosGrafico = $modelVariavel->geraDadosGraficos($regra->id_variavel_objetiva, true);

        array_push($dadosGrafico['series'], Aplicacao_Plugins_Util::areaCentroideGrafico($termosConsequentes, $pertinenciasTermosConsequentes));
        array_push($dadosGrafico['series'], Aplicacao_Plugins_Util::pontoCentroideGrafico($termosConsequentes, $pertinenciasTermosConsequentes, $retorno['centroide']));

        $retorno['grafico'] = $dadosGrafico;
        return $retorno;
    }
}