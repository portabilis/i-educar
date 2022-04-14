<?php

class clsPmieducarConfiguracoesGerais
{
    public $ref_cod_instituicao;
    public $permite_relacionamento_posvendas;
    public $url_novo_educacao;
    public $token_novo_educacao = false;
    public $mostrar_codigo_inep_aluno;
    public $justificativa_falta_documentacao_obrigatorio;
    public $tamanho_min_rede_estadual;
    public $modelo_boletim_professor;
    public $custom_labels;
    public $url_cadastro_usuario;
    public $active_on_ieducar;
    public $ieducar_image;
    public $ieducar_entity_name;
    public $ieducar_login_footer;
    public $ieducar_external_footer;
    public $ieducar_internal_footer;
    public $facebook_url;
    public $twitter_url;
    public $linkedin_url;
    public $ieducar_suspension_message;
    public $bloquear_cadastro_aluno;
    public $situacoes_especificas_atestados;
    public $emitir_ato_autorizativo;
    public $emitir_ato_criacao_credenciamento;

    /**
     * Armazena o total de resultados obtidos na última chamada ao método lista().
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema.
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela.
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por vírgula, com os campos que devem ser selecionados na
     * próxima chamado ao método lista().
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por vírgula, padrão para
     * seleção no método lista.
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo método lista().
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no método lista().
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo para ser usado como padrão de ordenação no método lista().
     *
     * @var string
     */
    public $_campo_order_by;

    /**
     * Define o campo para ser usado como padrão de agrupamento no método lista().
     *
     * @var string
     */
    public $_campo_group_by;

    public function __construct($ref_cod_instituicao = null, $campos = [])
    {
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'configuracoes_gerais';

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_instituicao, permite_relacionamento_posvendas,
        url_novo_educacao, token_novo_educacao, mostrar_codigo_inep_aluno, justificativa_falta_documentacao_obrigatorio,
        tamanho_min_rede_estadual, modelo_boletim_professor, custom_labels, url_cadastro_usuario,
        active_on_ieducar, ieducar_image, ieducar_entity_name, ieducar_login_footer,
        ieducar_external_footer, ieducar_internal_footer, facebook_url, twitter_url, linkedin_url,
        ieducar_suspension_message, bloquear_cadastro_aluno, situacoes_especificas_atestados, emitir_ato_autorizativo,
        emitir_ato_criacao_credenciamento';

        if (is_numeric($campos['ref_cod_instituicao'] ?? null)) {
            $this->ref_cod_instituicao = $campos['ref_cod_instituicao'];
        }

        if (is_numeric($campos['permite_relacionamento_posvendas'] ?? null)) {
            $this->permite_relacionamento_posvendas = $campos['permite_relacionamento_posvendas'];
        }

        if (!empty($campos['url_novo_educacao'])) {
            $this->url_novo_educacao = $campos['url_novo_educacao'];
        }

        if (!empty($campos['token_novo_educacao'])) {
            $this->token_novo_educacao = $campos['token_novo_educacao'];
        }

        if (is_numeric($campos['mostrar_codigo_inep_aluno'] ?? null)) {
            $this->mostrar_codigo_inep_aluno = $campos['mostrar_codigo_inep_aluno'];
        }

        if (is_numeric($campos['justificativa_falta_documentacao_obrigatorio'] ?? null)) {
            $this->justificativa_falta_documentacao_obrigatorio = $campos['justificativa_falta_documentacao_obrigatorio'];
        }

        if (!empty($campos['tamanho_min_rede_estadual'])) {
            $this->tamanho_min_rede_estadual = $campos['tamanho_min_rede_estadual'];
        }

        if (!empty($campos['modelo_boletim_professor']) && is_numeric($campos['modelo_boletim_professor'])) {
            $this->modelo_boletim_professor = $campos['modelo_boletim_professor'];
        }

        if (!empty($campos['custom_labels'])) {
            $this->custom_labels = $campos['custom_labels'];
        }

        if (!empty($campos['url_cadastro_usuario'])) {
            $this->url_cadastro_usuario = $campos['url_cadastro_usuario'];
        }

        if (is_numeric($campos['active_on_ieducar'] ?? null)) {
            $this->active_on_ieducar = $campos['active_on_ieducar'];
        }

        if (isset($campos['ieducar_image'])) {
            $this->ieducar_image = $campos['ieducar_image'];
        }

        if (!empty($campos['ieducar_entity_name'])) {
            $this->ieducar_entity_name = $campos['ieducar_entity_name'];
        }

        if (isset($campos['ieducar_login_footer'])) {
            $this->ieducar_login_footer = $campos['ieducar_login_footer'];
        }

        if (isset($campos['ieducar_external_footer'])) {
            $this->ieducar_external_footer = $campos['ieducar_external_footer'];
        }

        if (isset($campos['ieducar_internal_footer'])) {
            $this->ieducar_internal_footer = $campos['ieducar_internal_footer'];
        }

        if (isset($campos['facebook_url'])) {
            $this->facebook_url = $campos['facebook_url'];
        }

        if (isset($campos['twitter_url'])) {
            $this->twitter_url = $campos['twitter_url'];
        }

        if (isset($campos['linkedin_url'])) {
            $this->linkedin_url = $campos['linkedin_url'];
        }

        if (!empty($campos['ieducar_suspension_message'])) {
            $this->ieducar_suspension_message = $campos['ieducar_suspension_message'];
        }

        if (isset($campos['bloquear_cadastro_aluno'])) {
            $this->bloquear_cadastro_aluno = boolval($campos['bloquear_cadastro_aluno']);
        }

        if (isset($campos['situacoes_especificas_atestados'])) {
            $this->situacoes_especificas_atestados = boolval($campos['situacoes_especificas_atestados']);
        }

        if (isset($campos['emitir_ato_autorizativo'])) {
            $this->emitir_ato_autorizativo = boolval($campos['emitir_ato_autorizativo']);
        }

        if (isset($campos['emitir_ato_criacao_credenciamento'])) {
            $this->emitir_ato_criacao_credenciamento = boolval($campos['emitir_ato_criacao_credenciamento']);
        }
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        $db = new clsBanco();
        $set = [];

        if (is_numeric($this->permite_relacionamento_posvendas)) {
            $set[] = "permite_relacionamento_posvendas = '{$this->permite_relacionamento_posvendas}'";
        }

        if (is_numeric($this->ref_cod_instituicao)) {
            $ref_cod_instituicao = $this->ref_cod_instituicao;
        } else {
            $ref_cod_instituicao = $this->getUltimaInstituicaoAtiva();
        }

        if (!empty($this->url_novo_educacao)) {
            $set[] = "url_novo_educacao = '{$this->url_novo_educacao}'";
        }

        if ($this->token_novo_educacao !== false) {
            $set[] = "token_novo_educacao = '{$this->token_novo_educacao}'";
        } else {
            $set[] = 'token_novo_educacao = NULL';
        }

        if (is_array($this->custom_labels)) {
            $customLabels = SafeJson::encode($this->custom_labels);
            $set[] = "custom_labels = '{$customLabels}'";
        }

        if (is_numeric($this->mostrar_codigo_inep_aluno)) {
            $set[] = "mostrar_codigo_inep_aluno = '{$this->mostrar_codigo_inep_aluno}'";
        }

        if (is_numeric($this->justificativa_falta_documentacao_obrigatorio)) {
            $set[] = "justificativa_falta_documentacao_obrigatorio = '{$this->justificativa_falta_documentacao_obrigatorio}'";
        }

        if ($this->tamanho_min_rede_estadual == '') {
            $this->tamanho_min_rede_estadual = 'NULL';
        }

        $set[] = "tamanho_min_rede_estadual = {$this->tamanho_min_rede_estadual}";

        if (is_numeric($this->modelo_boletim_professor)) {
            $set[] = "modelo_boletim_professor = '{$this->modelo_boletim_professor}'";
        }

        if (!empty($this->url_cadastro_usuario)) {
            $set[] = "url_cadastro_usuario = '{$this->url_cadastro_usuario}'";
        }

        if (is_numeric($this->active_on_ieducar)) {
            $set[] = "active_on_ieducar = '{$this->active_on_ieducar}'";
        }

        if (isset($this->ieducar_image)) {
            $set[] = "ieducar_image = '{$this->ieducar_image}'";
        }

        if (!empty($this->ieducar_entity_name)) {
            $set[] = "ieducar_entity_name = '{$this->ieducar_entity_name}'";
        }

        if (isset($this->ieducar_login_footer)) {
            $set[] = "ieducar_login_footer = '{$this->ieducar_login_footer}'";
        }

        if (isset($this->ieducar_external_footer)) {
            $set[] = "ieducar_external_footer = '{$this->ieducar_external_footer}'";
        }

        if (isset($this->ieducar_internal_footer)) {
            $set[] = "ieducar_internal_footer = '{$this->ieducar_internal_footer}'";
        }

        if (isset($this->facebook_url)) {
            $set[] = "facebook_url = '{$this->facebook_url}'";
        }

        if (isset($this->twitter_url)) {
            $set[] = "twitter_url = '{$this->twitter_url}'";
        }

        if (isset($this->linkedin_url)) {
            $set[] = "linkedin_url = '{$this->linkedin_url}'";
        }

        if (!empty($this->ieducar_suspension_message)) {
            $set[] = "ieducar_suspension_message = '{$this->ieducar_suspension_message}'";
        }

        if (isset($this->bloquear_cadastro_aluno)) {
            $flag = $this->bloquear_cadastro_aluno ? 'true' : 'false';
            $set[] = "bloquear_cadastro_aluno = {$flag}";
        }

        if (isset($this->situacoes_especificas_atestados)) {
            $flag = $this->situacoes_especificas_atestados ? 'true' : 'false';
            $set[] = "situacoes_especificas_atestados = {$flag}";
        }

        if (isset($this->emitir_ato_autorizativo)) {
            $flag = $this->emitir_ato_autorizativo ? 'true' : 'false';
            $set[] = "emitir_ato_autorizativo = {$flag}";
        }

        if (isset($this->emitir_ato_criacao_credenciamento)) {
            $flag = $this->emitir_ato_criacao_credenciamento ? 'true' : 'false';
            $set[] = "emitir_ato_criacao_credenciamento = {$flag}";
        }

        if (!empty($set)) {
            $set = join(', ', $set);
            $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_instituicao = '{$ref_cod_instituicao}'");

            return true;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_instituicao)) {
            $ref_cod_instituicao = $this->ref_cod_instituicao;
        } else {
            $ref_cod_instituicao = $this->getUltimaInstituicaoAtiva();
        }

        $db = new clsBanco();
        $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$ref_cod_instituicao}'");
        $db->ProximoRegistro();
        $record = $db->Tupla();

        if (!empty($record['custom_labels'])) {
            $record['custom_labels'] = json_decode($record['custom_labels'], true);
        }

        return $record;
    }

    public function getUltimaInstituicaoAtiva()
    {
        $db = new clsBanco();
        $db->Consulta('SELECT cod_instituicao
                     FROM pmieducar.instituicao
                    WHERE ativo = 1
                    ORDER BY cod_instituicao DESC LIMIT 1');
        $db->ProximoRegistro();
        $instituicao = $db->Tupla();

        return $instituicao[0];
    }
}
