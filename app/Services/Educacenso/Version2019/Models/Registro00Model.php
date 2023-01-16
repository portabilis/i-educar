<?php

namespace App\Services\Educacenso\Version2019\Models;

use App\Models\Educacenso\Registro00;

class Registro00Model extends Registro00
{
    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->registro = $arrayColumns[1];
        $this->codigoInep = $arrayColumns[2];
        $this->situacaoFuncionamento = $arrayColumns[3];
        $this->inicioAnoLetivo = $arrayColumns[4];
        $this->fimAnoLetivo = $arrayColumns[5];
        $this->nome = $arrayColumns[6];
        $this->cep = $arrayColumns[7];
        $this->codigoIbgeMunicipio = $arrayColumns[8];
        $this->codigoIbgeDistrito = $arrayColumns[9];
        $this->logradouro = $arrayColumns[10];
        $this->numero = $arrayColumns[11];
        $this->complemento = $arrayColumns[12];
        $this->bairro = $arrayColumns[13];
        $this->ddd = $arrayColumns[14];
        $this->telefone = $arrayColumns[15];
        $this->telefoneOutro = $arrayColumns[16];
        $this->email = $arrayColumns[17];
        $this->orgaoRegional = $arrayColumns[18];
        $this->zonaLocalizacao = $arrayColumns[19];
        $this->localizacaoDiferenciada = $arrayColumns[20];
        $this->dependenciaAdministrativa = $arrayColumns[21];
        $this->orgaoEducacao = $arrayColumns[22];
        $this->orgaoSeguranca = $arrayColumns[23];
        $this->orgaoSaude = $arrayColumns[24];
        $this->orgaoOutro = $arrayColumns[25];
        $this->mantenedoraEmpresa = $arrayColumns[26];
        $this->mantenedoraSindicato = $arrayColumns[27];
        $this->mantenedoraOng = $arrayColumns[28];
        $this->mantenedoraInstituicoes = $arrayColumns[29];
        $this->mantenedoraSistemaS = $arrayColumns[30];
        $this->mantenedoraOscip = $arrayColumns[31];
        $this->categoriaEscolaPrivada = $arrayColumns[32];
        $this->conveniadaPoderPublico = $arrayColumns[33];
        $this->cnpjMantenedoraPrincipal = $arrayColumns[34];
        $this->cnpjEscolaPrivada = $arrayColumns[35];
        $this->regulamentacao = $arrayColumns[36];
        $this->esferaFederal = $arrayColumns[37];
        $this->esferaEstadual = $arrayColumns[38];
        $this->esferaMunicipal = $arrayColumns[39];
        $this->unidadeVinculada = $arrayColumns[40];
        $this->inepEscolaSede = $arrayColumns[41];
        $this->codigoIes = $arrayColumns[42];
    }
}
