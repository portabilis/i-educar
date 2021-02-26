<?php

$desvio_diretorio = '';


return new class extends clsCadastro
{
    public $p_cod_pessoa_fj;
    public $f_senha;
    public $f_senha2;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->p_cod_pessoa_fj = $this->pessoa_logada;

        $objPessoa = new clsPessoaFj();

        $db = new clsBanco();
        $db->Consulta("SELECT f.senha FROM portal.funcionario f WHERE f.ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}");

        if ($db->ProximoRegistro()) {
            list($this->f_senha) = $db->Tupla();
        }

        $this->acao_enviar = 'acao2()';

        return $retorno;
    }

    public function null2empityStr($vars)
    {
        foreach ($vars as $key => $valor) {
            $valor .= '';
            if ($valor == 'NULL') {
                $vars[$key] = '';
            }
        }

        return $vars;
    }

    public function Gerar()
    {
        $this->campoOculto('p_cod_pessoa_fj', $this->p_cod_pessoa_fj);
        $this->cod_pessoa_fj = $this->p_cod_pessoa_fj;

        $this->campoRotulo('', '<strong>Informações</strong>', '<strong>Sua senha expirará em alguns dias, por favor cadastre uma nova senha com no mínimo 8 caracteres e diferente da senha anterior</strong>');
        $this->campoSenha('f_senha', 'Senha', '', true, 'A sua nova senha deverá conter pelo menos oito caracteres');
        $this->campoSenha('f_senha2', 'Redigite a Senha', $this->f_senha2, true);
    }

    public function Novo()
    {
        $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE md5('{$this->f_senha}') = senha AND ref_cod_pessoa_fj = {$this->p_cod_pessoa_fj}";
        $db = new clsBanco();
        $senha_igual = $db->CampoUnico($sql);

        if ($this->f_senha && !$senha_igual) {
            $sql_funcionario = "UPDATE funcionario SET senha=md5('{$this->f_senha}'), data_troca_senha = NOW(), tempo_expira_senha = 30 WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
            $db->Consulta($sql_funcionario);
            echo '
        <script>
          window.parent.fechaExpansivel(\'div_dinamico_\'+(parent.DOM_divs.length-1));
          window.parent.location = \'index.php\';
        </script>';

            return true;
        }

        $this->mensagem .= 'A sua nova senha deverá ser diferente da anterior';

        return false;
    }

    public function Editar()
    {

    }

    public function Formular()
    {
        $this->titulo = "Usu&aacute;rios";
        $this->processoAp   = '0';
        $this->renderMenu   = false;
        $this->renderMenuSuspenso = false;
    }
};

?>

<script type="text/javascript">
function acao2()
{
  if ($F('f_senha').length > 7) {
    if ($F('f_senha') == $F('f_senha2')) {
      acao();
    }
    else {
      alert('As senhas devem ser iguais');
    }
  }
  else {
    alert('A sua nova senha deverá conter pelo menos oito caracteres');
  }
}
</script>
