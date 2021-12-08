<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class extends clsCadastro {
    public $bncc;
    public $observacao;

    public function Inicializar () {
        $this->titulo = 'Contéudo Ministrado - Cadastro';

        $retorno = 'Novo';

        // $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_conteudo_ministrado_lst.php');

        // if (is_numeric($this->id)) {
        //     $tmp_obj = new clsModulesFrequencia();
        //     $registro = $tmp_obj->detalhe(
        //         $this->id
        //     );

        //     if ($registro) {
        //         // passa todos os valores obtidos no registro para atributos do objeto
        //         foreach ($registro['detalhes'] as $campo => $val) {
        //             $this->$campo = $val;
        //         }
        //         $this->matriculas = $registro['matriculas'];
        //         $this->ref_cod_serie = $registro['detalhes']['ref_cod_serie']; 

        //         $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
        //         $retorno = 'Editar';

        //         $this->titulo = 'Frequência - Edição';
        //     }
        // }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_frequencia_det.php?id=%d', $this->id)
            : 'educar_professores_frequencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' contéudo ministrado', [
            url('intranet/educar_conteudo_ministrado_index.php') => 'Professores',
        ]);

        return $retorno;
    }

    public function Gerar () {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $helperOptions = [
            'objectName' => 'anos_letivos'
        ];

        dump($this->getBNCC());
        $this->bncc = array_values(array_intersect($this->bncc, $this->getBNCC()));

        $options = [
            'label' => 'Base nacional comum curricular',
            'required' => true,
            'size' => 50,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $this->getBNCC()
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $this->campoMemo('observacao', 'Observação', $this->observacao, 100, 5, true);
    }

    public function Novo() {

        return false;
    }

    public function Excluir () {
        
        return false;
    }

    private function getBNCC()
    {
        $BNCC = [];

        dump('Entrou 1');
        // if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();

            $db->Consulta("
                WITH select_ as (
                    SELECT
                        id,
                        code,
                        description,
                        field_of_experience,
                        thematic_unit,
                        discipline,
                        unnest(grades) as grade
                    FROM
                        public.learning_objectives_and_skills
                )
                    SELECT * FROM select_ WHERE grade = '9' AND discipline = '5'
            ");

            $db->ProximoRegistro();

            while ($db->ProximoRegistro()) {
                $BNCC[] = $db->Tupla();
            }
            // dump($BNCC);
        // }

        return array_combine($BNCC, $BNCC);
    }

    public function __construct () {
        parent::__construct();
    }

    public function Formular () {
        $this->title = 'Contéudo ministrado';
        $this->processoAp = '58';
    }
};
