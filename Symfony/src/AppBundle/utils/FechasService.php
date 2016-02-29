<?php

namespace AppBundle\Utils;


class FechasService
{
    public function __construct()
    {}

    /**
     *
     * Pasa fecha en formato de texto yyyymmdd a un objeto DateTime
     *
     * @param $str
     * @return \DateTime|null
     */
    public function getDateTimeToStr($str)
    {
        if(strlen($str) == 10)
            $str = str_replace("-","",$str);

        $result = null;
        if (strlen($str) == 8) { // YYYYMMDD
            $dia = intval(substr($str, 0, 2));
            $mes = intval(substr($str, 2, 2));
            $ano = intval(substr($str, 4, 4));

            $result = new \DateTime();
            $result->setDate($ano, $mes, $dia);
            $result->setTime(0,0,0);

        } elseif (strlen($str) == 6) {// YYYYMM
            $ano = intval(substr($str, 0, 4));
            $mes = intval(substr($str, 4, 2));

            $result = new \DateTime();
            $result->setDate($ano, $mes, 1);
            $result->setTime(0,0,0);
        }

        return $result;
    }
}