
/* document.getElementById('etapa').onchange = function () {
 
  const select = document.querySelector('#etapa');

  const value = select.value;
  console.log(value);
 
}; */
var ajax = new XMLHttpRequest();
ajax.open('GET',"educar_portal_aluno_boletim.php");

var select = document.getElementById('et')

function getBoletim(xml_boletim) {
  console.log(xml_boletim)
}

select.addEventListener('change', function() {
  console.log(select.value)
  var campoMatricula = document.getElementById('matricula').value;

httpRequest.onreadystatechange = function() {

$("#form").submit(function (e) {
  e.preventDefault();

  var campoNota = document.getElementsByClassName('notas').value;
  var campoFalta = document.getElementsByClassName('faltas').value;
  
}) 

$($document).ready(function () {
    $("#enviar").click(function () {
        var form = new FormData($("#form")[0]);
        $.ajax({
            url: 'ieducar\intranet\educar_portal_aluno_boletim_xml.php',
            type: 'post',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: form,
            timeout: 8000,
            success: function (resultado) {
                $("#resposta").html(resultado);
            }
        })
    })
})
if(httpRequest.readystate === 4){
  console.log("Olá")
}else{
  console.log("error")
}
};


  /* var campoAlunos = document.getElementById('alunos');
  campoAlunos.innerHTML = "Carregando alunos..."; */

  var xml_boletim = new ajax(getBoletim);
  xml_boletim.envia("educar_portal_aluno_boletim_xml.php?mat=" + campoMatricula + "&not=" + campoNota + "&falt=" + campoFalta);
  
})



