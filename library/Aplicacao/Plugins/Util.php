<?php

class Aplicacao_Plugins_Util extends Zend_Controller_Plugin_Abstract
{
    public static function calcularPertinencia($valor, $termo) {
        $pertinencia = 0;
        if ($valor >= $termo->inicio_suporte && $valor <= $termo->fim_suporte) {
            if ($valor >= $termo->inicio_nucleo && $valor <= $termo->fim_nucleo) {
                $pertinencia = 1;
            } else {
                if ($valor >= $termo->inicio_suporte && $valor < $termo->inicio_nucleo) {
                    $pertinencia = round(($valor - $termo->inicio_suporte) / ($termo->inicio_nucleo - $termo->inicio_suporte), 2);
                } else {
                    $pertinencia = round(($termo->fim_suporte - $valor) / ($termo->fim_suporte - $termo->fim_nucleo), 2);
                }
            }
        }
        return $pertinencia;
    }

    public static function calcularCentroide($termosConsequentes, $pertinenciasTermosConsequentes) {
        $pertinencias = array();
        $maxPertinencias = array();
        foreach ($termosConsequentes as $termo) {
            if ($pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id] > 0) {
                $intervalo = ($termo->fim_universo - $termo->inicio_universo) / 20;
                for ($i = $termo->inicio_universo + $intervalo; $i <= $termo->fim_universo; $i+=$intervalo) {

                    if ($i > $termo->inicio_suporte && $i < $termo->fim_suporte) {
                        if (isset($maxPertinencias[$i])) {
                            $pertinencia = self::calcularPertinencia($i, $termo);
                            if ($pertinencia > $maxPertinencias[$i]) {
                                $maxPertinencias[$i] = $pertinencia;
                                $pertinencias[$i] = $pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id];
                            }
                        } else {
                            $maxPertinencias[$i] = self::calcularPertinencia($i, $termo);
                            $pertinencias[$i] = $pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id];
                        }
                    }

                }
            }
        }
        unset($maxPertinencias);

        if (count($pertinencias) > 0) {
            $dividendo = 0;
            $divisor = 0;
            foreach ($pertinencias as $indice => $pertinencia) {
                $dividendo += $indice * $pertinencia;
                $divisor += $pertinencia;
            }
            return round($dividendo / $divisor, 2);
        }  else {
            return 0;
        }
    }

    public static function areaCentroideGrafico($termosConsequentes, $pertinenciasTermosConsequentes) {
        $area = array();
        foreach ($termosConsequentes as $termo) {
            if ($pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id] > 0) {
                
                for ($i = (float) $termo->inicio_universo; $i <= (float) $termo->fim_universo; $i++) {

                    if ($i >= $termo->inicio_suporte && $i <= $termo->fim_suporte) {
                        $pertinencia = self::calcularPertinencia($i, $termo);
                        if ($pertinencia < $pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id]) {
                            $area['data'][] = array($i , 0, $pertinencia);
                        } else {
                            $area['data'][] = array($i , 0, $pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id]);
                        }
                    }
                }
            }
        }
        $area['name'] = 'Área Centróide';
        $area['type'] = 'arearange';
        $area['fillOpacity'] = 0.5;
        $area['lineWidth'] = 0;
        $area['enableMouseTracking'] = false;
        return $area;
    }

    public static function pontoCentroideGrafico($termosConsequentes, $pertinenciasTermosConsequentes, $centroide) {
        $pertinencias = array();
        foreach ($termosConsequentes as $termo) {
            if ($pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id] > 0) {
                $pertinencias[] = $pertinenciasTermosConsequentes[$termo->id_variavel][$termo->id];
            }
        }
        $scatter = array();
        $scatter['name'] = 'Centróide';
        $scatter['type'] = 'scatter';
        $scatter['marker'] = array('enabled' => true);
        $scatter['data'][] = array($centroide, round((array_sum($pertinencias) / count($pertinencias)) / 3, 2));
        return $scatter;
    }
}