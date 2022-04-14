<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;

class RegrasEspecificasRegistro30 implements EducacensoExportRule
{
    /**
     * @param Registro30 $registro30
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro30): RegistroEducacenso
    {
        if (!$registro30->isStudent()) {
            $registro30->recursoLedor = null;
            $registro30->recursoTranscricao = null;
            $registro30->recursoGuia = null;
            $registro30->recursoTradutor = null;
            $registro30->recursoLeituraLabial = null;
            $registro30->recursoProvaAmpliada = null;
            $registro30->recursoProvaSuperampliada = null;
            $registro30->recursoAudio = null;
            $registro30->recursoLinguaPortuguesaSegundaLingua = null;
            $registro30->recursoVideoLibras = null;
            $registro30->recursoBraile = null;
            $registro30->recursoNenhum = null;
            $registro30->nis = null;
            $registro30->certidaoNascimento = null;
            $registro30->justificativaFaltaDocumentacao = null;
        }

        if ($registro30->isManager() && !$registro30->isTeacher()) {
            $registro30->paisResidencia = null;
            $registro30->cep = null;
            $registro30->municipioResidencia = null;
            $registro30->localizacaoResidencia = null;
            $registro30->localizacaoDiferenciada = null;
        }

        if ($registro30->isStudent()) {
            $registro30->escolaridade = null;
            $registro30->tipoEnsinoMedioCursado = null;
            $registro30->formacaoCurso = null;
            $registro30->formacaoAnoConclusao = null;
            $registro30->formacaoInstituicao = null;

            $registro30->posGraduacaoEspecializacao = null;
            $registro30->posGraduacaoMestrado = null;
            $registro30->posGraduacaoDoutorado = null;
            $registro30->posGraduacaoNaoPossui = null;
            $registro30->formacaoContinuadaCreche = null;
            $registro30->formacaoContinuadaPreEscola = null;
            $registro30->formacaoContinuadaAnosIniciaisFundamental = null;
            $registro30->formacaoContinuadaAnosFinaisFundamental = null;
            $registro30->formacaoContinuadaEnsinoMedio = null;
            $registro30->formacaoContinuadaEducacaoJovensAdultos = null;
            $registro30->formacaoContinuadaEducacaoEspecial = null;
            $registro30->formacaoContinuadaEducacaoIndigena = null;
            $registro30->formacaoContinuadaEducacaoCampo = null;
            $registro30->formacaoContinuadaEducacaoAmbiental = null;
            $registro30->formacaoContinuadaEducacaoDireitosHumanos = null;
            $registro30->formacaoContinuadaGeneroDiversidadeSexual = null;
            $registro30->formacaoContinuadaDireitosCriancaAdolescente = null;
            $registro30->formacaoContinuadaEducacaoRelacoesEticoRaciais = null;
            $registro30->formacaoContinuadaEducacaoGestaoEscolar = null;
            $registro30->formacaoContinuadaEducacaoOutros = null;
            $registro30->formacaoContinuadaEducacaoNenhum = null;
        }

        if (!$registro30->isTeacher()) {
            $registro30->formacaoComponenteCurricular = null;
        }

        if (!$registro30->isManager()) {
            $registro30->email = null;
        }

        return $registro30;
    }
}
