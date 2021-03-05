<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Avaliacao
 * @subpackage  Modules
 *
 * @since   Arquivo disponível desde a versão ?
 *
 * @version   $Id$
 */

/**
 * MatriculaTransferidoController class.
 *
 * @author     Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  Modules
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class TransferidoController extends ApiCoreController
{
    protected function canGetTransferido()
    {
        return $this->validatesId('turma') &&
           $this->validatesPresenceOf('ano');
    }

    protected function getTransferido()
    {
        if ($this->canGetTransferido()) {
            $matriculas = new clsPmieducarMatricula();
            $matriculas->setOrderby('sequencial_fechamento , translate(nome,\''.Portabilis_String_Utils::toLatin1(åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ).'\', \''.Portabilis_String_Utils::toLatin1(aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN).'\') ');
            $matriculas = $matriculas->lista_transferidos(
                null,
                null,
                $this->getRequest()->escola_id,
                $this->getRequest()->serie_id,
                null,
                null,
                $this->getRequest()->aluno_id,
                '4',
                null,
                null,
                null,
                null,
                $ativo = 1,
                $this->getRequest()->ano,
                null,
                $this->getRequest()->instituicao_id,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->getRequest()->curso_id,
                null,
                $this->getRequest()->matricula_id,
                null,
                null,
                null,
                null,
                $this->getRequest()->turma_id,
                null,
                false
            ); // Mostra alunos em abandono/transferidos se não existir nenhuma matricula_turma ativa pra outra turma

            $options = [];

            foreach ($matriculas as $matricula) {
                $options['__' . $matricula['cod_matricula']] = $this->toUtf8($matricula['nome']);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'transferidos')) {
            $this->appendResponse($this->getTransferido());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
