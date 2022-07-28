(function ($) {
  $(document).ready(function () {

    var id = $j('#id').val();
    var copy = $j('#copy').val();

    if (isNaN(id) || id === '')
      return;

    if (!isNaN(id) && copy)
      return;

    var ficha_aee_id = $j('#id').val();

    var submitButton = $j('#btn_enviar');
    submitButton.removeAttr('onclick');

    submitButton.click(function () {
      editarFichaAee();
    });

    function editarFichaAee() {
      var data = dataParaBanco(document.getElementById("data").value);  
      var turma = document.getElementById("ref_cod_turma").value;
      var matricula = document.getElementById("ref_cod_matricula").value;         
      var necessidades_aprendizagem = document.getElementById("necessidades_aprendizagem").value;
      var caracterizacao_pedagogica = document.getElementById("caracterizacao_pedagogica").value; 

      // VALIDAÇÃO
      if (necessidades_aprendizagem == null || necessidades_aprendizagem == '') { alert("O campo Necessidades de Aprendizagem é obrigatório."); return; }
      if (caracterizacao_pedagogica == null || caracterizacao_pedagogica == '') { alert("O campo Caracterização Pedagógica é obrigatório."); return; }

      var urlForEditarFichaAee = postResourceUrlBuilder.buildUrl('/module/Api/FichaAee', 'editar-ficha-aee', {});

      var options = {
        type: 'POST',
        url: urlForEditarFichaAee,
        dataType: 'json',
        data: {
          ficha_aee_id: ficha_aee_id,
          data: data,
          turma: turma,
          matricula: matricula,
          necessidades_aprendizagem: necessidades_aprendizagem,
          caracterizacao_pedagogica: caracterizacao_pedagogica
        },
        success: handleEditarFichaAee
      };

      postResource(options);
    }

    
    function handleEditarFichaAee(response) {
      if (response.result) {
          messageUtils.success('Ficha AEE editada com sucesso!');

          delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_ficha_aee_lst.php", '_self'));
      } else {
          messageUtils.success('Erro desconhecido ocorreu.');
      }
  }

  function urlHelper(href, mode) {
      Object.assign(document.createElement('a'), {
          target: mode,
          href: href,
      }).click();
  }

  function delay(time) {
      return new Promise(resolve => setTimeout(resolve, time));
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

  });
})(jQuery);
