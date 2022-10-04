(function($){
    $(document).ready(function(){
      //TODO: Refatorar Edição do plano de aula

        var id = $j('#id').val();
        var copy = $j('#copy').val();
        var bncc_table = document.getElementById("objetivos_aprendizagem");
        var titleTable = document.getElementById("tr_objetivos_aprendizagem_tit");
        var btn_add    = document.getElementById("btn_add_tab_add_1");
        var bnccsUtilizados = [];
        var especificacoesUtilizados = [];

        if (isNaN(id) || id === '')
            return;

        // if (!isNaN(id) && copy)
        //     return;

        var registrosAula = [];

        var planejamento_aula_id = $j('#id').val();
        var servidor_id = $j('#servidor_id').val();
        var auth_id = $j('#auth_id').val();
        var is_professor = $j('#is_professor').val();
        var turma_id = $j('#ref_cod_turma').val();
        var ano = $j('#ano').val();
        var ddp;
        var atividades;
        var bncc;
        var conteudos;
        var referencias;
        var componentesCurriculares;


      btn_add.onclick = function () {
        tab_add_1.addRow();

        updateComponentesCurriculares(false);
        consertarBNCCElementos();
        consertarBNCCEspecificoesElementos();
      }

      getObjetivosAprendizagem();

      function getComponentesCurriculares () {
        if (ano && turma_id) {

          var data = {
            ano      : ano,
            turma_id : turma_id
          };

          var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
            '/module/DynamicInput/componenteCurricular', 'componentesCurriculares', data
          );

          var options = {
            url : urlForGetComponentesCurriculares,
            dataType : 'json',
            success  : function (response) {
              componentesCurriculares = response['options'];
            }
          };

          getResources(options);
        }
      }

      async function getObjetivosAprendizagem() {
        $(titleTable).children().first().html("Aguarde, carregando Objetivo(s) de aprendizagem...");

        await getComponentesCurriculares();

        var urlForGetObjetivosAprendizagem = getResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'get-objetivos-aprendizagem', {});

        var options = {
          url: urlForGetObjetivosAprendizagem,
          dataType: 'json',
          data: {
            planejamento_aula_id: planejamento_aula_id,
            turma_id: turma_id,
            ano: ano,
          },
          success: handleFillObjetivosAprendizagem
        };

        getResources(options);
      }

      function handleFillObjetivosAprendizagem(response) {
        for (let index = 0; index < response.count_objetivos; index++) {
          gerarObjetivoAprendizagem(index, response);
        }

        bnccsUtilizados = response['utilizados']['bncss_utilizados'];
        especificacoesUtilizados = response['utilizados']['especificacoes_utilizados'];
     }

      async function gerarObjetivoAprendizagem(index, response) {

      if (index > 0) {
        tab_add_1.addRow();
        consertarBNCCElementos();
        consertarBNCCEspecificoesElementos();
      }

        await fillComponenteCurricular(index, response);
        await fillHabilidadesAndEspecificacoes(index, response);

        $(titleTable).children().first().html("Objetivo(s) de aprendizagem");
     }

      function fillComponenteCurricular(index, response) {
        let ccElement = document.getElementById(`ref_cod_componente_curricular_array[${index}]`);
        $.each(componentesCurriculares, function (id, mapValue) {
          let selected = '';
          if (id.indexOf && id.substr && id.indexOf('__') == 0) {
            id = id.substr(2);

            if (parseInt(id) == response[index].componente_curricular_id) {
              selected = 'selected';
            }

            $(ccElement).append(`<option value="${id}" ${selected}>${mapValue.value}</option>`);
          }
          ccElement.addEventListener("change", trocaComponenteCurricular, false);
        });
      }

      function fillHabilidadesAndEspecificacoes(index, response) {
        let bnccElemento = document.getElementById(`custom_bncc[${index}]`);
        let bnccEspecificoesElemento = document.getElementById(`custom_bncc_especificacoes[${index}]`);
        let habilidadesGeralCC = Object.entries(response[index].habilidades.habilidades_geral_cc);
        const maxCharacters = 60;

        habilidadesGeralCC.forEach(function (habilidade, key) {
          let selected = '';
          let styleBNCC = '';
          let id = habilidade[0];
          let value = habilidade[1].substring(0, maxCharacters).trimEnd();
          value = value.length < maxCharacters ? value : value.concat("...");

          if ($.inArray(id, response[index].habilidades.habilidades_planejamento_aula_cc) !== -1) {
            selected = 'selected';
          }

          if (bnccsUtilizados.includes(parseInt(id))) {
            styleBNCC = "style=\"color:blue\"";
          }

          $(bnccElemento).append(`<option value="${id}" ${styleBNCC} ${selected}>${value}</option>`);
          $(bnccElemento).trigger("chosen:updated");

          if (response[index].especificacoes.especificacoes_geral_bncc[id]) {
            let escpecificacoesGeralBNCC = Object.entries(response[index].especificacoes.especificacoes_geral_bncc[id]);

            escpecificacoesGeralBNCC.forEach(function (especificacao) {
              let selectedEspecificacao = '';
              let styleEspecificacao = '';
              let idEspecificacao = especificacao[0];
              let valueEspecificacao = especificacao[1].substring(0, maxCharacters).trimEnd();
              valueEspecificacao = valueEspecificacao.length < maxCharacters ? valueEspecificacao : valueEspecificacao.concat("...");

              if ($.inArray(parseInt(idEspecificacao), response[index].especificacoes.especificacoes_pa_bncc[0]) !== -1) {
                selectedEspecificacao = 'selected';
              }

              if (especificacoesUtilizados.includes(parseInt(idEspecificacao))) {
                styleEspecificacao = "style=\"color:blue\"";
              }

              $(bnccEspecificoesElemento).append(`<option value="${idEspecificacao}" ${styleEspecificacao} ${selectedEspecificacao}>${valueEspecificacao}</option>`);
              $(bnccEspecificoesElemento).trigger("chosen:updated");
            });
          }
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

      function updateComponentesCurriculares (clearComponent = true) {
        if (ano && turma_id) {

          var data = {
            ano      : ano,
            turma_id : turma_id
          };

          var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
            '/module/DynamicInput/componenteCurricular', 'componentesCurriculares', data
          );

          var options = {
            url : urlForGetComponentesCurriculares,
            dataType : 'json',
            success  : function (response) {
              handleGetComponentesCurriculares(response, clearComponent)
            }
          };

          getResources(options);
        }
      }

      function handleGetComponentesCurriculares (response, clearComponent = true) {
        var selectOptions = jsonResourcesToSelectOptions(response['options']);
        bnccsUtilizados = response['utilizados']['bncss_utilizados'];
        especificacoesUtilizados = response['utilizados']['especificacoes_utilizados'];

        var linhasElemento = document.getElementsByName("tr_objetivos_aprendizagem[]");
        var componentesCurricularesElementos = []
        let componentesCurricularesSelecionados = pegarSomenteValoresComponentesCurriculares();

        // get disciplines elements
        linhasElemento.forEach(linhaElemento => {
          componentesCurricularesElementos.push(linhaElemento.children[0].children[0]);
        });

        componentesCurricularesElementos.forEach(componenteCurricularElemento => {
          var jComponenteCurricularElemento = $(componenteCurricularElemento);

          if (clearComponent) {
            $(componenteCurricularElemento).empty();
            var optionOne = '<option id="ref_cod_componente_curricular_array[0]_" value="">Selecione o componente curricular</option>';
            jComponenteCurricularElemento.append(optionOne);
          }

          // add disciplines
          selectOptions.forEach(option => {
            if (!componentesCurricularesSelecionados.includes(option[0].value)) {
              jComponenteCurricularElemento.append(option[0]);
            }
          });

          // bind onchange event
          componenteCurricularElemento.addEventListener("change", trocaComponenteCurricular, false);
        });
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

            addOpcoesBNCC(bnccEspecificoesElemento, bnccEspeficacoesDados, false);
          });
        } else {
          addOpcoesBNCC(bnccEspecificoesElemento, [], false);
        }
      }

      function addOpcoesBNCC (elemento, novasOpcoes, bncc = true) {
        const maxCharacters = 60;

        $(elemento).empty();

        for (let index = 0; index < novasOpcoes.length; index++) {
          const novaOpcao = novasOpcoes[index];

          var id = novaOpcao[2] != null ? novaOpcao[2] : novaOpcao[0];
          var value = novaOpcao[1].substring(0, maxCharacters).trimEnd();
          value = value.length < maxCharacters ? value : value.concat("...");

          var style = '';

          if (bncc && bnccsUtilizados.includes(parseInt(id))) {
            style = "style=\"color:blue\"";
          }

          if (!bncc && especificacoesUtilizados.includes(parseInt(id))) {
            style = "style=\"color:blue\"";
          }

          $(elemento).append(`<option value="${id}" ${style}>${value}</option>`);
        }

        $(elemento).trigger("chosen:updated");
      }

        var submitButton = $j('#btn_enviar');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
          if (!copy) {
            tentaEditarPlanoAula();
          }
        });

        function tentaEditarPlanoAula () {
            conteudos = pegarConteudos();

            var urlForVerificarPlanoAulaSendoUsado = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'verificar-plano-aula-sendo-usado-conteudo', {});

            var options = {
                type     : 'POST',
                url      : urlForVerificarPlanoAulaSendoUsado,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
                    conteudos               : conteudos,
                },
                success  : handleTentaEditarPlanoAula
            };

            postResource(options);
        }

        function handleTentaEditarPlanoAula (response) {
            registrosAula = response.frequencia_ids;
            let obrigatorio_conteudo        = document.getElementById("obrigatorio_conteudo").value;

            if ((obrigatorio_conteudo.length != 1 && obrigatorio_conteudo != '1') || registrosAula.length == 0) {
                editarPlanoAula();
            } else {
                openModal();
            }
        }

        function ehDataValida (d) {
          return d instanceof Date && !isNaN(d);
        }

        function ehComponentesCurricularesValidos (componentesCurriculares) {
          return componentesCurriculares.every(componenteCurricular => !isNaN(parseInt(componenteCurricular[1], 10)));
        }

        function componentesCurricularesPreenchidos (componentesCurriculares, componentesCurricularesGeral) {
          let componentesCurricularesFiltrados = [];
          let componentesUnique = [];

          $.each(componentesCurricularesGeral, function(i, el){
            if($.inArray(el, componentesUnique) === -1) componentesUnique.push(el);
          });

          componentesCurriculares.forEach(componenteCurricular => {
            componentesCurricularesFiltrados.push(componenteCurricular[1]);
          });

          return JSON.stringify(componentesCurricularesFiltrados) == JSON.stringify(componentesUnique);
        }

        function ehBNCCsValidos (bnccs) {
          return bnccs.every(bncc => bncc[1].length > 0);
        }

        function ehBNCCEspecificacoesValidos (bnccEspecificacoes) {
          return bnccEspecificacoes.every(bnccsEspecificacao => bnccsEspecificacao[1].length > 0);
        }

        function ehConteudosValidos (conteudos) {
          return conteudos.every(conteudo => conteudo[1] !== "" && conteudo[1] != null);
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

      function pegarSomenteValoresComponentesCurriculares () {
        var componentesCurriculares = []

        tr_objetivos_aprendizagens = document.getElementsByName("tr_objetivos_aprendizagem[]");
        tr_objetivos_aprendizagens.forEach(tr_objetivos_aprendizagem => {
          var id = tr_objetivos_aprendizagem.children[0].children[0].id;
          var componenteCurricularElemento = document.getElementById(id);
          var componenteCurricularValor = componenteCurricularElemento.value;

          componentesCurriculares.push(componenteCurricularValor);
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

      function pegarComponentesCurricularesGeral() {
        let componentesCurricularesGeral = [];
        let linhasElemento = document.getElementsByName("tr_objetivos_aprendizagem[]");
        let componentesCurricularesElementos = []

        linhasElemento.forEach(linhaElemento => {
          componentesCurricularesElementos.push(linhaElemento.children[0].children[0]);
        });

        componentesCurricularesElementos.forEach(componenteCurricularElemento => {
          $(componenteCurricularElemento).find('option').each(function() {
            if ($(this).val() != '' && $(this).val() != 0) {
              componentesCurricularesGeral.push($(this).val());
            }
          });
        });

        return componentesCurricularesGeral;
      }

        function editarPlanoAula () {
          let data_inicial              = dataParaBanco(document.getElementById("data_inicial").value);
          let data_final                = dataParaBanco(document.getElementById("data_final").value);
          let ddp = $j('#ddp').val(); //metodologia
          let atividades = $j('#atividades').val();
          let recursos_didaticos = $j('#recursos_didaticos').val();
          let registro_adaptacao = $j('#registro_adaptacao').val();
          let referencias = $j('#referencias').val();
          let componentesCurriculares   = pegarComponentesCurriculares();
          let componentesCurricularesGeral   = pegarComponentesCurricularesGeral();
          let bnccs                     = pegarBNCCs();
          let bnccEspecificacoes        = pegarBNCCEspecificacoes();
          let turma                     = document.getElementById("ref_cod_turma").value;
          let faseEtapa                 = document.getElementById("fase_etapa").value;
          let obrigatorio_conteudo        = document.getElementById("obrigatorio_conteudo").value;
          var ref_cod_escola            = document.getElementById("ref_cod_escola").value;

          // VALIDAÇÃO
          if (!ehDataValida(new Date(data_inicial))) { alert("Data inicial não é válida."); return; }
          if (!ehDataValida(new Date(data_final))) { alert("Data final não é válida."); return; }
          if (isNaN(parseInt(turma, 10))) { alert("Turma é obrigatória."); return; }
          if (isNaN(parseInt(faseEtapa, 10))) { alert("Etapa é obrigatória."); return; }
          if (ddp == null || ddp == '') { alert("Metodologia é obrigatória."); return; }
          if (atividades == null) { alert("O campo atividades não é válido."); return; }
          if (referencias == null) { alert("O campo referências não é válido."); return; }
          if (!ehComponentesCurricularesValidos(componentesCurriculares)) { alert("Os componentes curriculares são obrigatórios."); return; }
          if (!componentesCurricularesPreenchidos(componentesCurriculares, componentesCurricularesGeral)) { alert("Existem componentes sem planejamento."); }
          if (!ehBNCCsValidos(bnccs)) { alert("As habilidades são obrigatórias."); return; }
          if (!ehBNCCEspecificacoesValidos(bnccEspecificacoes)) { alert("As especificações são obrigatórias."); return; }
          if (obrigatorio_conteudo.length == 1 && obrigatorio_conteudo == '1' && !ehConteudosValidos(conteudos)) { alert("Os conteúdos são obrigatórios."); return; }
          if (recursos_didaticos == null) { alert("O campo recursos didáticos não é válido."); return; }
          if (registro_adaptacao == null) { alert("O campo registro de adaptação não é válido."); return; }

            var urlForEditarPlanoAula = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'editar-plano-aula', {});

            var options = {
                type     : 'POST',
                url      : urlForEditarPlanoAula,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
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
                    registro_adaptacao      : registro_adaptacao,
                    ref_cod_escola          : ref_cod_escola
                },
                success  : handleEditarPlanoAula
            };

            postResource(options);
        }

        function handleEditarPlanoAula (response) {
            if(response.result == "Edição efetuada com sucesso.") {
                messageUtils.success('Plano de aula editado com sucesso!');

                delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_lst.php", '_self'));
            } else {
                messageUtils.success('Erro desconhecido ocorreu.');
            }
        }

        function openModal() {
            var quantidadeRegistrosAula = registrosAula.length;

            $j("#dialog-warning-editar-plano-aula").find('#msg').html(getMessageEditarPlanoAula(quantidadeRegistrosAula));
            $j("#dialog-warning-editar-plano-aula").dialog("open");
        }

        function closeModal() {
            registrosAula = [];

            $j("#dialog-warning-editar-plano-aula").dialog('close');
        }

        function getMessageEditarPlanoAula(quantidadeRegistrosAula) {
            return ` \
                <span> \
                    Não é possível prosseguir com a edição porque <b> um ou mais conteúdos </b> estão sendo utilizados em \
                    <b>${quantidadeRegistrosAula}</b> registro(s) de aula. O que deseja fazer? \
                </span><br> \
            `;
        }

        function verRegistrosAula () {
            for (let index = 0; index < registrosAula.length; index++) {
                const registroAula = registrosAula[index];

                const url = "http://" + window.location.host + "/intranet/educar_professores_frequencia_cad.php?id=" + registroAula;
                urlHelper(url, '_blank');
            }
        }

        function urlHelper (href, mode) {
            Object.assign(document.createElement('a'), {
            target: mode,
            href: href,
            }).click();
        }

        function delay (time) {
            return new Promise(resolve => setTimeout(resolve, time));
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

        function pegarId (name) {
            let id = name;
            id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

            return id;
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

        $j('body').append(
            '<div id="dialog-warning-editar-plano-aula' + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
            '<div id="msg" class="msg"></div>' +
            '</div>'
        );

        $j('#dialog-warning-editar-plano-aula').find(':input').css('display', 'block');

        $j("#dialog-warning-editar-plano-aula").dialog({
            autoOpen: false,
            closeOnEscape: false,
            draggable: false,
            width: 820,
            modal: true,
            resizable: false,
            title: 'Dependências detectadas',
            open: function(event, ui) {
                $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            },
            buttons: {
                "Cancelar": function () {
                    closeModal();
                },
                "Ver registro(s) afetado(s)": function () {
                    verRegistrosAula();
                }
            }
        });
    });
})(jQuery);
