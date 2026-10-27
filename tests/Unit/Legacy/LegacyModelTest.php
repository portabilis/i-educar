<?php

use iEducar\Legacy\Model;

test('volta a listar todos os campos da tabela', function () {
    $model = new Model;
    $model->_todos_campos = 'a, b';
    $model->setCamposLista('a');
    $model->resetCamposLista();

    expect($model->_campos_lista)->toBe('a, b');
});
