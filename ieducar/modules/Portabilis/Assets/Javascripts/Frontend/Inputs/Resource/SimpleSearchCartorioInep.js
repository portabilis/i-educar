var  simpleSearchCartorioCertCivilInepOptions= {

    params : { 
        sigla_uf_cartorio : () => $j('#uf_emissao_certidao_civil').val()
    },

    canSearch : function() {
        if (!$j('#uf_emissao_certidao_civil').val()) {
            alert('Informe o estado de emissão.');
            return false;
        }
        
        return true;
    }
};
