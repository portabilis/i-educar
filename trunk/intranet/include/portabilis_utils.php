<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("portabilis/dal.php");

#TODO refatorar estas classes / mover para intranet/include/portabilis/
class Utils
{
  function __construct()
  {
    $this->db = new Db();
  }
}

class User extends Utils
{
  function __construct()
  {
    $this->_setUserId();
    parent::__construct();
  }

  function getInstituicaoId()
  {
    return $this->_getUserInfo($this->userId, 'instituicao_id');
  }

  function _setUserId()
  {
    @session_start();#precisa ?
    $this->userId = isset($_SESSION['id_pessoa']) ? $_SESSION['id_pessoa'] : null;
    session_write_close();#precisa ?
  }

  function isLoggedIn()
  {
    return $this->userId != null;
  }

  function _getUserInfo($userId, $fieldName)
  {
    if (! isset($this->_info))
    {
      $s = "SELECT $fieldName from (select funcionario.ref_cod_pessoa_fj AS id,
	funcionario.matricula AS username,
	usuario.ref_cod_escola as escola_id,
	usuario.ref_cod_instituicao as instituicao_id,
	(SELECT funcao.professor as is_professor FROM pmieducar.servidor_funcao, pmieducar.funcao
	 WHERE servidor_funcao.ref_ref_cod_instituicao = usuario.ref_cod_instituicao AND servidor_funcao.ref_cod_servidor = usuario.cod_usuario AND funcao.professor = 1 AND servidor_funcao.ref_cod_funcao = funcao.cod_funcao LIMIT 1) as is_professor FROM portal.funcionario, pmieducar.usuario WHERE funcionario.ativo = 1 and usuario.ativo = 1 and funcionario.ref_cod_pessoa_fj = usuario.cod_usuario AND
usuario.cod_usuario = $userId LIMIT 1 ) AS usuario";

  #evita novas consultas no banco a cada nova solicitação das informações

      $this->_info = $this->db->selectField($s);
    }
    return $this->_info;
  }

  function isProfessor()
  {
    return $this->_getUserInfo($this->userId, 'is_professor') == 1;
  }
}

class Professor extends Utils
{
  function __construct($userId)
  {
    $this->userId = $userId;

    if (! $this->userId)
      die('Invalid user id for instance of class Professor');

    parent::__construct();
  }

  function getEscolasByInstituicao($instituicao_id)
  {
    $s = "SELECT * from (select ref_cod_servidor as servidor_id, ref_ref_cod_instituicao as instituicao_id, ref_cod_escola as escola_id, (select juridica.fantasia from escola, cadastro.juridica where cod_escola = ref_cod_escola and escola.ref_idpes = juridica.idpes limit 1) as escola_nome, carga_horaria, periodo, hora_final, hora_inicial, dia_semana FROM pmieducar.servidor_alocacao WHERE ref_cod_servidor  = $this->userId and	ativo = 1  and ref_ref_cod_instituicao = $instituicao_id) AS professor_escola";

  return $this->db->select($s);
  }

  function getCursosByInstituicaoEscola($instituicaoId, $escolaId)
  {
    $s = "SELECT * from (SELECT ref_cod_curso AS curso_id, (select nm_curso from pmieducar.curso where cod_curso = ref_cod_curso limit 1) AS curso_nome, ref_ref_cod_instituicao as instituicao_id FROM pmieducar.servidor_curso_ministra
WHERE servidor_curso_ministra.ref_ref_cod_instituicao = $instituicaoId and servidor_curso_ministra.ref_cod_servidor = $this->userId and ref_cod_curso in (select ref_cod_curso from escola_curso where ref_cod_escola = $escolaId)) AS professor_curso";
  return $this->db->select($s);
  }

  function getComponentesCurriculares($instituicaoId, $cursoId, $escolaId, $turmaId, $anoEscolar)
  {
//    $s = "SELECT * from (SELECT ref_cod_disciplina as componente_curricular_id, cc.nome as componente_curricular_nome FROM pmieducar.servidor_disciplina, modules.componente_curricular as cc WHERE servidor_disciplina.ref_ref_cod_instituicao = $instituicaoId and servidor_disciplina.ref_cod_servidor = $this->userId and servidor_disciplina.ref_cod_curso = $cursoId and ref_cod_disciplina = cc.id) AS professor_disciplina";

echo '1';
    $s = "select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome from modules.componente_curricular_turma as cct, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al, pmieducar.servidor_disciplina as scc where cct.turma_id = $turmaId and cct.escola_id = $escolaId and cct.componente_curricular_id = cc.id and al.ano = $anoEscolar and cct.escola_id = al.ref_cod_escola and scc.ref_ref_cod_instituicao = $instituicaoId and scc.ref_cod_servidor = $this->userId and scc.ref_cod_curso = $cursoId and scc.ref_cod_disciplina = cc.id";

    $componentes = $this->db->select($s);
    if (count($componentes))
      return $componentes;
echo $s;
    $s = "select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome from pmieducar.turma as	t, pmieducar.escola_serie_disciplina as esd, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al, pmieducar.servidor_disciplina as scc where t.cod_turma = $turmaId and esd.ref_ref_cod_escola = $escolaId and t.ref_ref_cod_serie = esd.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $anoEscolar and 
	esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and esd.ativo = 1 and al.ativo = 1 and	scc.ref_ref_cod_instituicao = $instituicaoId and scc.ref_cod_servidor = $this->userId and scc.ref_cod_curso = $cursoId and scc.ref_cod_disciplina = cc.id";

  return $this->db->select($s);
  }
}
/*
$user = new User();
if ($user->isLoggedIn() and $user->isProfessor())
{
  $p = new Professor($user->userId);
  $escolas = $p->getEscolasByInstituicao($user->instituicaoId);

  foreach($escolas as $e)
  {
    echo "<strong><br />Escola professor logged with user id '$user->userId':</strong> <br />";
    var_dump($e);

    $cursos = $p->getCursosByInstituicaoEscola($e['instituicao_id'], $e['escola_id']);

    foreach($cursos as $c)
    {
      echo "<br /><strong>Curso professor logged with user id '$user->userId':</strong> <br />";
      var_dump($c);
      
      $componentes_curriculares = $p->getComponentesCurriculares($c['instituicao_id'], $c['curso_id'], $e['escola_id'], 36, 2011);
      echo "<br /><strong>Componentes curriculares professor logged with user id '$user->userId':</strong> <br />";
      var_dump($componentes_curriculares);
    }
  }
}
else if ($user->isLoggedIn() and ! $user->isProfessor($user->userId))
  echo "User logged with id '$user->userId' not is professor!";
else
  echo 'not logged yet!';
*/
?>
