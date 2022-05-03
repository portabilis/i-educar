(function($){
  $(document).ready(function(){
    var bncc_table = document.getElementById("objetivos_aprendizagem");
    var btn_add    = document.getElementById("btn_add_tab_add_1");

    var anoField   = getElementFor('ano');
    var turmaField = getElementFor('turma');

    var submitButton = $j('#btn_enviar');
    submitButton.removeAttr('onclick');

    submitButton.click(function () {
        enviarFormulario();
    });

    consertarBNCCElementos();
    consertarBNCCEspecificoesElementos();

    btn_add.onclick = function () {
      tab_add_1.addRow();
      
      updateComponentesCurriculares();
      consertarBNCCElementos();
      consertarBNCCEspecificoesElementos();
    }

    var updateComponentesCurriculares = function () {
      if (anoField.val() && turmaField.val() && turmaField.is(':enabled')) {

        var data = {
          ano      : anoField.attr('value'),
          turma_id : turmaField.attr('value')
        };

        var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricular', 'componentesCurriculares', data
        );

        var options = {
          url : urlForGetComponentesCurriculares,
          dataType : 'json',
          success  : handleGetComponentesCurriculares
        };

        getResources(options);
      }
    }

    function handleGetComponentesCurriculares (response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);

      var linhasElemento = document.getElementsByName("tr_objetivos_aprendizagem[]");
      var componentesCurricularesElementos = []

      // get disciplines elements
      linhasElemento.forEach(linhaElemento => {
        componentesCurricularesElementos.push(linhaElemento.children[0].children[0]);
      });

      componentesCurricularesElementos.forEach(componenteCurricularElemento => {
        var jComponenteCurricularElemento = $(componenteCurricularElemento);
        
        // add disciplines
        selectOptions.forEach(option => {
          jComponenteCurricularElemento.append(option[0]);
        });

        // bind onchange event
        componenteCurricularElemento.addEventListener("change", trocaComponenteCurricular, false);
      });
    }

    function consertarBNCCElementos () {
      count = bncc_table.children[0].childElementCount - 4;
      id = 0;
      index = 0;
      while (index !== count) {
        if (id > 1000) break;
        var bnccField = document.getElementById(`custom_bncc[${id}]`);

        if (bnccField !== null) {
          bnccField.setAttribute("multiple", "multiple");
        
          $j(bnccField).chosen({
            no_results_text: "Sem resultados para ",
            width: '100% !important',
            height: '28px',
            placeholder_text_multiple: "Selecione as opções",
            search_contains: true
          }).change(async function(e){
            await trocaBNCC(pegarId(e.currentTarget.id), $(this).val());
          });

          index++;
        }

        id++;
      }
    }

    function consertarBNCCEspecificoesElementos () {
      count = bncc_table.children[0].childElementCount - 4;
      id = 0;
      index = 0;
      while (index !== count) {
        var bncc_especificaoes = document.getElementById(`custom_bncc_especificacoes[${id}]`);

        if (bncc_especificaoes !== null) {
          bncc_especificaoes.setAttribute("multiple", "multiple");
        
          $j(bncc_especificaoes).chosen({
            no_results_text: "Sem resultados para ",
            width: '100% !important',
            height: '28px',
            placeholder_text_multiple: "Selecione as opções",
            search_contains: true
          });

          index++;
        }

        id++;
      }
    }
    
    async function trocaComponenteCurricular (event) {
      var bnccDados = [];
    
      var componenteCurricularId = pegarId(event.currentTarget.id);
      var componenteCurricularValue = event.currentTarget.value || null;
      var turma = document.getElementById("ref_cod_turma").value;
    
      var bnccElemento = document.getElementById(`custom_bncc[${componenteCurricularId}]`);

      if (turma !== null && componenteCurricularValue !== null) {
        var searchPathBNCCTurma = '/module/Api/BNCC?oper=get&resource=bncc_turma',
        paramsBNCCTurma  = {
          turma                 : document.getElementById("ref_cod_turma").value,
          componente_curricular : componenteCurricularValue
        };
      
        await $j.get(searchPathBNCCTurma, paramsBNCCTurma, function (dataResponse) {
          bnccDados = dataResponse.bncc === null ? [] : Object.entries(dataResponse.bncc);
      
          addOpcoesBNCC(bnccElemento, bnccDados);
        });
      } else {
        addOpcoesBNCC(bnccElemento, []);
      }
    }

    async function trocaBNCC (bnccElementoId, bnccArray) {
      var bnccEspeficacoesDados = [];

      var bnccEspecificoesElemento = document.getElementById(`custom_bncc_especificacoes[${bnccElementoId}]`);

      if (bnccElementoId !== null && bnccArray !== null && bnccArray.length > 0) {
        var searchPathBNCCEspeficacoesTurma = '/module/Api/BNCCEspecificacao?oper=get&resource=list',
        paramsBNCCEspecificacoesTurma  = {
          bnccArray  : bnccArray
        };
      
        await $j.get(searchPathBNCCEspeficacoesTurma, paramsBNCCEspecificacoesTurma, function (dataResponse) {
          var obj = dataResponse.result;
          bnccEspeficacoesDados = dataResponse.result === null ? [] : Object.keys(obj).map((key) => [obj[key][0], obj[key][1], obj[key][2]]);

          addOpcoesBNCC(bnccEspecificoesElemento, bnccEspeficacoesDados);
        });
      } else {
        addOpcoesBNCC(bnccEspecificoesElemento, []);
      }
    }
    
    function addOpcoesBNCC (elemento, novasOpcoes) {
      const maxCharacters = 60;

      $(elemento).empty();

      for (let index = 0; index < novasOpcoes.length; index++) {
        const novaOpcao = novasOpcoes[index];

        var id = novaOpcao[2] != null ? novaOpcao[2] : novaOpcao[0];
        var value = novaOpcao[1].substring(0, maxCharacters).trimEnd();
        value = value.length < maxCharacters ? value : value.concat("...");
        $(elemento).append(`<option value="${id}">${value}</option>`);
      }
    
      $(elemento).trigger("chosen:updated");
    }
    
    function pegarId (id) {
      id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));
    
      return id;
    }

    function pegarComponentesCurriculares () {
      var componentesCurriculares = []

      tr_objetivos_aprendizagens = document.getElementsByName("tr_objetivos_aprendizagem[]");
      tr_objetivos_aprendizagens.forEach(tr_objetivos_aprendizagem => {
          var id = tr_objetivos_aprendizagem.children[0].children[0].id;
          var componenteCurricularElemento = document.getElementById(id);
          var componenteCurricularId = pegarId(componenteCurricularElemento.name);
          var componenteCurricularValor = componenteCurricularElemento.value;

          var componenteCurricular = [];
          componenteCurricular.push(componenteCurricularId);
          componenteCurricular.push(componenteCurricularValor);
          componentesCurriculares.push(componenteCurricular);
      });

      return componentesCurriculares;
    }

    function pegarBNCCs () {
      var BNCCs = []

      tr_objetivos_aprendizagens = document.getElementsByName("tr_objetivos_aprendizagem[]");
      tr_objetivos_aprendizagens.forEach(tr_objetivos_aprendizagem => {
          var id = tr_objetivos_aprendizagem.children[1].children[0].id;
          var BNCCElemento = document.getElementById(id);
          var BNCCId = pegarId(BNCCElemento.name);
          var BNCCValores = Array.from(BNCCElemento.selectedOptions).map(({ value }) => value);

          var BNCC = [];
          BNCC.push(BNCCId);
          BNCC.push(BNCCValores);
          BNCCs.push(BNCC);
      });

      return BNCCs;
    }

    function pegarBNCCEspecificacoes () {
      var BNCCEspecificacoes = []

      tr_objetivos_aprendizagens = document.getElementsByName("tr_objetivos_aprendizagem[]");
      tr_objetivos_aprendizagens.forEach(tr_objetivos_aprendizagem => {
          var id = tr_objetivos_aprendizagem.children[2].children[0].id;
          var BNCCEspecificacaoElemento = document.getElementById(id);
          var BNCCEspecificacaoId = pegarId(BNCCEspecificacaoElemento.name);
          var BNCCEspecificacaoValores = Array.from(BNCCEspecificacaoElemento.selectedOptions).map(({ value }) => value);

          var BNCCEspecificacao = [];
          BNCCEspecificacao.push(BNCCEspecificacaoId);
          BNCCEspecificacao.push(BNCCEspecificacaoValores);
          BNCCEspecificacoes.push(BNCCEspecificacao);
      });

      return BNCCEspecificacoes;
    }

    function pegarConteudos () {
      var conteudos = []

      tr_conteudos = document.getElementsByName("tr_conteudos[]");
      tr_conteudos.forEach(tr_conteudo => {
          var id = tr_conteudo.children[0].children[0].id;
          var conteudoElemento = document.getElementById(id);
          var conteudoId = pegarId(conteudoElemento.name);
          var conteudoValor = conteudoElemento.value;

          var conteudo = [];
          conteudo.push(conteudoId);
          conteudo.push(conteudoValor);
          conteudos.push(conteudo);
      });

      return conteudos;
    }

    function enviarFormulario () {
      var data_inicial              = dataParaBanco(document.getElementById("data_inicial").value);
      var data_final                = dataParaBanco(document.getElementById("data_final").value);
      var turma                     = document.getElementById("ref_cod_turma").value;
      var faseEtapa                 = document.getElementById("fase_etapa").value;
      var ddp                       = document.getElementById("ddp").value;
      var atividades                = document.getElementById("atividades").value;
      var referencias               = document.getElementById("referencias").value;
      var conteudos                 = pegarConteudos();
      var componentesCurriculares   = pegarComponentesCurriculares();
      var bnccs                     = pegarBNCCs();
      var bnccEspecificacoes        = pegarBNCCEspecificacoes();
      var recursos_didaticos        = document.getElementById("recursos_didaticos").value;
      var registro_adaptacao        = document.getElementById("registro_adaptacao").value;

      // VALIDAÇÃO
      if (!ehDataValida(new Date(data_inicial))) { alert("Data inicial não é válida."); return; }
      if (!ehDataValida(new Date(data_final))) { alert("Data final não é válida."); return; }
      if (isNaN(parseInt(turma, 10))) { alert("Turma é obrigatória."); return; }
      if (isNaN(parseInt(faseEtapa, 10))) { alert("Etapa é obrigatória."); return; }
      if (ddp == null || ddp == '') { alert("Metodologia é obrigatória."); return; }
      if (atividades == null) { alert("O campo atividades não é válido."); return; }
      if (referencias == null) { alert("O campo referências não é válido."); return; }
      if (!ehComponentesCurricularesValidos(componentesCurriculares)) { alert("Os componentes curriculares são obrigatórios."); return; }
      if (!ehBNCCsValidos(bnccs)) { alert("As habilidades são obrigatórias."); return; }
      if (!ehBNCCEspecificacoesValidos(bnccEspecificacoes)) { alert("As especificações são obrigatórias."); return; }
      if (!ehConteudosValidos(conteudos)) { alert("Os conteúdos são obrigatórios."); return; }
      if (recursos_didaticos == null) { alert("O campo recursos didáticos não é válido."); return; }
      if (registro_adaptacao == null) { alert("O campo registro de adaptação não é válido."); return; }

      novoPlanoAula(
        data_inicial,
        data_final,
        turma,
        faseEtapa,
        ddp,
        atividades,
        referencias,
        conteudos,
        componentesCurriculares,
        bnccs,
        bnccEspecificacoes,
        recursos_didaticos,
        registro_adaptacao
      );
    }

    function dataParaBanco (dataFromBrasil) {
      var data = "";
      var data_fragmentos = dataFromBrasil.split('/');

      for (let index = data_fragmentos.length - 1; index >= 0; index--) {
          const data_fragmento = data_fragmentos[index];

          if (index !== 0) {
              data += data_fragmento + '-';
          } else {
              data += data_fragmento;
          }
      }
    
      return data
    }
  
    function ehDataValida (d) {
      return d instanceof Date && !isNaN(d);
    }

    function ehConteudosValidos (conteudos) {
      return conteudos.every(conteudo => conteudo[1] !== "" && conteudo[1] != null);
    }

    function ehComponentesCurricularesValidos (componentesCurriculares) {
      return componentesCurriculares.every(componenteCurricular => !isNaN(parseInt(componenteCurricular[1], 10)));
    }

    function ehBNCCsValidos (bnccs) {
      return bnccs.every(bncc => bncc[1].length > 0);
    }

    function ehBNCCEspecificacoesValidos (bnccEspecificacoes) {
      return bnccEspecificacoes.every(bnccsEspecificacao => bnccsEspecificacao[1].length > 0);
    }

    function novoPlanoAula (data_inicial, data_final, turma, faseEtapa, ddp, atividades, referencias, conteudos, componentesCurriculares, bnccs, bnccEspecificacoes, recursos_didaticos, registro_adaptacao) {
      var urlForNovoPlanoAula = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'novo-plano-aula', {});

      var options = {
          type     : 'POST',
          url      : urlForNovoPlanoAula,
          dataType : 'json',
          data     : {
            data_inicial            : data_inicial,
            data_final              : data_final,
            turma                   : turma,
            faseEtapa               : faseEtapa,
            ddp                     : ddp,
            atividades              : atividades,
            referencias             : referencias,
            conteudos               : conteudos,
            componentesCurriculares : componentesCurriculares,
            bnccs                   : bnccs,
            bnccEspecificacoes      : bnccEspecificacoes,
            recursos_didaticos      : recursos_didaticos,
            registro_adaptacao      : registro_adaptacao
          },
          success  : handleNovoPlanoAula
      };

      postResource(options);
    }

    function handleNovoPlanoAula (response) {
      if(response.result == "Cadastro efetuado com sucesso.") {
          messageUtils.success('Cadastro efetuado com sucesso!');

          delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_lst.php", '_self'));
      } else {
          messageUtils.error(response.result);
      }
    }

    function delay (time) {
      return new Promise(resolve => setTimeout(resolve, time));
    }

    function urlHelper (href, mode) {
      Object.assign(document.createElement('a'), {
      target: mode,
      href: href,
      }).click();
  }

    // bind onchange event
    turmaField.change(updateComponentesCurriculares);
  });
})(jQuery);
