<?php

class OrdenacaoAlunosApiController extends ApiCoreController
{
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;

    protected function validatesExistenceOfExemplar()
    {
        $valid = true;

        $exemplares = $this->loadExemplar($reload = true);

        if (!is_array($exemplares) || count($exemplares) < 1) {
            $id = $this->getRequest()->exemplar_id;
            $tombo = $this->getRequest()->tombo_exemplar;

            $this->messenger->append("Aparentemente não existe um exemplar com id $id e/ou tombo $tombo, para a biblioteca informada.");
            $valid = false;
        }

        return $valid;
    }

    protected function validatesExistenceOfCliente()
    {
        $valid = true;

        if (!$this->loadCliente()) {
            $this->messenger->append("Não existe um cliente com id '{$this->getRequest()->cliente_id}'.");
            $valid = false;
        }

        return $valid;
    }

    protected function validatesClienteIsNotSuspenso()
    {
        $cliente = $this->loadCliente();

        if ($cliente['suspenso']) {
            $this->messenger->append('Operação não pode ser realizada, pois o cliente esta suspenso.', 'error');

            return false;
        }

        return true;
    }

    protected function canAcceptRequest()
    {
        return parent::canAcceptRequest()

            && $this->validatesPresenceOf([
                'instituicao_id',
                'escola_id',
                'biblioteca_id',
                'cliente_id',
                'tombo_exemplar'
            ])

            && $this->validatesExistenceOfExemplar()
            && $this->validatesExistenceOfCliente();
    }

    protected function canPostEmprestimo()
    {
        return $this->validatesPresenceOf(['exemplar_id'])
            && $this->validatesExistenceOfExemplar()
            && $this->validatesClienteIsNotSuspenso()
            && $this->validatesSituacaoExemplarIsIn('disponivel');

        /*
         #TODO validar:
           qtd emprestimos em aberto do cliente <= limite biblioteca
           valor R$ multas em aberto do cliente <= limite biblioteca

           não existe outro exemplar mesma obra emprestado para cliente
           validates situacao exemplar is disponivel or is reservado cliente
        */
    }

    protected function canPostDevolucao()
    {
        return $this->validatesPresenceOf(['exemplar_id'])
            && $this->validatesExistenceOfExemplar()
            && $this->validatesSituacaoExemplarIsIn(['emprestado', 'emprestado_e_reservado']);
    }

    protected function loadCliente($id = null)
    {
        if (!$id) {
            $id = $this->getRequest()->cliente_id;
        }

        $cliente = new clsPmieducarCliente($id);
        $cliente = $cliente->detalhe();

        if ($cliente) {
            $cliente = Portabilis_Array_Utils::filter($cliente, [
                'cod_cliente' => 'id',
                'ref_idpes' => 'pessoa_id'
            ]);

            $pessoa = new clsPessoa_($cliente['pessoa_id']);
            $pessoa = $pessoa->detalhe();
            $cliente['nome'] = $this->toUtf8($pessoa['nome']);

            $sql = 'select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||\' day\')::interval >= now()';
            $suspenso = $this->fetchPreparedQuery($sql, $params = [$id], true, 'first-field');

            $cliente['suspenso'] = $suspenso == '1';
        }

        return $cliente;
    }

    protected function getDataPrevistaDisponivelForExemplar($exemplar, $dataInicio, $format = 'd/m/Y')
    {
        $qtdDiasEmprestimo = $this->loadQtdDiasEmprestimoForExemplar($exemplar);

        $_format = explode('/', $format);

        if (count($_format) > 0 && $_format[0] == 'd') {
            list($diaInicio, $mesInicio, $anoInicio) = explode('/', $dataInicio);
            $dataInicio = "$mesInicio/$diaInicio/$anoInicio";
        }

        $date = date('Y-m-d', strtotime("+$qtdDiasEmprestimo days", strtotime($dataInicio)));

        // #TODO Caso seja a devolução seja refatorada, separar esse trecho num método para reutilizar código
        $dias_da_semana = ['Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7];

        $obj_biblioteca_dia = new clsPmieducarBibliotecaDia();
        $lst_biblioteca_dia = $obj_biblioteca_dia->lista($this->getRequest()->biblioteca_id);
        if (is_array($lst_biblioteca_dia) && count($lst_biblioteca_dia)) {
            foreach ($lst_biblioteca_dia as $dia_semana) {
                $biblioteca_dias_semana[] = $dia_semana['dia'];
            }
        }

        $biblioteca_dias_folga = array_diff($dias_da_semana, $biblioteca_dias_semana);

        $biblioteca_dias_folga = array_flip($biblioteca_dias_folga);

        $obj_biblioteca_feriado = new clsPmieducarBibliotecaFeriados();
        $lst_biblioteca_feriado = $obj_biblioteca_feriado->lista(null, $this->getRequest()->biblioteca_id);
        if (is_array($lst_biblioteca_feriado) && count($lst_biblioteca_feriado)) {
            foreach ($lst_biblioteca_feriado as $dia_feriado) {
                $biblioteca_dias_feriado[] = dataFromPgToBr($dia_feriado['data_feriado'], 'D Y-m-d');
            }
        }

        $data_entrega = dataFromPgToBr($date, 'D Y-m-d');

        if (!is_array($biblioteca_dias_folga)) {
            $biblioteca_dias_folga = [null];
        }
        if (!is_array($biblioteca_dias_feriado)) {
            $biblioteca_dias_feriado = [null];
        }

        while (in_array(substr($data_entrega, 0, 3), $biblioteca_dias_folga) || in_array(
            $data_entrega,
            $biblioteca_dias_feriado
        )) {
            $data_entrega = date('D Y-m-d ', strtotime("$data_entrega +1 day"));
            $data_entrega = dataFromPgToBr($data_entrega, 'D Y-m-d');
        }

        $data_entrega = dataFromPgToBr($data_entrega, $format);

        return $data_entrega;
    }

    protected function loadReservasForExemplar($exemplar, $clienteId = null, $reload = false)
    {
        if ($reload || !isset($this->_reservas)) {
            $reservas = new clsPmieducarReservas();
            $reservas = $reservas->lista(
                null,
                null,
                null,
                $clienteId,
                null,
                null,
                null,
                null,
                null,
                null,
                $exemplar['id'],
                1,
                $this->getRequest()->biblioteca_id,
                $this->getRequest()->instituicao_id,
                $this->getRequest()->escola_id,
                $data_retirada_null = true
            );

            if ($reservas) {
                $reservas = Portabilis_Array_Utils::filterSet($reservas, [
                    'cod_reserva' => 'id',
                    'data_reserva' => 'data',
                    'ref_cod_cliente' => 'cliente_id',
                    'data_prevista_disponivel'
                ]);

                foreach ($reservas as $index => $reserva) {
                    $cliente = $this->loadCliente($reserva['cliente_id']);

                    $reserva['cliente'] = $cliente;
                    $reserva['nome_cliente'] = $cliente['id'] . ' - ' . $cliente['nome'];
                    $reserva['data'] = date('d/m/Y', strtotime($reserva['data']));
                    $reserva['situacao'] = $this->getSituacaoForFlag('reservado');

                    if ($this->getRequest()->cliente_id == $cliente['id']) {
                        $reserva['data_prevista_disponivel'] = date(
                            'd/m/Y',
                            strtotime($reserva['data_prevista_disponivel'])
                        );
                    } else {
                        $reserva['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar(
                            $exemplar,
                            $reserva['data_prevista_disponivel'],
                            'd/m/Y'
                        );
                    }
                }
            }

            $this->_reservas = $reservas;
        }

        return $this->_reservas;
    }

    protected function loadEmprestimoForExemplar($exemplar = null)
    {
        if (is_null($exemplar)) {
            $exemplar = $this->loadExemplar();
        }

        $emprestimo = new clsPmieducarExemplarEmprestimo();

        $emprestimo = $emprestimo->lista(
            null,
            null,
            null,
            null,
            $exemplar['id'],
            null,
            null,
            null,
            null,
            null,
            $devolvido = false,
            $this->getRequest()->biblioteca_id,
            null,
            $this->getRequest()->instituicao_id,
            $this->getRequest()->escola_id
        );

        if ($emprestimo) {
            $emprestimo = array_shift($emprestimo);
            $emprestimo = Portabilis_Array_Utils::filter($emprestimo, [
                'cod_emprestimo' => 'id',
                'data_retirada' => 'data',
                'ref_cod_cliente' => 'cliente_id'
            ]);

            $cliente = $this->loadCliente($emprestimo['cliente_id']);

            $emprestimo['cliente'] = $cliente;
            $emprestimo['nome_cliente'] = $cliente['id'] . ' - ' . $cliente['nome'];
            $emprestimo['situacao'] = $this->getSituacaoForFlag('emprestado');

            $emprestimo['data'] = date('d/m/Y', strtotime($emprestimo['data']));
            $emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar(
                $exemplar,
                $emprestimo['data'],
                'd/m/Y'
            );
        }

        return $emprestimo;
    }

    protected function existsReservaForExemplar($exemplar = null, $clienteId = null)
    {
        $reservas = $this->loadReservasForExemplar($exemplar, $clienteId, $reload = true);

        return is_array($reservas) && count($reservas) > 0;
    }

    protected function getSituacaoForFlag($flag)
    {
        $situacoes = [
            'indisponivel' => ['flag' => 'indisponivel', 'label' => 'Indisponível'],
            'disponivel' => ['flag' => 'disponivel', 'label' => 'Disponível'],
            'emprestado' => ['flag' => 'emprestado', 'label' => 'Emprestado'],
            'reservado' => ['flag' => 'reservado', 'label' => 'Reservado'],
            'emprestado_e_reservado' => [
                'flag' => 'emprestado_e_reservado',
                'label' => 'Emprestado e reservado'
            ],
            'invalida' => ['flag' => 'invalida', 'label' => 'Inválida']
        ];

        return $situacoes[$flag];
    }

    protected function loadSituacaoForExemplar($exemplar)
    {
        $situacao = new clsPmieducarSituacao($exemplar['situacao_id']);
        $situacao = $situacao->detalhe();

        $reservado = $this->existsReservaForExemplar($exemplar);
        $emprestado = $situacao['situacao_emprestada'] == 1;

        $situacaoPermiteEmprestimo = $situacao['permite_emprestimo'] == 2;
        $exemplarPermiteEmprestimo = $exemplar['permite_emprestimo'] == 2;

        if ($emprestado && $reservado) {
            $flagSituacaoExemplar = 'emprestado_e_reservado';
        } elseif ($emprestado) {
            $flagSituacaoExemplar = 'emprestado';
        } elseif ($reservado) {
            $flagSituacaoExemplar = 'reservado';
        } elseif ($situacaoPermiteEmprestimo && $exemplarPermiteEmprestimo) {
            $flagSituacaoExemplar = 'disponivel';
        } elseif (!$situacaoPermiteEmprestimo || !$exemplarPermiteEmprestimo) {
            $flagSituacaoExemplar = 'indisponivel';
        } else {
            $flagSituacaoExemplar = 'invalida';
        }

        return $this->getSituacaoForFlag($flagSituacaoExemplar);
    }

    protected function getSituacaoExemplar($exemplar = null)
    {
        if (is_null($exemplar)) {
            $exemplar = $this->loadExemplar();
        }

        if ($exemplar['situacao']['flag'] == 'reservado') {
            $exemplar['situacao']['flag'] = $this->validateReservaOfExemplar($exemplar);
        }

        return $exemplar['situacao'];
    }

    protected function validateReservaOfExemplar($exemplar = null)
    {
        if (is_null($exemplar)) {
            $exemplar = $this->loadExemplar();
        }

        $reservas = $this->loadReservasForExemplar($exemplar);
        $cont = 0;
        $clientePossuiReserva = false;
        $codReserva = 0;
        if (is_array($reservas) && count($reservas)) {
            foreach ($reservas as $registro) {
                $cont++;
                if ($registro['cliente_id'] == $this->getRequest()->cliente_id) {
                    $clientePossuiReserva = true;
                    $codReserva = $registro['id'];
                    break;
                }
            }
        }
        if ($clientePossuiReserva) {
            if ($cont == 1) {
                $reservas = new clsPmieducarReservas($codReserva);
                $reservas->data_retirada = date('Y-m-d H:i:s');
                $reservas->edita();
                $return = 'disponivel';
            } else {
                $this->messenger->append('Outros clientes já haviam reservado o exemplar.', 'error');
                $return = 'reservado';
            }
        } elseif ($cont > 0) {
            $this->messenger->append('Outros clientes já haviam reservado o exemplar.', 'error');
            $return = 'reservado';
        }

        return $return;
    }

    protected function getPendenciasForExemplar($exemplar)
    {
        if (!isset($exemplar['situacao'])) {
            throw new CoreExt_Exception('Exemplar deve possuir uma chave \'situacao\' para getPendenciasForExemplar.');
        }

        $situacaoExemplar = $exemplar['situacao'];
        $pendencias = [];

        if (strpos($situacaoExemplar['flag'], 'emprestado') > -1) {
            $emprestimo = $this->loadEmprestimoForExemplar($exemplar);

            if ($emprestimo != false) {
                $pendencias[] = $emprestimo;
            }
        }

        if (strpos($situacaoExemplar['flag'], 'reservado') > -1) {
            $reservas = $this->loadReservasForExemplar($exemplar);

            if ($reservas != false) {
                $pendencias = array_merge($pendencias, $reservas);
            }
        }

        return $pendencias;
    }

    protected function loadAcervo($id = '', $reload = false)
    {
        if (empty($id)) {
            $id = $this->getRequest()->acervo_id;
        }

        if ($reload || !isset($this->_acervos)) {
            $this->_acervos = [];
        }

        if (!isset($this->_acervos[$id])) {
            $acervo = new clsPmieducarAcervo($id);
            $acervo = $acervo->detalhe();

            if ($acervo) {
                $acervo = Portabilis_Array_Utils::filter($acervo, [
                    'cod_acervo' => 'id',
                    'ref_cod_exemplar_tipo' => 'exemplar_tipo_id',
                    'ref_cod_acervo' => 'acervo_referencia_id',
                    'ref_cod_acervo_colecao' => 'colecao_id',
                    'ref_cod_acervo_idioma' => 'idioma_id',
                    'ref_cod_acervo_editora' => 'editora_id',
                    'ref_cod_biblioteca' => 'biblioteca_id',
                    'titulo',
                    'sub_titulo',
                    'cdu',
                    'cutter',
                    'volume',
                    'num_edicao',
                    'ano',
                    'num_paginas',
                    'isbn',
                    'data_cadastro'
                ]);
            }

            $this->_acervos[$id] = $acervo;
        }

        return $this->_acervos[$id];
    }

    protected function loadExemplares($reload = false, $id = null)
    {
        if ($reload || !isset($this->_exemplares)) {
            $exemplares = new clsPmieducarExemplar();

            $exemplares = $exemplares->lista(
                $id,
                null,
                null,
                $this->getRequest()->acervo_id,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                null,
                null,
                null,
                null,
                $this->getRequest()->biblioteca_id,
                null,
                $this->getRequest()->instituicao_id,
                $this->getRequest()->escola_id,
                $this->getRequest()->tombo_exemplar
            );

            if ($exemplares) {
                $exemplares = Portabilis_Array_Utils::filterSet($exemplares, [
                    'cod_exemplar' => 'id',
                    'ref_cod_fonte' => 'fonte_id',
                    'ref_cod_motivo_baixa' => 'motivo_baixa_id',
                    'ref_cod_acervo' => 'acervo_id',
                    'ref_cod_biblioteca' => 'biblioteca_id',
                    'ref_cod_situacao' => 'situacao_id',
                    'permite_emprestimo',
                    'tombo'
                ]);

                foreach ($exemplares as $index => $exemplar) {
                    $acervo = $this->loadAcervo($exemplar['acervo_id']);
                    $exemplares[$index]['acervo'] = [];
                    $exemplares[$index]['acervo']['id'] = $exemplar['acervo_id'];
                    $exemplares[$index]['acervo']['titulo'] = $acervo['titulo'];
                    $exemplares[$index]['acervo']['exemplar_tipo_id'] = $acervo['exemplar_tipo_id'];

                    $exemplares[$index]['exemplar_tipo_id'] = $acervo['exemplar_tipo_id'];

                    $exemplares[$index]['situacao'] = $this->loadSituacaoForExemplar($exemplares[$index]);
                    $exemplares[$index]['pendencias'] = $this->getPendenciasForExemplar($exemplares[$index]);
                }
            }

            $this->_exemplares = $exemplares;
        }

        return $this->_exemplares;
    }

    protected function loadExemplar($reload = false, $id = null)
    {
        if (!$id) {
            $id = $this->getRequest()->exemplar_id;
        }

        return array_shift($this->loadExemplares($reload, $id));
    }

    protected function getExemplares()
    {
        $this->appendResponse('exemplares', $this->loadExemplares($reload = true));
    }

    protected function loadSituacaoExemplar($permiteEmprestimo = true, $padrao = true, $emprestada = false)
    {
        $permiteEmprestimo = $permiteEmprestimo == true ? 2 : 1;
        $emprestada = $emprestada == true ? 1 : 0;

        if (!is_null($padrao)) {
            $padrao = $padrao == true ? 1 : 0;
        }

        $situacao = new clsPmieducarSituacao();
        $situacao = $situacao->lista(
            null,
            null,
            null,
            null,
            $permiteEmprestimo,
            null,
            $padrao,
            $emprestada,
            null,
            null,
            null,
            null,
            1,
            $this->getRequest()->biblioteca_id,
            $this->getRequest()->instituicao_id,
            $this->getRequest()->escola_id
        );

        if ($situacao) {
            $situacao = Portabilis_Array_Utils::filter($situacao[0], [
                'cod_situacao' => 'id',
                'ref_cod_biblioteca' => 'biblioteca_id',
                'nm_situacao' => 'label',
                'situacao_padrao' => 'padrao',
                'situacao_emprestada' => 'emprestada',
                'permite_emprestimo',
                'descricao'
            ]);
        }

        return $situacao;
    }

    protected function updateSituacaoExemplar($newSituacao)
    {
        if (!$newSituacao) {
            throw new CoreExt_Exception('$newSituacao não pode ser falso em updateSituacaoExemplar.');
        }

        $exemplar = new clsPmieducarExemplar();
        $exemplar->cod_exemplar = $this->getRequest()->exemplar_id;
        $exemplar->ref_cod_acervo = $this->getRequest()->acervo_id;
        $exemplar->ref_cod_situacao = $newSituacao['id'];
        $exemplar->ref_usuario_exc = \Illuminate\Support\Facades\Auth::id();

        return $exemplar->edita();
    }

    protected function postEmprestimo()
    {
        if ($this->canPostEmprestimo()) {
            $situacaoEmprestimo = $this->loadSituacaoExemplar(
                $permiteEmprestimo = false,
                $padrao = null,
                $emprestada = true
            );

            if ($situacaoEmprestimo && !$this->updateSituacaoExemplar($situacaoEmprestimo)) {
                $this->messenger->append(
                    'Aparentemente a situação do exemplar não foi alterada para emprestado.',
                    'error'
                );
            } elseif (!$situacaoEmprestimo) {
                $this->messenger->append('Não foi encontrado uma situação cadastrada para emprestimo.', 'error');
            }

            if (!$this->messenger->hasMsgWithType('error')) {
                $emprestimo = new clsPmieducarExemplarEmprestimo();
                $emprestimo->ref_usuario_cad = \Illuminate\Support\Facades\Auth::id();
                $emprestimo->ref_cod_cliente = $this->getRequest()->cliente_id;
                $emprestimo->ref_cod_exemplar = $this->getRequest()->exemplar_id;

                if ($emprestimo->cadastra()) {
                    $this->messenger->append('Emprestimo realizado com sucesso.', 'success');
                } else {
                    $this->messenger->append(
                        'Aparentemente o realizado não foi cadastrado, por favor, tente novamente.',
                        'error'
                    );
                }
            }
        }

        $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
    }

    protected function postDevolucao()
    {
        if ($this->canPostDevolucao()) {
            $situacaoDisponivel = $this->loadSituacaoExemplar(
                $permiteEmprestimo = true,
                $padrao = true,
                $emprestada = false
            );

            if ($situacaoDisponivel && !$this->updateSituacaoExemplar($situacaoDisponivel)) {
                $this->messenger->append(
                    'Aparentemente a situação do exemplar não foi alterada para disponivel.',
                    'error'
                );
            } elseif (!$situacaoDisponivel) {
                $this->messenger->append(
                    'Não foi encontrado uma situação padrão cadastrada para exemplar disponivel.',
                    'error'
                );
            }

            if (!$this->messenger->hasMsgWithType('error')) {
                $_emprestimo = $this->loadEmprestimoForExemplar();
                $emprestimo = new clsPmieducarExemplarEmprestimo();
                $emprestimo->cod_emprestimo = $_emprestimo['id'];
                $emprestimo->ref_usuario_devolucao = \Illuminate\Support\Facades\Auth::id();
                $emprestimo->data_devolucao = date('Y-m-d');

                // TODO calcular / setar valor multa (se) devolução atrasada?

                if ($emprestimo->edita()) {
                    $this->messenger->append('Devolução realizada com sucesso.', 'success');
                } else {
                    $this->messenger->append(
                        'Aparentemente a devolução não foi cadastrada, por favor, tente novamente.',
                        'error'
                    );
                }
            }
        }

        $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'exemplares')) {
            $this->getExemplares();
        } elseif ($this->isRequestFor('post', 'emprestimo')) {
            $this->postEmprestimo();
        } elseif ($this->isRequestFor('post', 'devolucao')) {
            $this->postDevolucao();
        } else {
            $this->notImplementedOperationError();
        }
    }
}
