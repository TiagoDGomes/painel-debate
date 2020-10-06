<?php

class Relogio
{
    public static function tempoServidorInt($tempoServidor = NULL)
    {
        if ($tempoServidor == NULL){
            $tempoServidor = Relogio::tempoCalculoReal();        
        }
        $tempoRetorno = null;
        if (Relogio::tempoSimulado()) {
            $m2 = explode(".", $tempoServidor . "");
            $md = substr($m2[1], 0, 3);
            switch (strlen($md)) {
                case 0:
                    $md = "000";
                case 1:
                    $md = $md . "00";
                case 2:
                    $md = $md . "0";
            }
            $tempoRetorno = (int) (substr($m2[0], -4) . $md);
        } else {
            $tempoRetorno = (int) ($tempoServidor * 1000);
        }
        return $tempoRetorno;
    }
    public static function tempoSimulado()
    {
        return PHP_INT_MAX <= 2147483647;
    }
    public static function tempoCalculoReal()
    {
        return microtime(1);
    }
    public static function tempoServidor()
    {
        if (Relogio::tempoSimulado()) {
            return Relogio::tempoServidorInt() / 1000;
        } else {
            return Relogio::tempoCalculoReal();
        }
    }
    
}
