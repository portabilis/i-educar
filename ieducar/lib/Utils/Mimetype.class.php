<?php

/*
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
 */

/*
 * Copyright (C) 2002 Jason Sheets <jsheets@shadonet.com>.
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE PROJECT AND CONTRIBUTORS ``AS IS'' AND
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the project nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE PROJECT AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE PROJECT OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

/**
 * mimetype class.
 *
 * Essa classe é uma modificação da classe mimetype de Jason Sheets. A classe
 * original é distribuída sobre uma licença BSD. Essa classe estava modificada
 * dentro do arquivo intranet/download.php mas para melhorar a testabilidade,
 * foi refatorada para a sua própria classe novamente.
 *
 * Essa classe poderá a vir ser depreciada em favor do uso da extensão PECL
 * {@link http://php.net/fileinfo fileinfo} do PHP 5.2 (no core na versão 5.3).
 * No entanto, essa dependência só deverá ser implantada quando um instalador
 * ou processo de verificação de dependência estiver disponível.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @author   Jason Sheets <jsheets@shadonet.com>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @license  http://opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.phpclasses.org/browse/file/2743.html  Código fonte original
 * @package  Core
 * @since    Classe disponível desde a versão 1.1.0
 * @todo     Verificar dual-licensing do arquivo
 * @todo     Substituir por fileinfo e adicionar dependência na aplicação
 * @version  $Id$
 */
class Mimetype
{

  public function getType($filename)
  {
    $filename = basename($filename);
    $filename = explode('.', $filename);
    $filename = $filename[count($filename)-1];

    return $this->privFindType($filename);
  }

  protected function privFindType($ext)
  {
    $mimetypes = $this->privBuildMimeArray();

    if (isset($mimetypes[$ext])) {
      return $mimetypes[$ext];
    }
    else {
      return FALSE;
    }
  }

  protected function privBuildMimeArray() {
    return array(
      'doc' => 'application/msword',
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
      'pdf' => 'application/pdf',
      'xls' => 'application/vnd.ms-excel',
    );
  }
}