<?php

$obj_permissoes = new clsPermissoes();
$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

if ($nivel_usuario <= 4 && !empty($nivel_usuario)) {
  $retorno .= '
    <tr>
      <td height="24" colspan="2" class="formdktd">
        <span class="form"><b>Filtros de busca</b></span>
      </td>
    </tr>';

  $retorno .= '<form action="" method="post" id="formcadastro" name="formcadastro">';

  if ($obrigatorio) {
    $instituicao_obrigatorio = $escola_obrigatorio = TRUE;
  }
  else {
    $instituicao_obrigatorio = isset($instituicao_obrigatorio) ?
      $instituicao_obrigatorio : $obrigatorio;

    $escola_obrigatorio = isset($escola_obrigatorio) ?
      $escola_obrigatorio : $obrigatorio;
  }

  if ($desabilitado) {
    $instituicao_desabilitado = $escola_desabilitado = TRUE;
  }
  else {
    $instituicao_desabilitado = isset($instituicao_desabilitado) ?
      $instituicao_desabilitado : $desabilitado;

    $escola_desabilitado = isset($escola_desabilitado) ?
      $escola_desabilitado : $desabilitado;
  }

  $obj_permissoes = new clsPermissoes();
  $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

  if ($nivel_usuario == 1) {
    if (class_exists('clsPmieducarInstituicao')) {
      $opcoes = array('' => 'Selecione');

      $obj_instituicao = new clsPmieducarInstituicao();
      $obj_instituicao->setCamposLista('cod_instituicao, nm_instituicao');
      $obj_instituicao->setOrderby('nm_instituicao ASC');

      $lista = $obj_instituicao->lista(NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_instituicao']] = $registro['nm_instituicao'];
        }
      }
    }
    else {
      $opcoes = array('' => 'Erro na geração');
    }

    if ($get_escola) {
      $retorno .= '
        <tr id="tr_status">
          <td valign="top" class="formlttd">
            <span class="form">Instituição</span>
            <span class="campo_obrigatorio">*</span><br/>
            <sub style="vertical-align: top;"/>
          </td>';

      $retorno .= '<td valign="top" class="formlttd"><span class="form">';
      $retorno .= '<select onchange="habilitaCampos(\'ref_cod_instituicao\');" class="geral" name="ref_cod_instituicao" id="ref_cod_instituicao">';

      reset($opcoes);

      while (list($chave, $texto) = each($opcoes)) {
        $retorno .= sprintf(
          '<option id="ref_cod_instituicao_%s" value="%s"',
          urlencode($chave), urlencode($chave)
        );

        if ($chave == $this->ref_cod_instituicao) {
          $retorno .= " selected";
        }

        $retorno .=  ">$texto</option>";
      }

      $retorno .= '</select>';
      $retorno .= '</span></td></tr>';
    }
  }

  if ($nivel_usuario == 2) {
    if ($get_instituicao) {
      $obj_per = new clsPermissoes();
      $this->ref_cod_instituicao = $obj_per->getInstituicao($this->pessoa_logada);
      $retorno .= sprintf(
        '<input type="hidden" id="red_cod_instituicao" value="%s">',
        $this->ref_cod_instituicao
      );
    }
  }
  elseif ($nivel_usuario != 1) {
    $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
    $det_usuario = $obj_usuario->detalhe();
    $this->ref_cod_instituicao = $det_usuario['ref_cod_instituicao'];

    if ($nivel_usuario == 4 || $nivel_usuario == 8) {
      $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
      $det_usuario = $obj_usuario->detalhe();
      $this->ref_cod_escola = $det_usuario['ref_cod_escola'];
    }
  }

  if ($nivel_usuario == 1 || $nivel_usuario == 2) {
    if ($get_escola) {
      if (class_exists('clsPmieducarEscola')) {
        $opcoes_escola = array('' => 'Selecione');

        $todas_escolas = 'escola = new Array();' . "\n";
        $obj_escola = new clsPmieducarEscola();

        $lista = $obj_escola->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, 1);

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $todas_escolas .= sprintf(
              'escola[escola.length] = new Array(%s, \'%s\', %s);' . "\n",
              $registro['cod_escola'], $registro['nome'], $registro['ref_cod_instituicao']
            );
          }
        }

        echo sprintf('<script>%s</script>', $todas_escolas);
      }
      else {
        $opcoes_escola = array('' => 'Erro na geração');
      }

      if ($this->ref_cod_instituicao) {
        if (class_exists('clsPmieducarEscola')) {
          $opcoes_escola = array('' => 'Selecione');
          $obj_escola = new clsPmieducarEscola();
          $lista = $obj_escola->lista(NULL, NULL, NULL, $this->ref_cod_instituicao,
            NULL, NULL, NULL, NULL, NULL, NULL, 1);

          if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
              $opcoes_escola[$registro['cod_escola']] = $registro['nome'];
            }
          }
        }
        else {
          $opcoes_escola = array('' => 'Erro na geração');
        }
      }

      if ($get_escola) {
        $retorno .= '
          <tr id="tr_escola">
            <td valign="top" class="formmdtd">
              <span class="form">Escola</span>
              <span class="campo_obrigatorio">*</span><br/>
              <sub style="vertical-align: top;"/>
            </td>';

        $retorno .= '<td valign="top" class="formmdtd"><span class="form">';

        $disabled = !$this->ref_cod_escola && $nivel_usuario == 1 ? 'disabled="true" ' : '';
        $retorno .= sprintf(
          ' <select class="geral" name="ref_cod_escola" %s id="ref_cod_escola">', $disabled
        );

        reset($opcoes_escola);

        while (list($chave, $texto) = each($opcoes_escola)) {
          $retorno .= sprintf(
            '<option id="ref_cod_escola_%s" value="%s"', urlencode($chave), urlencode($chave)
          );

          if ($chave == $this->ref_cod_escola) {
            $retorno .= ' selected';
          }

          $retorno .= sprintf('>%s</option>', $texto);
        }

        $retorno .= '</select>';
        $retorno .= '</span></td></tr>';
      }
    }
  }

  if (isset($get_cabecalho)) {
    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
      ${$get_cabecalho}[] = 'Escola';
    }
    if ($nivel_usuario == 1) {
      ${$get_cabecalho}[] = 'Instituição';
    }
  }

  $validacao = '';

  if ($nivel_usuario == 1) {
    $validacao = '
      if (!document.getElementById("ref_cod_instituicao").value) {
        alert("Por favor, selecione uma instituicao");
        return false;
      }
      if (!document.getElementById("ref_cod_escola").value) {
        alert("Por favor, selecione uma escola");
        return false;
      } ';
  }
  elseif ($nivel_usuario == 2) {
    $validacao = '
      if (!document.getElementById("ref_cod_escola").value){
        alert("Por favor, selecione uma escola");
        return false;
      } ';
  }

  $retorno .= '
    <tr id="tr_escola">
      <td valign="top" class="formldtd">
        <span class="form">Ano</span>
        <span class="campo_obrigatorio">*</span><br/>
        <sub style="vertical-align: top;"/>
      </td>';
  $retorno .= '<td valign="top" class="formldtd"><span class="form">';
  $retorno .= " <select class='geral' name='ano' id='ano'>";

  $lim = 5;

  for ($a = date('Y'); $a < date('Y') + $lim; $a++) {
    $retorno .= sprintf('<option value="%s"', $a);

    if ($a == $_POST['ano']) {
      $retorno .= " selected";
    }

    $retorno .= '>' . $a . '</option>';
  }

  $retorno .= '</select>';
  $retorno .= '</span></td></tr>';

  $retorno .= '</form>';

  $retorno .= sprintf("
    <tr>
      <td colspan='2' class='formdktd'/>
    </tr>
    <tr>
      <td align='center' colspan='2'>
      <script language='javascript'>
        function acao() {
          %s
          document.formcadastro.submit();
        }
      </script>
      <input type='button' id='botao_busca' value='busca' onclick='javascript:acao();' class='botaolistagem'/>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>", $validacao);
?>

<?php if ($nivel_usuario == 1 || $nivel_usuario == 2): ?>
  <script type="text/javascript">
  var before_getEscola = function() {}
  var after_getEscola  = function() {}

  function getEscola()
  {
    before_getEscola();

    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    if (document.getElementById('ref_cod_escola')) {
      var campoEscola = document.getElementById('ref_cod_escola');
    }

    if (document.getElementById('ref_ref_cod_escola')) {
      var campoEscola = document.getElementById('ref_ref_cod_escola');
    }

    campoEscola.length = 1;
    campoEscola.options[0] = new Option('Selecione uma escola', '', false, false);
    for (var j = 0; j < escola.length; j++) {
      if (escola[j][2] == campoInstituicao) {
        campoEscola.options[campoEscola.options.length] = new Option(
          escola[j][1], escola[j][0], false, false
        );
      }
    }

    if (campoEscola.length == 1 && campoInstituicao != '') {
      campoEscola.options[0] = new Option(
        'A institução não possui nenhuma escola', '', false, false
      );
    }

    after_getEscola();
  }

  function habilitaCampos(campo)
  {
    var campo_instituicao = document.getElementById('ref_cod_instituicao');
    var campo_escola      = document.getElementById('ref_cod_escola');

    if (campo == "") {
      campo_instituicao.disabled = true;
      campo_escola.disabled      = true;
    }
    else if (campo == 'ref_cod_instituicao') {
      campo_escola.disabled = false;
      getEscola();
    }
  }
  </script>
<?php endif; ?>
<?php
}