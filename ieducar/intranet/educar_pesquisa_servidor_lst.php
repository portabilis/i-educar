<?php

use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_servidor;
    public $ref_idesco;
    public $ref_cod_funcao;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $horario;
    public $lst_matriculas;
    public $ref_cod_instituicao;
    public $professor;
    public $ref_cod_escola;
    public $nome_servidor;
    public $ref_cod_servidor;
    public $periodo;
    public $carga_horaria_usada;
    public $min_mat;
    public $min_ves;
    public $min_not;
    public $dia_semana;
    public $ref_cod_disciplina;
    public $ref_cod_curso;
    public $matutino   = false;
    public $vespertino = false;
    public $noturno    = false;
    public $identificador;
    public $ano_alocacao;
    public function Gerar()
    {
        Session::put([
          'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
          'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
          'dia_semana' => $_GET['dia_semana'] ?? Session::get('dia_semana'),
          'hora_inicial' => $_GET['hora_inicial'] ?? Session::get('hora_inicial'),
          'hora_final' => $_GET['hora_final'] ?? Session::get('hora_final'),
          'professor' => $_GET['professor'] ?? Session::get('professor'),
          'horario' => $_GET['horario'] ?? Session::get('horario'),
          'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get('ref_cod_escola'),
          'min_mat' => $_GET['min_mat'] ?? Session::get('min_mat'),
          'min_ves' => $_GET['min_ves'] ?? Session::get('min_ves'),
          'min_not' => $_GET['min_not'] ?? Session::get('min_not'),
          'ref_cod_disciplina' => $_GET['ref_cod_disciplina'] ?? Session::get('ref_cod_disciplina'),
          'ref_cod_curso' => $_GET['ref_cod_curso'] ?? Session::get('ref_cod_curso'),
          'ano_alocacao' => $_GET['ano_alocacao'] ?? Session::get('ano_alocacao'),
          'identificador' => $_GET['identificador'] ?? Session::get('identificador'),
          'lst_matriculas' => $_GET['lst_matriculas'] ?? Session::get('lst_matriculas'),
          'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ? $_GET['ref_cod_instituicao'] : Session::get('ref_cod_instituicao'),
          'ref_cod_servidor' => $_GET['ref_cod_servidor'] ? $_GET['ref_cod_servidor'] : Session::get('ref_cod_servidor'),
      ]);

        if (!isset($_GET['tipo'])) {
            Session::forget([
              'setAllField1',
              'setAllField2',
              'tipo',
          ]);
        }

        $this->ref_cod_escola      = Session::get('ref_cod_escola');
        $this->ref_cod_instituicao = Session::get('ref_cod_instituicao');
        $this->ref_cod_servidor    = Session::get('ref_cod_servidor');
        $this->professor           = Session::get('professor');
        $this->horario             = Session::get('horario');
        $this->min_mat             = Session::get('min_mat');
        $this->min_ves             = Session::get('min_ves');
        $this->min_not             = Session::get('min_not');
        $this->ref_cod_disciplina  = Session::get('ref_cod_disciplina');
        $this->ref_cod_curso       = Session::get('ref_cod_curso');
        $this->identificador       = Session::get('identificador');
        $this->ano_alocacao        = Session::get('ano_alocacao');
        $this->lst_matriculas        = Session::get('lst_matriculas');

        Session::put('tipo', $_GET['tipo'] ?? Session::get('tipo'));

        $this->titulo = 'Servidores P&uacute;blicos - Listagem';
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = $val === '' ? null : $val;
        }
        if (isset($this->lst_matriculas)) {
            $this->lst_matriculas = urldecode($this->lst_matriculas);
        }
        $string1 = ($this->min_mat - floor($this->min_mat / 60) * 60);
        $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
        $string2 = floor($this->min_mat / 60);
        $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
        $hr_mat  = $string2.':'.$string1;
        $string1 = ($this->min_ves - floor($this->min_ves / 60) * 60);
        $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
        $string2 = floor($this->min_ves / 60);
        $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
        $hr_ves  = $string2.':'.$string1;
        $string1 = ($this->min_not - floor($this->min_not / 60) * 60);
        $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
        $string2 = floor($this->min_not / 60);
        $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
        $hr_not  = $string2.':'.$string1;
        $hora_inicial_ = explode(':', Session::get('hora_inicial'));
        $hora_final_   = explode(':', Session::get('hora_final'));
        $h_m_ini = ((int)$hora_inicial_[0] * 60) + $hora_inicial_[1];
        $h_m_fim = ((int)$hora_final_[0]   * 60) + $hora_final_[1];
        if ($h_m_ini >= 480 && $h_m_ini <= 720) {
            $this->matutino = true;
            if ($h_m_fim >= 721 && $h_m_fim <= 1080) {
                $this->vespertino = true;
            } elseif (($h_m_fim >= 1801 && $h_m_fim <= 1439) || ($h_m_fim == 0)) {
                $this->noturno = true;
            }
        } elseif ($h_m_ini >= 721 && $h_m_ini <= 1080) {
            $this->vespertino = true;
            if (($h_m_fim >= 1081 && $h_m_fim <= 1439)) {
                $this->noturno = true;
            }
        } elseif (($h_m_ini >= 1081 && $h_m_ini <= 1439) || ($h_m_ini == 0)) {
            $this->noturno = true;
        }
        $this->addCabecalhos([
      'Nome do Servidor',
      'Matrícula',
      'Instituição'
    ]);
        $this->campoTexto('nome_servidor', 'Nome Servidor', $this->nome_servidor, 30, 255, false);
        $this->campoOculto('tipo', $_GET['tipo']);
        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_{$this->nome}']) ? $_GET['pagina_{$this->nome}'] * $this->limite-$this->limite: 0;
        $obj_servidor = new clsPmieducarServidor();
        $obj_servidor->setOrderby('carga_horaria ASC');
        $obj_servidor->setLimite($this->limite, $this->offset);
        if (Session::has(['dia_semana', 'hora_inicial', 'hora_final'])) {
            $array_hora = [
          Session::get('dia_semana'),
          Session::get('hora_inicial'),
          Session::get('hora_final')
      ];
        }
        // Marca a disciplina como NULL se não for informada, restringindo a busca
        // aos professores e não selecionar aqueles em que o curso não seja
        // globalizado e sem disciplinas cadastradas
        $this->ref_cod_disciplina = $this->ref_cod_disciplina ?
      $this->ref_cod_disciplina : null;
        // Passa NULL para $alocacao_escola_instituicao senão o seu filtro anula
        // um anterior (referente a selecionar somente servidores não alocados),
        // selecionando apenas servidores alocados na instituiÃ§Ã£o
        $lista = $obj_servidor->lista(
            null,
            null,
            $this->ref_idesco,
            $this->carga_horaria,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao,
            Session::get('tipo'),
            $array_hora,
            $this->ref_cod_servidor,
            $this->nome_servidor,
            true,
            $this->horario,
            false,
            $this->lst_matriculas,
            $this->matutino,
            $this->vespertino,
            $this->noturno,
            $this->ref_cod_escola,
            $hr_mat,
            $hr_ves,
            $hr_not,
            Session::get('dia_semana'),
            $this->ref_cod_escola,
            $this->identificador,
            $this->ref_cod_curso,
            $this->ref_cod_disciplina,
            null,
            null,
            $this->ano_alocacao
        );

        // Se for uma listagem de professores, recupera as disciplinas dadas para
        // comparaÃ§Ã£o com a de outros professores (somente quando a busca Ã© para
        // substituiÃ§Ã£o de servidores)
        $disciplinas = [];
        if ('true' == $this->professor) {
            $disciplinas = $obj_servidor->getServidorDisciplinasQuadroHorarioHorarios(
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            );
        }
        $total = $obj_servidor->_total;
        // pega detalhes de foreign_keys
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($lista[0]['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $nm_instituicao = $det_ref_cod_instituicao['nm_instituicao'];

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_servidor      = new clsFuncionario($registro['cod_servidor']);
                $det_cod_servidor      = $obj_cod_servidor->detalhe();
                $registro['matricula'] = $det_cod_servidor['matricula'];
                // Se servidor for professor, verifica se possui as mesmas
                // disciplinas do servidor a ser substituido (este passo somente Ã©
                // executado ao buscar um servidor substituto)
                if ($this->professor == 'true') {
                    $disciplinasSubstituto = clsPmieducarServidor::getServidorDisciplinas(
                        $registro['cod_servidor'],
                        $this->ref_cod_instituicao
                    );
                    // Se os arrays diferirem, passa para o prÃ³ximo resultado
                    if ($disciplinasSubstituto != $disciplinas) {
                        continue;
                    }
                }
                $campo1 = Session::get('campo1');
                $campo2 = Session::get('campo2');
                if (Session::get('tipo')) {
                    if (is_string($campo1) && is_string($campo2)) {
                        if (is_string(Session::get('horario'))) {
                            $script = " onclick=\"addVal1('{$campo1}','{$registro['nome']}','{$registro['cod_servidor']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                        } else {
                            $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); fecha();\"";
                        }
                    } elseif (is_string($campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                    }
                } else {
                    if (is_string($campo1) && is_string($campo2)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                    } elseif (is_string($campo2)) {
                        $script = " onclick=\"addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                    } elseif (is_string($campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                    }
                }
                $this->addLinhas([
          "<a href=\"javascript:void(0);\" $script>{$registro['nome']}</a>",
          "<a href=\"javascript:void(0);\" $script>{$registro['matricula']}</a>",
          "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>"
        ]);
            }
        }
        $this->addPaginador2(
            'educar_pesquisa_servidor_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );
        $obj_permissoes = new clsPermissoes();
        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-servidor-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = '0';
        $this->renderMenu = true;
        $this->renderMenuSuspenso = false;
    }
};
