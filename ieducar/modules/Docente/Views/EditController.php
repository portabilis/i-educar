<?php

require_once 'Core/Controller/Page/EditController.php';
require_once 'Educacenso/Model/CursoSuperiorDataMapper.php';
require_once 'Educacenso/Model/IesDataMapper.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';
require_once 'include/public/clsPublicUf.inc.php';

class EditController extends Core_Controller_Page_EditController
{
    protected $_dataMapper        = 'Docente_Model_LicenciaturaDataMapper';
    protected $_titulo            = 'Cadastro de Curso Superior/Licenciatura';
    protected $_processoAp        = 635;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_saveOption        = true;
    protected $_deleteOption      = true;
    protected $_formMap = [
        'servidor' => [
            'label'  => '',
            'help'   => '',
            'entity' => 'servidor'
        ],
        'licenciatura' => [
            'label'  => 'Licenciatura',
            'help'   => '',
            'entity' => 'licenciatura'
        ],
        'curso' => [
            'label'  => 'Curso',
            'help'   => '',
            'entity' => 'curso'
        ],
        'anoConclusao' => [
            'label'  => 'Ano conclusão',
            'help'   => '',
            'entity' => 'anoConclusao'
        ],
        'ies' => [
            'label'  => 'IES',
            'help'   => '',
            'entity' => 'ies'
        ],
        'user' => [
            'label'  => '',
            'help'   => '',
            'entity' => 'user'
        ],
        'created_at' => [
            'label'  => '',
            'help'   => '',
            'entity' => 'created_at'
        ]
    ];
    protected function _preConstruct()
    {
        $params = [
            'id'          => $this->getRequest()->id,
            'servidor'    => $this->getRequest()->servidor,
            'instituicao' => $this->getRequest()->instituicao
        ];

        $this->setOptions(['new_success_params'  => $params]);
        $this->setOptions(['edit_success_params' => $params]);

        unset($params['id']);

        $this->setOptions(['delete_success_params' => $params]);
    }

    /**
     * @see clsCadastro#Gerar()
     */
    public function Gerar()
    {
        $this->campoOculto('id', $this->getEntity()->id);
        $this->campoOculto('servidor', $this->getRequest()->servidor);

        $cursoSuperiorMapper = new Educacenso_Model_CursoSuperiorDataMapper();
        $cursos = $cursoSuperiorMapper->findAll([], [], ['id' => 'ASC', 'nome' => 'ASC']);

        // Licenciatura
        $licenciatura = $this->getEntity()->get('licenciatura')
            ? $this->getEntity()->get('licenciatura')
            : 0;

        $this->campoRadio(
            'licenciatura',
            $this->_getLabel('licenciatura'),
            [1 => 'Sim', 0 => 'Não'],
            $licenciatura
        );

        // Curso
        $opcoes = [];

        foreach ($cursos as $curso) {
            $opcoes[$curso->id] = $curso->nome;
        }

        $this->campoLista(
            'curso',
            $this->_getLabel('curso'),
            $opcoes,
            $this->getEntity()->get('curso')
        );

        // Ano conclusão
        $opcoes = range(1960, date('Y'));

        rsort($opcoes);

        $opcoes = array_combine($opcoes, $opcoes);

        $this->campoLista(
      'anoConclusao',
            $this->_getLabel('anoConclusao'),
            $opcoes,
            $this->getEntity()->anoConclusao
        );

        // UF da IES.
        $ufs = new clsPublicUf();
        $ufs = $ufs->lista();

        $opcoes = [];

        foreach ($ufs as $uf) {
            $opcoes[$uf['sigla_uf']] = $uf['sigla_uf'];
        }

        ksort($opcoes);

        // Caso não seja uma instância persistida, usa a UF do locale.
        $uf = $this->getEntity()->ies->uf
            ? $this->getEntity()->ies->uf
            : config('legacy.app.locale.province');

        $this->campoLista('uf', 'UF', $opcoes, $uf, 'getIes()');

        // IES.
        $opcoes = [];
        $iesMapper = new Educacenso_Model_IesDataMapper();
        $iesUf = $iesMapper->findAll([], ['uf' => $uf]);

        foreach ($iesUf as $ies) {
            $opcoes[$ies->id] = $ies->nome;
        }

        // Adiciona a instituição "Não cadastrada".
        $ies = $iesMapper->find(['ies' => 9999999]);
        $opcoes[$ies->id] = $ies->nome;

        $this->campoLista(
      'ies',
            $this->_getLabel('ies'),
            $opcoes,
            $this->getEntity()->ies->id
        );

        $this->url_cancelar = sprintf(
            'index?servidor=%d&instituicao=%d',
            $this->getRequest()->servidor,
            $this->getRequest()->instituicao
        );

        // Javascript para Ajax.
        echo
<<<EOT
      <script type="text/javascript">
      function getIes()
      {
        var ies = document.getElementById('ies').value;
        var uf  = document.getElementById('uf').value;

        var url  = '/modules/Educacenso/Views/IesAjaxController.php';
        var pars = '?uf=' + uf;

        var xml1 = new ajax(getIesXml);
        xml1.envia(url + pars);
      }

      function getIesXml(xml)
      {
        var ies = document.getElementById('ies');

        ies.length     = 1;
        ies.options[0] = new Option('Selecione uma IES', '', false, false);

        var iesItems = xml.getElementsByTagName('ies');

        for (var i = 0; i < iesItems.length; i++) {
          ies.options[ies.options.length] = new Option(
            iesItems[i].firstChild.nodeValue, iesItems[i].getAttribute('id'), false, false
          );
        }

        if (ies.length == 1) {
          ies.options[0] = new Option(
            'A UF não possui IES.', '', false, false
          );
        }
      }
      </script>
EOT;
    }

    public function Novo()
    {
        $_POST['user']       = $this->getOption('id_usuario');
        $_POST['created_at'] = 'NOW()';

        parent::Novo();
    }
}
