<?php

namespace App\Models {
    class LegacyCourseEducacensoStage
    {
        public static function query()
        {
            return new class {
                public function updateOrCreate($a, $b = null) {}
                public function where($column, $operator = null, $value = null) { return $this; }
                public function whereNotIn($column, $values) { return $this; }
                public function delete() { return true; }
            };
        }

        public static function getIdsByCourse($course) { return []; }
    }

    class LegacyEducacensoStages
    {
        public static function getDescriptiveValues() { return []; }
    }

    class LegacyEducationLevel {}
    class LegacyEducationType {}
    class LegacyRegimeType {}
}

namespace {
    use PHPUnit\Framework\TestCase;

    if (class_exists(\Illuminate\Database\Eloquent\Model::class) && interface_exists(\Illuminate\Database\ConnectionResolverInterface::class)) {
        \Illuminate\Database\Eloquent\Model::setConnectionResolver(new class implements \Illuminate\Database\ConnectionResolverInterface {
            public function connection($name = null) {
                return new class {
                    public function getPdo() { return null; }
                    public function getQueryGrammar() {}
                    public function getPostProcessor() {}
                    public function getSchemaGrammar() {}
                    public function getSchemaBuilder() { return new class { public function hasColumn($table, $column) { return false; } }; }
                };
            }
            public function getDefaultConnection() { return null; }
            public function setDefaultConnection($name) {}
        });
    }

    class clsCadastro
    {
        public function getRequest()
        {
            return (object) ['etapacurso' => []];
        }

        public function simpleRedirect($url) {}
        public function inputsHelper() { return new class { public function select(...$a) {} public function multipleSearchCustom(...$a) {} }; }
        public function campoOculto(...$a) {}
        public function campoLista(...$a) {}
        public function campoTexto(...$a) {}
        public function campoNumero(...$a) {}
        public function campoMonetario(...$a) {}
        public function campoCheck(...$a) {}
        public function campoMemo(...$a) {}
        public function breadcrumb(...$a) {}
    }

    class clsPermissoes {}

    class clsPmieducarCurso
    {
        public static $lastArgs = [];
        public $modalidade_curso;

        public function __construct(
            $ref_usuario_cad = null,
            $ref_cod_tipo_regime = null,
            $ref_cod_nivel_ensino = null,
            $ref_cod_tipo_ensino = null,
            $nm_curso = null,
            $sgl_curso = null,
            $qtd_etapas = null,
            $carga_horaria = null,
            $ato_poder_publico = null,
            $objetivo_curso = null,
            $publico_alvo = null,
            $ativo = null,
            $ref_cod_instituicao = null,
            $padrao_ano_escolar = null,
            $hora_falta = null,
            $multi_seriado = null,
            $importar_curso_pre_matricula = null,
            $descricao = null,
            $bloquear_novas_matriculas = null,
            $cod_curso = null,
            $ref_usuario_exc = null
        ) {
            self::$lastArgs = compact(
                'ref_usuario_cad',
                'ref_cod_tipo_regime',
                'ref_cod_nivel_ensino',
                'ref_cod_tipo_ensino',
                'nm_curso',
                'sgl_curso',
                'qtd_etapas',
                'carga_horaria',
                'ato_poder_publico',
                'objetivo_curso',
                'publico_alvo',
                'ativo',
                'ref_cod_instituicao',
                'padrao_ano_escolar',
                'hora_falta',
                'multi_seriado',
                'importar_curso_pre_matricula',
                'descricao',
                'bloquear_novas_matriculas',
                'cod_curso',
                'ref_usuario_exc'
            );
        }

        public function cadastra() { return 1; }
        public function detalhe() { return []; }
        public function edita() { return true; }
        public function excluir() { return true; }
    }

    final class CourseHoraFaltaTest extends TestCase
    {
        protected function setUp(): void
        {
            clsPmieducarCurso::$lastArgs = [];
        }

        public function test_negative_hora_falta_is_rejected_after_fix()
        {
            $path = __DIR__ . '/../../ieducar/intranet/educar_curso_cad.php';
            $page = include $path;

            $page->pessoa_logada = 1;
            $page->incluir = null;
            $page->excluir_ = null;
            $page->nm_curso = 'Curso Teste';
            $page->sgl_curso = 'CT';
            $page->ref_cod_nivel_ensino = 1;
            $page->ref_cod_tipo_ensino = 1;
            $page->ref_cod_tipo_regime = 1;
            $page->ref_cod_instituicao = 1;
            $page->qtd_etapas = 1;
            $page->carga_horaria = '10';
            $page->hora_falta = '-30';

            $page->Novo();

            $this->assertEmpty(clsPmieducarCurso::$lastArgs, 'Negative hora_falta should prevent creation');
        }

        public function test_positive_hora_falta_is_accepted()
        {
            $path = __DIR__ . '/../../ieducar/intranet/educar_curso_cad.php';
            $page = include $path;

            $page->pessoa_logada = 1;
            $page->incluir = null;
            $page->excluir_ = null;
            $page->nm_curso = 'Curso Teste';
            $page->sgl_curso = 'CT';
            $page->ref_cod_nivel_ensino = 1;
            $page->ref_cod_tipo_ensino = 1;
            $page->ref_cod_tipo_regime = 1;
            $page->ref_cod_instituicao = 1;
            $page->qtd_etapas = 1;
            $page->carga_horaria = '10';
            $page->hora_falta = '30';

            $page->Novo();

            $this->assertArrayHasKey('hora_falta', clsPmieducarCurso::$lastArgs);
            $this->assertGreaterThanOrEqual(0, clsPmieducarCurso::$lastArgs['hora_falta']);
        }
    }
}