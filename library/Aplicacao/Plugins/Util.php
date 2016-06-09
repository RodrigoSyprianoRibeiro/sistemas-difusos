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
        $dividendo = 0;
        $divisor = 0;
        foreach ($pertinencias as $indice => $pertinencia) {
            $dividendo += $indice * $pertinencia;
            $divisor += $pertinencia;
        }
        return round($dividendo / $divisor, 2);
    }
}