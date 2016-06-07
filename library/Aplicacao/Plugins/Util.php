<?php

class Aplicacao_Plugins_Util extends Zend_Controller_Plugin_Abstract
{
    public static function calcularPertinencia($valor, $termo) {
        $pertinencia = null;
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
}