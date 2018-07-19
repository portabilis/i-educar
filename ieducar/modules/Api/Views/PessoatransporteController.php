<?php

require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';
require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class PessoatransporteController extends ApiCoreController
{

    protected $_processoAp = 21240; //verificar
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

    protected function loadNomePessoaj($id)
    {
        $sql = 'select nome from cadastro.pessoa, modules.pessoa_transporte where idpes = ref_idpes_destino and cod_pessoa_transporte = $1';
        $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

        return $this->toUtf8($nome, ['transform' => true]);
    }

    protected function loadNomePessoa($id)
    {
        $sql = 'select nome from cadastro.pessoa, modules.pessoa_transporte where idpes = ref_idpes and cod_pessoa_transporte = $1';
        $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

        return $this->toUtf8($nome, ['transform' => true]);
    }

    protected function createOrUpdatePessoaTransporte($id = null)
    {
        $pt = new clsModulesPessoaTransporte();
        $pt->cod_pessoa_transporte = $id;

        // após cadastro não muda mais id pessoa
        $pt->ref_idpes = $this->getRequest()->pessoa_id;
        $pt->ref_idpes_destino = $this->getRequest()->pessoaj_id;
        $pt->ref_cod_ponto_transporte_escolar = $this->getRequest()->ponto;
        $pt->ref_cod_rota_transporte_escolar = $this->getRequest()->rota;
        $pt->observacao = Portabilis_String_Utils::toLatin1($this->getRequest()->observacao);
        $pt->turno = $this->getRequest()->turno;

        return (is_null($id) ? $pt->cadastra() : $pt->edita());
    }

    protected function get()
    {
        $id = $this->getRequest()->id;
        $pt = new clsModulesPessoaTransporte();
        $pt->cod_pessoa_transporte = $id;
        $pt = $pt->detalhe();

        $attrs = [
            'cod_pessoa_transporte' => 'id',
            'ref_cod_rota_transporte_escolar' => 'rota',
            'ref_cod_ponto_transporte_escolar' => 'ponto',
            'ref_idpes_destino' => 'pessoaj',
            'ref_idpes' => 'pessoa',
            'observacao' => 'observacao',
            'turno' => 'turno'
        ];

        $pt = Portabilis_Array_Utils::filter($pt, $attrs);

        $pt['pessoaj_nome'] = $this->loadNomePessoaj($id);
        $pt['pessoa_nome'] = $this->loadNomePessoa($id);

        return $pt;
    }

    protected function validateSizeOfObservacao()
    {
        if (strlen($this->getRequest()->observacao)<=255) {
            return true;
        } else {
            $this->messenger->append('O campo Observações não pode ter mais que 255 caracteres.');

            return false;
        }
    }

    protected function post()
    {
        if ($this->validateSizeOfObservacao()) {
            $id = $this->createOrUpdatePessoaTransporte();
            if (is_numeric($id)) {
                $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o vinculo não pode ser cadastrada, por favor, verifique.');
            }
        }

        return ['id' => $id];
    }

    protected function put()
    {
        if ($this->validateSizeOfObservacao()) {
            $id = $this->getRequest()->id;
            $editou = $this->createOrUpdatePessoaTransporte($id);

            if ($editou) {
                $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o vinculo não pode ser alterado, por favor, verifique.');
            }
        }

        return ['id' => $id];
    }

    protected function delete()
    {
        $id = $this->getRequest()->id;

        $pt = new clsModulesPessoaTransporte();
        $pt->cod_pessoa_transporte = $id;

        if ($pt->excluir()) {
            $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append(
                'Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                'error',
                false,
                'error'
            );
        }

        return ['id' => $id];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pessoatransporte')) {
            $this->appendResponse($this->get());
        }

        // create
        elseif ($this->isRequestFor('post', 'pessoatransporte')) {
            $this->appendResponse($this->post());
        }

        // update
        elseif ($this->isRequestFor('put', 'pessoatransporte')) {
            $this->appendResponse($this->put());
        } elseif ($this->isRequestFor('delete', 'pessoatransporte')) {
            $this->appendResponse($this->delete());
            echo '<script language= "JavaScript">
                location.href="intranet/transporte_pessoa_lst.php";
                </script>';

            die();
        } else {
            $this->notImplementedOperationError();
        }
    }
}
