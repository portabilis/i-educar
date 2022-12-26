$('#ref_cod_componente_curricular').on('change', function() {
    var value= this.value ;
    $.ajax({

        url:'action.php',
        type:'POST',
        data: { 
            'value':value
        },

        success: function(data) {
            alert(data);
        }
    });
})