<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchDocumentosAtestadoVaga extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = [
                'certidao_nasci' => 'Certidão de nascimento e/ou carteira de identidade',
                'comprovante_resi' => 'Comprovante de residência',
                'foto_3_4' => 'Foto 3/4',
                'historico_escola' => 'Histórico escolar original',
                'atestado_frequencia' => 'Atestado de frequência original',
                'atestado_transferencia' => 'Atestado de Transferência',
                'decla_vacina' => 'Declaração de vacina da unidade de sa&uacute;de original',
                'carteira_sus' => 'Carteira do SUS',
                'cartao_bolsa_fami' => 'Cópia do cartão bolsa família',
                'rg_aluno_pai' => 'Cópia do RG (aluno e pai)',
                'cpf_aluno_pai' => 'Cópia do CPF (aluno e pai)',
                'tit_eleitor' => 'Título de eleitor do responsável',
                'doc_nis' => 'N&uacute;mero de Identificação Social - NIS',
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
