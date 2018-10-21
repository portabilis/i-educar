<?php

require_once 'CoreExt/Entity.php';

class Docente_Model_Licenciatura extends CoreExt_Entity
{
    protected $_data = [
      'servidor'     => null,
      'licenciatura' => null,
      'curso'        => null,
      'anoConclusao' => null,
      'ies'          => null,
      'user'         => null,
      'created_at'   => null,
      'updated_at'   => null
  ];

    protected $_references = [
        'licenciatura' => [
            'value' => null,
            'class' => 'App_Model_SimNao',
            'file'  => 'App/Model/SimNao.php'
        ],
        'ies' => [
            'value' => null,
            'class' => 'Educacenso_Model_IesDataMapper',
            'file'  => 'Educacenso/Model/IesDataMapper.php'
        ],
        'curso' => [
            'value' => null,
            'class' => 'Educacenso_Model_CursoSuperiorDataMapper',
            'file'  => 'Educacenso/Model/CursoSuperiorDataMapper.php'
        ]
  ];

    public function getDefaultValidatorCollection()
    {
        return [];
    }
}
