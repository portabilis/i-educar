<?php

return new class extends clsDetalhe {
    public $titulo;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $this->titulo = 'Usuário de transporte - Detalhe';

        $cod_pt = $_GET['cod_pt'];

        $tmp_obj = new clsModulesPessoaTransporte($cod_pt);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('transporte_empresa_lst.php');
        }

        $this->addDetalhe(['Código', $cod_pt]);
        $this->addDetalhe(['Pessoa', $registro['nome_pessoa']]);
        $this->addDetalhe(['Rota', $registro['nome_rota']]);
        $this->addDetalhe(['Destino', (trim($registro['nome_destino'])=='' ? $registro['nome_destino2'] : $registro['nome_destino'])]);
        $this->addDetalhe(['Ponto de embarque', $registro['nome_ponto'] ]);
        $this->addDetalhe(['Observação', $registro['observacao']]);

        $turnos = [
      1 => 'Matutino',
      2 => 'Vespertino',
      3 => 'Noturno',
      4 => 'Integral',
      5 => 'Matutino e vespertino',
      6 => 'Matutino e noturno',
      7 => 'Vespertino e noturno'
    ];
        $nm_turno = $turnos[$registro['turno']] ?? '';
        $this->addDetalhe(['Turno', $nm_turno]);
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21240, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = '../module/TransporteEscolar/Pessoatransporte';
            $this->url_editar = "../module/TransporteEscolar/Pessoatransporte?id={$cod_pt}";
        }

        $this->url_cancelar = 'transporte_pessoa_lst.php';

        $this->largura = '100%';

        $this->breadcrumb('Detalhe do usuário de transporte', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Usuários de transporte';
        $this->processoAp = 21240;
    }
};
