<?php

namespace iEducar\Modules\Educacenso\ExportRule;


use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Model\PaisResidencia;

class RegrasGeraisRegistro30 implements EducacensoExportRule
{
    /**
     * @param Registro30 $registro30
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro30): RegistroEducacenso
    {
        if ($registro30->nacionalidade == Nacionalidade::ESTRANGEIRA || $registro30->nacionalidade == Nacionalidade::NATURALIZADO_BRASILEIRO) {
            $registro30->municipioNascimento = null;
        }

        if (!$registro30->deficiencia) {
            $registro30->deficienciaCegueira = null;
            $registro30->deficienciaBaixaVisao = null;
            $registro30->deficienciaSurdez = null;
            $registro30->deficienciaAuditiva = null;
            $registro30->deficienciaSurdoCegueira = null;
            $registro30->deficienciaFisica = null;
            $registro30->deficienciaIntelectual = null;
            $registro30->deficienciaMultipla = null;
            $registro30->deficienciaAutismo = null;
            $registro30->deficienciaAltasHabilidades = null;
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
        }

        if (!$registro30->semDocumentacao()) {
            $registro30->justificativaFaltaDocumentacao = null;
        }

        if ($registro30->paisResidencia != PaisResidencia::BRASIL) {
            $registro30->paisResidencia = null;
            $registro30->cep = null;
            $registro30->municipioResidencia = null;
            $registro30->localizacaoResidencia = null;
            $registro30->localizacaoDiferenciada = null;
        }

        if ($registro30->escolaridade != Escolaridade::ENSINO_MEDIO) {
            $registro30->tipoEnsinoMedioCursado = null;
        }

        if ($registro30->escolaridade != Escolaridade::EDUCACAO_SUPERIOR) {
            $registro30->formacaoCurso = null;
            $registro30->formacaoAnoConclusao = null;
            $registro30->formacaoInstituicao = null;
            $registro30->formacaoComponenteCurricular = null;
            $registro30->posGraduacaoEspecializacao = null;
            $registro30->posGraduacaoDoutorado = null;
            $registro30->posGraduacaoMestrado = null;
            $registro30->posGraduacaoNaoPossui = null;
        }

        return $registro30;
    }
}
