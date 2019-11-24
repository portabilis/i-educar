<?php

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';

class Portabilis_View_Helper_Input_Resource_MultipleSearchDocumentosAtestadoVaga extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = [
                'certidao_nasci' => 'Certid&atilde;o de nascimento e/ou carteira de identidade',
                'comprovante_resi' => 'Comprovante de resid&ecirc;ncia',
                'foto_3_4' => 'Foto 3/4',
                'historico_escola' => 'Hist&oacute;rico escolar original',
                'atestado_frequencia' => 'Atestado de frequ&ecirc;ncia original',
                'atestado_transferencia' => 'Atestado de Transfer&ecirc;ncia',
                'decla_vacina' => 'Declara&ccedil;&atilde;o de vacina da unidade de sa&uacute;de original',
                'carteira_sus' => 'Carteira do SUS',
                'cartao_bolsa_fami' => 'C&oacute;pia do cart&atilde;o bolsa fam&iacute;lia',
                'rg_aluno_pai' => 'C&oacute;pia do RG (aluno e pai)',
                'cpf_aluno_pai' => 'C&oacute;pia do CPF (aluno e pai)',
                'tit_eleitor' => 'T&iacute;tulo de eleitor do respons&aacute;vel',
                'doc_nis' => 'N&uacute;mero de Identifica&ccedil;&atilde;o Social - NIS',
                'responsabilidade_nao_genitor' => 'Documento de responsável não genitor',
                'intolerancia_alergia' => 'Laudo médico para intolerância ou alergia',
                'tipo_sanguineo' => 'Tipo Sanguíneo',
            ];
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchDocumentosAtestadoVaga($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'documentos',
            'apiController' => '',
            'apiResource' => ''
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        $options['options']['resources'] = $this->getOptions($options['options']['resources']);

        $this->placeholderJs($options);

        parent::multipleSearch($options['objectName'], $attrName, $options);
    }

    protected function placeholderJs($options)
    {
        $optionsVarName = 'multipleSearch' . Portabilis_String_Utils::camelize($options['objectName']) . 'Options';
        $js = "
            if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
            $optionsVarName.placeholder = safeUtf8Decode('Selecione os componentes');
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
