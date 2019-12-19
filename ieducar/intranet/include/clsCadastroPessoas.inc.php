<?php

trait clsCadastroEndereco
{
    protected function criaCamposEnderecamento()
    {
        // Detalhes do Endereço
        if ($this->idlog && $this->idbai) {
            $objLogradouro = new clsLogradouro($this->idlog);
            $detalheLogradouro = $objLogradouro->detalhe();
            if ($detalheLogradouro) {
                $this->municipio_id = $detalheLogradouro['idmun'];
            }

            $sql = "SELECT iddis FROM public.bairro
            WHERE idbai = '{$this->idbai}'";

            $options = ['return_only' => 'first-field'];
            $this->distrito_id = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

        // Caso seja um endereço externo, tentamos então recuperar a cidade pelo cep
        } elseif ($this->cep) {
            $numCep = idFederal2int($this->cep);

            $sql = "SELECT idmun, count(idmun) as count_mun FROM public.logradouro l, urbano.cep_logradouro cl
              WHERE cl.idlog = l.idlog AND cl.cep = '{$numCep}' group by idmun order by count_mun desc limit 1";

            $options = ['return_only' => 'first-field'];
            $result = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            if ($result) {
                $this->municipio_id = $result;
            }
        }

        if ($this->cod_pessoa_fj) {
            $objPE = new clsPessoaEndereco($this->cod_pessoa_fj);
            $det = $objPE->detalhe();

            if ($det) {
                $this->bairro_id = $det['idbai'];
                $this->logradouro_id = $det['idlog'];
                $sql = "SELECT iddis FROM public.bairro
                WHERE idbai = '{$this->bairro_id}'";

                $options = ['return_only' => 'first-field'];
                $this->distrito_id = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            }
        }

        if (!($this->bairro_id && $this->municipio_id && $this->logradouro_id && $this->distrito_id)) {
            $this->bairro_id = null;
            $this->municipio_id = null;
            $this->logradouro_id = null;
        }

        $this->campoOculto('idbai', $this->idbai);
        $this->campoOculto('idlog', $this->idlog);
        $this->campoOculto('cep', $this->cep);
        $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
        $this->campoOculto('ref_idtlog', $this->idtlog);
        $this->campoOculto('id_cidade', $this->cidade);

        // o endereçamento é opcional
        $enderecamentoObrigatorio = false;

        // considera como endereço localizado por CEP quando alguma das variaveis de instancia
        // idbai (bairro) ou idlog (logradouro) estão definidas, neste caso desabilita a edição
        // dos campos definidos via CEP.
        //$desativarCamposDefinidosViaCep = ($this->idbai || $this->idlog);

        // Caso o cep já esteja definido, os campos já vem desbloqueados inicialmente
        $desativarCamposDefinidosViaCep = empty($this->cep);

        $this->campoRotulo('enderecamento', '<b> Endereçamento</b>', '', '', 'Digite um CEP ou clique na lupa para<br/> busca avançada para começar');

        $this->campoCep(
            'cep_',
            'CEP',
            $this->cep,
            $enderecamentoObrigatorio,
            '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro2.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
            false
        );

        $options = ['label' => 'Município', 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $helperOptions = [
            'objectName' => 'municipio',
            'hiddenInputOptions' => ['options' => ['value' => $this->municipio_id]]
        ];

        $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

        $options = ['label' => 'Distrito', 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $helperOptions = [
            'objectName' => 'distrito',
            'hiddenInputOptions' => ['options' => ['value' => $this->distrito_id]]
        ];

        $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->bairro_id]]];

        $options = [ 'label' => 'Bairro / Zona de Localização - <b>Buscar</b>', 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

        $options = [
            'label' => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
            'placeholder' => 'Bairro',
            'value' => $this->bairro,
            'max_length' => 40,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('bairro', $options);

        // zona localização

        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localização', $zonas);

        $options = [
            'label' => '',
            'placeholder' => 'Zona localização',
            'value' => $this->zona_localizacao,
            'disabled' => $desativarCamposDefinidosViaCep,
            'resources' => $zonas,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->select('zona_localizacao', $options);

        $helperOptions = ['hiddenInputOptions' => ['options' => ['value' => $this->logradouro_id]]];

        $options = ['label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep];

        $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

        // tipo logradouro

        $options = [
            'label' => 'Tipo / Logradouro - <b>Cadastrar</b>',
            'value' => $this->idtlog,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        ];

        $helperOptions = [
            'attrName' => 'idtlog'
        ];

        $this->inputsHelper()->tipoLogradouro($options, $helperOptions);

        // logradouro

        $options = [
            'label' => '',
            'placeholder' => 'Logradouro',
            'value' => $this->logradouro,
            'max_length' => 150,
            'disabled' => $desativarCamposDefinidosViaCep,
            'required' => $enderecamentoObrigatorio
        ];

        $this->inputsHelper()->text('logradouro', $options);
    }

    protected function createOrUpdateEndereco($pessoaId)
    {
        if ($this->cep_ && is_numeric($this->bairro_id) && is_numeric($this->logradouro_id)) {
            $this->_createOrUpdatePessoaEndereco($pessoaId);
        } elseif ($this->cep_ && is_numeric($this->municipio_id) && is_numeric($this->distrito_id)) {
            if (!is_numeric($this->bairro_id)) {
                if ($this->canCreateBairro()) {
                    $this->bairro_id = $this->createBairro();
                } else {
                    return;
                }
            }

            if (!is_numeric($this->logradouro_id)) {
                if ($this->canCreateLogradouro()) {
                    $this->logradouro_id = $this->createLogradouro();
                } else {
                    return;
                }
            }

            $this->_createOrUpdatePessoaEndereco($pessoaId);
        } else {
            $endereco = new clsPessoaEndereco($pessoaId);
            $endereco->exclui();
        }
    }

    protected function canCreateBairro()
    {
        return !empty($this->bairro) && !empty($this->zona_localizacao);
    }

    protected function canCreateLogradouro()
    {
        return !empty($this->logradouro) && !empty($this->idtlog);
    }

    protected function createBairro()
    {
        $objBairro = new clsBairro(null, $this->municipio_id, null, addslashes($this->bairro), $this->currentUserId());
        $objBairro->zona_localizacao = $this->zona_localizacao;
        $objBairro->iddis = $this->distrito_id;

        return $objBairro->cadastra();
    }

    protected function createLogradouro()
    {
        $objLogradouro = new clsLogradouro(
            null,
            $this->idtlog,
            $this->logradouro,
            $this->municipio_id,
            null,
            'S',
            $this->currentUserId()
        );

        return $objLogradouro->cadastra();
    }

    protected function _createOrUpdatePessoaEndereco($pessoaId)
    {
        $cep = idFederal2Int($this->cep_);

        $objCepLogradouro = new ClsCepLogradouro($cep, $this->logradouro_id);

        if (! $objCepLogradouro->existe()) {
            $objCepLogradouro->cadastra();
        }

        $objCepLogradouroBairro = new ClsCepLogradouroBairro();
        $objCepLogradouroBairro->cep = $cep;
        $objCepLogradouroBairro->idbai = $this->bairro_id;
        $objCepLogradouroBairro->idlog = $this->logradouro_id;

        if (! $objCepLogradouroBairro->existe()) {
            $objCepLogradouroBairro->cadastra();
        }

        $endereco = new clsPessoaEndereco(
            $pessoaId,
            $cep,
            $this->logradouro_id,
            $this->bairro_id,
            $this->numero,
            addslashes($this->complemento),
            false,
            addslashes($this->letra),
            addslashes($this->bloco),
            $this->apartamento,
            $this->andar
        );

        // forçado exclusão, assim ao cadastrar endereco_pessoa novamente,
        // será excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
        $endereco->exclui();
        $endereco->cadastra();
    }
}
