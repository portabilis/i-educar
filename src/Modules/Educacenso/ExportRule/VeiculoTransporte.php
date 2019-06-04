<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;

class VeiculoTransporte implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        if (empty($registro60->transportePublico)) {
            $registro60->veiculoTransporteBicicleta = null;
            $registro60->veiculoTransporteMicroonibus = null;
            $registro60->veiculoTransporteOnibus = null;
            $registro60->veiculoTransporteTracaoAnimal = null;
            $registro60->veiculoTransporteVanKonbi = null;
            $registro60->veiculoTransporteOutro = null;
            $registro60->veiculoTransporteAquaviarioCapacidade5 = null;
            $registro60->veiculoTransporteAquaviarioCapacidade5a15 = null;
            $registro60->veiculoTransporteAquaviarioCapacidade15a35 = null;
            $registro60->veiculoTransporteAquaviarioCapacidadeAcima35 = null;
        }

        return $registro60;
    }
}
