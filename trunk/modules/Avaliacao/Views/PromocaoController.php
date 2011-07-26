<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/* 

ANTES DE RODAR ESTE PROCESSO, EXECURAR ESTES SQL PARA ELIMINAR INFORMAÇÕES QUE CAUSAM ERROS DURANTE O PROCESSO

#TODO criar botões na página promocao para cada um destes sql e remover faltas ?

--altera para em andamento as matriculas aprovadas ou reprovadas de alunos que possuem notas cujo materias não estão mais vinculadas a turma (no caso de turma não padrão)
update pmieducar.matricula set aprovado = 3 where aprovado in (1, 2) and ano = 2010 and cod_matricula in (
select m.cod_matricula from modules.nota_componente_curricular as ncc,
		modules.nota_aluno as na, 
		pmieducar.matricula as m,
		pmieducar.matricula_turma as mt

	 where ncc.nota_aluno_id = na.id and

		m.cod_matricula = na.matricula_id and
		m.cod_matricula = mt.ref_cod_matricula and
		m.ativo = 1 and
		mt.ativo = m.ativo and
		m.ano = 2010 and
		--m.aprovado = 3 and
		ncc.componente_curricular_id not in (

			select cct.componente_curricular_id from modules.componente_curricular_turma as cct where 
				cct.turma_id = mt.ref_cod_turma));


--altera para em andamento as matriculas aprovadas ou reprovadas de alunos que possuem medias das notas cujo materias não estão mais vinculadas a turma (no caso de turma não padrão)
update pmieducar.matricula set aprovado = 3 where aprovado in (1, 2) and ano = 2010 and cod_matricula in (select m.cod_matricula from modules.nota_componente_curricular_media as nccm,
		modules.nota_aluno as na, 
		pmieducar.matricula as m,
		pmieducar.matricula_turma as mt

	 where nccm.nota_aluno_id = na.id and

	 
		m.cod_matricula = na.matricula_id and
		m.cod_matricula = mt.ref_cod_matricula and
		m.ativo = 1 and
		mt.ativo = m.ativo and
		m.ano = 2010 and
		--m.aprovado = 3 and
		nccm.componente_curricular_id not in (

			select cct.componente_curricular_id from modules.componente_curricular_turma as cct where 
				cct.turma_id = mt.ref_cod_turma));

--deleta os componentes curriculares turma, que nao possuem relacionamento em modules.componente_curricular_ano_escolar - causa do erro não foi encontrado registro com as chaves informadas
delete from modules.componente_curricular_turma as cct where not exists (select 1 from modules.componente_curricular_ano_escolar as cca WHERE componente_curricular_id = cct.componente_curricular_id AND cca.ano_escolar_id = cct.ano_escolar_id);

--remove as notas cujo materias não estão mais vinculadas a turma (no caso de turma não padrão)
delete from modules.nota_componente_curricular where id in ( select ncc.id from modules.nota_componente_curricular as ncc,
		modules.nota_aluno as na, 
		pmieducar.matricula as m,
		pmieducar.matricula_turma as mt

	 where ncc.nota_aluno_id = na.id and

		m.cod_matricula = na.matricula_id and
		m.cod_matricula = mt.ref_cod_matricula and
		m.ativo = 1 and
		mt.ativo = m.ativo and
		m.ano = 2010 and
		--m.aprovado = 3 and
		ncc.componente_curricular_id not in (

			select cct.componente_curricular_id from modules.componente_curricular_turma as cct where 
				cct.turma_id = mt.ref_cod_turma));

--remove as medias das notas cujo materias não estão mais vinculadas a turma (no caso de turma não padrão)
delete from modules.nota_componente_curricular_media where nota_aluno_id||componente_curricular_id in ( select nccm.nota_aluno_id|| nccm.componente_curricular_id from modules.nota_componente_curricular_media as nccm,
		modules.nota_aluno as na, 
		pmieducar.matricula as m,
		pmieducar.matricula_turma as mt

	 where nccm.nota_aluno_id = na.id and

	 
		m.cod_matricula = na.matricula_id and
		m.cod_matricula = mt.ref_cod_matricula and
		m.ativo = 1 and
		mt.ativo = m.ativo and
		m.ano = 2010 and
		--m.aprovado = 3 and
		nccm.componente_curricular_id not in (

			select cct.componente_curricular_id from modules.componente_curricular_turma as cct where 
				cct.turma_id = mt.ref_cod_turma));

*/


ini_set('max_execution_time', 1200);#seconds

require_once 'Core/Controller/Page/ListController.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';

class PromocaoController extends Core_Controller_Page_ListController
{
  protected $_titulo   = 'Promocao alunos';
  protected $_processoAp = 644; #usando a mesma do boletim por turma...

  public function Gerar()
  {

    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];
    $this->ano_escolar = $_GET['ano_escolar'];

    #$get_escola = $escola_obrigatorio = TRUE;
    #$get_ano_escolar = $ano_escolar_obrigatorio = true;
    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoNumero('ano_escolar', 'ano_escolar', '', 4, 4, true, '');

    if ($this->ref_cod_instituicao && $this->ref_cod_escola && $this->ano_escolar)
    {

    }
    $this->largura = '100%';
    $a = <<<EOT

        <style type="text/css">
          #formcadastro #nm_aluno, #formcadastro select {
            min-width: 400px;
          }

        </style>

        <script type="text/javascript">

            document.getElementById('botao_busca').value = 'Verificar';

            function pesquisa_aluno()
            {
              pesquisa_valores_popless('/intranet/educar_pesquisa_aluno.php')
            }

            __bBusca = document.getElementById('botao_busca');
            var __old_event = __bBusca.onclick;
            __bBusca.onclick = function()
            {

              var __not_empty_fields = document.getElementsByClassName('obrigatorio');
              var __all_filled = true;
              for (var i = 0; i < __not_empty_fields.length; i++)
              {
                if (! __not_empty_fields[i].value)
                {
                  var __all_filled = false;
                  break;
                }
              }
              if (! __all_filled)
                alert('Selecione um valor em todos os campos, antes de continuar.');
              else
              {

               	__instituicao_id = document.getElementById('ref_cod_instituicao').value;
                /*__escola_id = document.getElementById('ref_cod_escola').value;*/
                __ano_escolar = document.getElementById('ano_escolar').value;
                __bBusca.disable();
                __bBusca.value = 'Verificando...';

                getNumeroAlunosEmAndamento(__instituicao_id, __ano_escolar);

                //__old_event();
                //__bBusca.onclick = __old_event;
                //__bBusca.click(); bug no ie ?
              }
            }

            function getNumeroAlunosEmAndamento(instituicao_id, ano_escolar)
            {

              var action = 'get_numero_alunos_em_andamento';

              var request = new ajax(updateNumAlunos);
              var vars = "/module/Avaliacao/PromocaoAjax?instituicao_id="+instituicao_id+"&ano_escolar="+ano_escolar+"&action="+action;
              //alert(vars);
              request.envia(vars);

            }

            function updateNumAlunos(xml)
            {
              var num_restante = 	xml.getElementsByTagName('restante');
              var __ultima_matricula = xml.getElementsByTagName('ultima_matricula')[0].getAttribute('id');
              var u = document.getElementById('ultima-matricula');
              if (u)
                u.value = __ultima_matricula;
              __limit_by = document.getElementById('limitby');

              if (__limit_by)
                __limit_by = __limit_by.value;
              else
                __limit_by = 1;

              var p_num_restante = document.getElementById('num_restante');
              if (! p_num_restante)
              {
                var p_num_restante = document.createElement('p');
                p_num_restante.id = 'num_restante';
                appendElementInForm(p_num_restante);

                var b = document.createElement('input');
                b.id = 'iniciar_processo'
                b.type = 'button';
                b.value = 'Iniciar processo'; 
                b.onclick = function(){

                  var action = 'savenotasfaltas';

                  document.getElementById('iniciar_processo').disable();
                  document.getElementById('iniciar_processo').value = 'Processando...';

                  var request = new ajax(updateNumAlunos);
                  var vars = "/module/Avaliacao/PromocaoAjax?instituicao_id="+__instituicao_id+"&ano_escolar="+__ano_escolar+"&action="+action+"&limit_by="+__limit_by+"&ultima_matricula="+document.getElementById('ultima-matricula').value;
                  //alert(vars);
                  request.envia(vars);

                };

                var t = document.createElement('p');
                t.innerText = 'Ultima matricula: ';

                var c = document.createElement('input');
                c.id= 'ultima-matricula';
                c.value='0';
                t.appendChild(c);
                appendElementInForm(t);

                var t = document.createElement('p');
                t.innerText = 'limite atualizacoes : ';

                var c = document.createElement('input');
                c.id= 'limitby';
                c.value='5';
                t.appendChild(c);
                appendElementInForm(t);
                
                var t = document.createElement('p');
                t.textContent = 'Continuar processo';

                var c = document.createElement('input');
                c.type = 'checkbox';
                c.id= 'continuar-processo';
                t.appendChild(c);
                appendElementInForm(t);

                appendElementInForm(b);
              }
              p_num_restante.textContent = 'Encontrado: ' + num_restante[0].getAttribute('num') + ' alunos com situacao em andamento'; 
              document.getElementById('iniciar_processo').enable();
              document.getElementById('iniciar_processo').value = 'Iniciar processo';
              //__bBusca.enable();
              __bBusca.value = '.';

              if (! document.getElementById('ultima-matricula').value)
                document.getElementById('ultima-matricula').value = 0;

              var msgs = xml.getElementsByTagName('msg');
              for(var i =0; i<msgs.length; i++)
                appendMessage(msgs[i].getAttribute('text'));

              if (document.getElementById('continuar-processo').checked)
                document.getElementById('iniciar_processo').click();
            }

            function appendMessage(msg)
            {

              var t = document.getElementById('messages');        
            
              if (! t)
              {
                var t = document.createElement('table');
                t.id = 'messages';
                appendElementInForm(t);
              }

              var tr = document.createElement('tr');
              var td = document.createElement('td');
              td.textContent = msg;

              tr.appendChild(td);
              t.appendChild(tr);
            }

            function appendElementInForm(element)
            {
              var form = document.getElementById('form_resultado');
              //var parent = form.parentNode;
              //form.remove();                                
              //t = document.createElement('p');
              //t.align = 'center';
              //t.textContent = 'Por favor aguarde, carregando dados...'; 
              //parent.appendChild(t);
              form.appendChild(element);
            }

            var __a = document.createElement('a');
            __a.innerHTML = 'Recarregar';
            __a.href = document.location.href.split('?')[0];
            __bBusca.parentNode.appendChild(__a);

              /*document.getElementById('ref_cod_instituicao').onchange = function()
              {
                clearSelect(entity = 'ano_escolar', disable = false, text = '', multipleId = false);
                getEscola();
              }

              document.getElementById('ref_cod_escola').onchange = function()
              {
                getAnoEscolar();
              }*/

        </script>

        <script type="text/javascript" src="/modules/Avaliacao/Static/ajax.js"> </script>
        <script type="text/javascript" src="/modules/Avaliacao/Static/dom_utils.js"> </script>
        <script type="text/javascript">

          var ajaxReq = new AjaxRequest();

          function setAtt(att, matricula, etapa, componente_curricular)
          {
            try
            {
              var attElement = document.getElementById(att + '-matricula:' + matricula);
              var attValue = attElement.value;

              //fix for bug in service boletim
              if (att == 'parecer' && ((/^\d+\.\d+$/.test(attValue)) || (/^\d+$/.test(attValue)) || (/^\.\d+$/.test(attValue)) || (/^\d+\.$/.test(attValue))))
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">Informe pelo menos uma letra.</span>';
              else if (attValue.length)
              {
                var _c = ['notas', 'faltas', 'parecer'];
                for (var i = 0; i < _c.length; i++)
                {
                  var _e = document.getElementsByClassName(_c[i]);
                  for (var j = 0; j < _e.length; j++)
                    _e[j].disabled = true;
                }

                document.getElementById('status_alteracao-matricula:'+matricula).innerHTML = 'Atualizando... <img src="/modules/Avaliacao/Static/images/min-wait.gif"/>';
                var vars = "att="+att+"&matricula=" + matricula + "&etapa=" + etapa + "&componente_curricular=" + componente_curricular+"&att_value=" + attValue;
                //alert(vars);
                ajaxReq.send("POST", "/module/Avaliacao/DiarioAjax", handleRequest, "application/x-www-form-urlencoded; charset=UTF-8", vars);
              }
              else
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">Selecione um valor v&aacute;lido.</span>';
            }
            catch(err)
            {
              try
              {
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">ERRO1: Ocorreu um erro inesperado, por favor tente novamente.</span>';
                window.location.reload();
              }
              catch(err)
              {
                alert('ERRO2: Ocorreram erros inesperados, por favor tente novamente.');
                window.location.reload();
              }
            }
          }

          function handleRequest()
          {
            try
            {
              if (ajaxReq.getReadyState() == 4 && ajaxReq.getStatus() == 200)
              {
                var xmlData = ajaxReq.getResponseXML().getElementsByTagName("status")[0];
                var att = getText(xmlData.getElementsByTagName('att')[0]);
                var success = getText(xmlData.getElementsByTagName('success')[0]);
                var matricula = getText(xmlData.getElementsByTagName('matricula')[0]);
                document.getElementById(att + '-matricula:' + matricula).disabled = false;
                document.getElementById('botao_busca').disabled = false;

                var _c = ['notas', 'faltas', 'parecer'];
                for (var i = 0; i < _c.length; i++)
                {
                  var _e = document.getElementsByClassName(_c[i]);
                  for (var j = 0; j < _e.length; j++)
                    _e[j].disabled = false;
                }

                var situacao = getText(xmlData.getElementsByTagName('situacao')[0]);
                document.getElementById('situacao'  + '-matricula:' + matricula).innerHTML = situacao;

                if (success == '1')
                  var s = '<span class="success" style="color: green;">Atualizado</span>';
                else
                   var s = '<span class="error" style="color: red;">Erro ao atualizar, tente novamente !</span>';
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = s;
              }
            }
            catch(err)
            {
              try
              {
                document.getElementById('status_alteracao-matricula:' + matricula).innerHTML = '<span class="error" style="color: red;">ERRO3: Ocorreu um erro inesperado, por favor tente novamente.</span>';
                window.location.reload();
              }
              catch(err)
              {
                alert('ERRO4: Ocorreram erros inesperados, por favor tente novamente.');
                window.location.reload();
              }
            }
          }
        </script>
EOT;

  $this->appendOutput($a);

  }
}

?>
