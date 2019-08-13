<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Usuario/Model/Funcionario.php';

class Usuario_Model_FuncionarioDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Usuario_Model_Funcionario';

    protected $_tableName = 'funcionario';

    protected $_tableSchema = 'portal';

    protected $_attributeMap = [
        'ref_cod_pessoa_fj' => 'ref_cod_pessoa_fj',
        'matricula' => 'matricula',
        'senha' => 'senha',
        'ativo' => 'ativo',
        'ref_sec' => 'ref_sec',
        'sequencial' => 'sequencial',
        'opcao_menu' => 'opcao_menu',
        'ref_cod_setor' => 'ref_cod_setor',
        'ref_cod_funcionario_vinculo' => 'ref_cod_funcionario_vinculo',
        'tempo_expira_senha' => 'tempo_expira_senha',
        'data_expiracao' => 'data_expiracao',
        'data_troca_senha' => 'data_troca_senha',
        'data_reativa_conta' => 'data_reativa_conta',
        'ref_ref_cod_pessoa_fj' => 'ref_ref_cod_pessoa_fj',
        'ref_cod_setor_new' => 'ref_cod_setor_new',
        'matricula_new' => 'matricula_new',
        'tipo_menu' => 'tipo_menu',
        'ip_logado' => 'ip_logado',
        'data_login' => 'data_login',
        'email' => 'email',
        'status_token' => 'status_token',
        'matricula_interna' => 'matricula_interna',
        'receber_novidades' => 'receber_novidades',
        'atualizou_cadastro' => 'atualizou_cadastro',
    ];

    protected $_primaryKey = [
        'ref_cod_pessoa_fj' => 'ref_cod_pessoa_fj'
    ];
}
