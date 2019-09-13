<?php


function segundosToDataExtenso( $seg )
{
    $data = segundos2Data( $seg );

    $retorno = "";
    if( $data["dia"] )
    {
        $retorno .=( $data["dia"] > 1 ) ? "{$data["dia"]} Dias " : "{$data["dia"]} Dia ";
    }
    if( $data["hor"] )
    {
        $retorno .=( $data["hor"] < 10 ) ? "0{$data["hor"]}" : "{$data["hor"]}";
    }else
    {
        $retorno .="00";
    }
    if( $data["min"] )
    {
        $retorno .=( $data["min"] < 10 ) ? ":0{$data["min"]} " : ":{$data["min"]} ";
    }else
    {
        $retorno .=":00";
    }
    if( $data["seg"] )
    {
        $retorno .=( $data["seg"] < 10 ) ? ":0{$data["seg"]}" : ":{$data["seg"]}";
    }else
    {
        $retorno .=":00";
    }
    return $retorno;
}


function difTempo( $data_inicial, $data_final)
{
    $seg = $data_final - $data_inicial;
    return segundosToDataExtenso( $seg );
}

function segundos2Data( $seg )
{
    $retorno = array();
    $retorno["dia"] = floor( $seg / 86400 );
    $seg -= $retorno["dia"] * 86400;
    $retorno["hor"] = floor( $seg / 3600 );
    $seg -= $retorno["hor"] * 3600;
    $retorno["min"] = floor( $seg / 60 );
    $seg -= $retorno["min"] * 60;
    $retorno["seg"] = $seg;

    return $retorno;
}

function timeMakeArray( $listaAtividades )
{
    $retorno = array();
    if( $listaAtividades )
    {
        foreach( $listaAtividades as $atividade ) {
            $retorno = array_merge_recursive( horasUteisInterval( strtotime( $atividade['data_abertura'] ), strtotime( $atividade['data_fechamento'] ) ), $retorno) ;

        }
    }
    return $retorno;
}


function horasUteisInterval( $data_inicial, $data_final )
{
    $hora_inicial = date("H:i:s", $data_inicial);
    $hora_final  = date("H:i:s", $data_final);
    $data_inicial  = date("Y/m/d", $data_inicial);
    $data_final = date("Y/m/d", $data_final);

    if($data_inicial == $data_final)
    {
        $horas[ $data_inicial ][] = array("ini"=> $hora_inicial, "fim"=>$hora_final);
    }
    else
    {
        $horas[ $data_inicial ][] =     array("ini"=> $hora_inicial, "fim"=>"18:00");

        while ($data_inicial != $data_final) {
            $temp = explode("/",$data_inicial);
            $data_inicial = date( 'Y/m/d',mktime(0,0,0,$temp[1],$temp[2]+1,$temp[0]) );
            if($data_inicial == $data_final)
            {
                $horas[ $data_inicial ][] = array("ini"=> "08:00", "fim"=>$hora_final);
            }
            else
            {
                $horas[ $data_inicial ][] = array("ini"=> "08:00", "fim"=>"18:00");
            }

        }
    }
    return $horas;
}

function horasInterval( $data_inicial, $data_final )
{
    $hora_inicial = date("H:i:s", $data_inicial);
    $hora_final  = date("H:i:s", $data_final);
    $data_inicial  = date("Y/m/d", $data_inicial);
    $data_final = date("Y/m/d", $data_final);

    if($data_inicial == $data_final)
    {
        $horas[ $data_inicial ][] = array("ini"=> $hora_inicial, "fim"=>$hora_final);
    }
    else
    {
        $horas[ $data_inicial ][] =     array("ini"=> $hora_inicial, "fim"=>"23:59:59");

        while ($data_inicial != $data_final) {
            $temp = explode("/",$data_inicial);
            $data_inicial = date( 'Y/m/d',mktime(0,0,0,$temp[1],$temp[2]+1,$temp[0]) );
            if($data_inicial == $data_final)
            {
                $horas[ $data_inicial ][] = array("ini"=> "00:00", "fim"=>$hora_final);
            }
            else
            {
                $horas[ $data_inicial ][] = array("ini"=> "00:00", "fim"=>"23:59:59");
            }

        }
    }
    return $horas;
}



function difTempoHoras( $data_inicial, $data_final )
{
    $seg = $data_final - $data_inicial;
    $hor =  number_format($seg / 3600,2, ".",",") ;
    return $hor;
}
?>
