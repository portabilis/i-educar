<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Imagem
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/imagem/clsPortalImagemTipo.inc.php';
require_once 'include/imagem/clsPortalImagem.inc.php';

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Imagem
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Banco de Imagens');
        $this->processoAp = '473';
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Imagem
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    public $pessoa_logada;
    public $nome_reponsavel;

    public $cod_imagem;
    public $ref_cod_imagem_tipo;
    public $caminho;
    public $nm_imagem;
    public $extensao;
    public $img_altura;
    public $img_largura;
    public $data_cadastro;
    public $ref_cod_pessoa_cad;
    public $data_exclusao;
    public $ref_cod_pessoa_exc;

    public function Inicializar()
    {
        $retorno = 'Novo';

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_imagem = $_GET['cod_imagem'];

        if ($this->cod_imagem) {
            $obj = new clsPortalImagem($this->cod_imagem);

            $detalhe  = $obj->detalhe();

            $this->nm_tipo             = $detalhe['nm_tipo'];
            $this->ref_cod_imagem_tipo = $detalhe['ref_cod_imagem_tipo'];
            $this->caminho             = $detalhe['caminho'];
            $this->nm_imagem           = $detalhe['nm_imagem'];
            $this->extensao            = $detalhe['extensao'];
            $this->img_altura          = $detalhe['altura'];
            $this->img_largura         = $detalhe['largura'];
            $this->data_cadastro       = dataFromPgToBr($detalhe['data_cadastro']);
            $this->ref_cod_pessoa_cad  = $detalhe['ref_cod_pessoa_cad'];
            $this->data_exclusao       = dataFromPgToBr($detalhe['data_exclusao']);
            $this->ref_cod_pessoa_exc  = $detalhe['ref_cod_pessoa_exc'];
            $this->fexcluir = true;
            $retorno = 'Editar';
        }

        $this->url_cancelar = $retorno == 'Editar' ?
      'imagem_det.php?cod_imagem=' . $this->cod_imagem : 'imagem_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_imagem', $this->cod_imagem_tipo);
        $ObjTImagem = new clsPortalImagemTipo();
        $TipoImagem = $ObjTImagem->lista();
        $listaTipo = [];

        if ($TipoImagem) {
            foreach ($TipoImagem as $dados) {
                $listaTipo[$dados['cod_imagem_tipo']] = $dados['nm_tipo'];
            }
        }

        $this->campoOculto('cod_imagem', $this->cod_imagem);
        $this->campoOculto('img_altura', $this->img_altura);
        $this->campoOculto('img_largura', $this->img_largura);
        $this->campoOculto('extensao', $this->extensao);
        $this->campoLista('ref_cod_imagem_tipo', 'Tipo da Imagem', $listaTipo, $this->ref_cod_imagem_tipo);
        $this->campoTexto('nm_imagem', 'Nome da Imagem', $this->nm_imagem, 30, 255, true);
        $this->campoArquivo('caminho', 'Imagem', $this->caminho, 30);
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj = new clsPortalImagem(
        false,
        $this->ref_cod_imagem_tipo,
        'caminho',
      $this->nm_imagem,
        false,
        false,
        false,
        false,
        $this->pessoa_logada,
      false,
        false
    );

        if ($obj->cadastra()) {
            header('Location: imagem_lst.php');
        }

        return false;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $obj = new clsPortalImagem(
        $this->cod_imagem,
        $this->ref_cod_imagem_tipo,
      'caminho',
        $this->nm_imagem,
        false,
        false,
        false,
        false,
        $this->pessoa_logada,
      false,
        false
    );

        if ($obj->edita()) {
            header("Location: imagem_det.php?cod_imagem={$this->cod_imagem}");
        }

        return true;
    }

    public function Excluir()
    {
        $ObjImg = new clsPortalImagem($this->cod_imagem);
        $ObjImg->excluir();
        header('Location: imagem_lst.php');
    }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
