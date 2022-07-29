(function ($) {
  $(document).ready(function () { 
    var id = $j('#id').val();
    var copy = $j('#copy').val();
    var ficha_aee_id    = document.getElementById('ficha_aee_id');

    var submitButton = $j('#btn_enviar');
    submitButton.removeAttr('onclick');

    submitButton.click(function () {
      if (ficha_aee_id == '' || isNaN(ficha_aee_id) && copy === '') {
        enviarFormulario();
      }
    });

    function enviarFormulario() {
      var data = dataParaBanco(document.getElementById("data").value);  
      var turma = document.getElementById("ref_cod_turma").value;
      var matricula = document.getElementById("ref_cod_matricula").value;         
      var necessidades_aprendizagem = document.getElementById("necessidades_aprendizagem").value;
      var caracterizacao_pedagogica = document.getElementById("caracterizacao_pedagogica").value; 

      // VALIDAÇÃO
      if (!ehDataValida(new Date(data))) { alert("Data não é válida."); return; }
      if (isNaN(parseInt(turma, 10))) { alert("Turma é obrigatória."); return; }
      if (isNaN(parseInt(matricula, 10))) { alert("Aluno é obrigatório."); return; }
      if (necessidades_aprendizagem == null) { alert("O campo Necessidades de Aprendizagem não é válido."); return; }
      if (caracterizacao_pedagogica == null) { alert("O campo Caracterização Pedagógica não é válido."); return; }

      novaFichaAee(
        data,
        turma,
        matricula,
        necessidades_aprendizagem,
        caracterizacao_pedagogica
      );
    }

    function dataParaBanco(dataFromBrasil) {
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

    function ehDataValida(d) {
      return d instanceof Date && !isNaN(d);
    }

    function novaFichaAee(data, turma, matricula, necessidades_aprendizagem, caracterizacao_pedagogica) {
      var urlForNovaFichaAee = postResourceUrlBuilder.buildUrl('/module/Api/FichaAee', 'nova-ficha-aee', {});

      var options = {
        type: 'POST',
        url: urlForNovaFichaAee,
        dataType: 'json',
        data: {
          data: data,
          turma: turma,
          matricula: matricula,
          necessidades_aprendizagem: necessidades_aprendizagem,
          caracterizacao_pedagogica: caracterizacao_pedagogica
        },
        success: handleNovaFichaAee
      };

      postResource(options);
    }

    function handleNovaFichaAee(response) {
      if (response.result == "Cadastro efetuado com sucesso.") {
        messageUtils.success('Cadastro efetuado com sucesso!');

        delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_ficha_aee_lst.php", '_self'));
      } else {
        messageUtils.error(response.result);
      }
    }

    function delay(time) {
      return new Promise(resolve => setTimeout(resolve, time));
    }

    function urlHelper(href, mode) {
      Object.assign(document.createElement('a'), {
        target: mode,
        href: href,
      }).click();
    }
    
  });
})(jQuery);