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
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       ?
 * @version     $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * ComponenteCurricular_Model_CodigoEducacenso class.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       ?
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_CodigoEducacenso extends CoreExt_Enum
{

  protected $_data = array(
    null => 'Selecione',
    1    => 'Química',
    2    => 'Física',
    3    => 'Matemática',
    4    => 'Biologia',
    5    => 'Ciências',
    6    => 'Língua/Literatura portuguesa',
    7    => 'Língua/Literatura extrangeira - Inglês',
    8    => 'Língua/Literatura extrangeira - Espanhol',
    30   => 'Língua/Literatura extrangeira - Francês',
    9    => 'Língua/Literatura extrangeira - Outra',
    10   => 'Artes (educação artística, teatro, dança, música, artes plásticas e outras)',
    11   => 'Educação física',
    12   => 'História',
    13   => 'Geografia',
    14   => 'Filosofia',
    28   => 'Estudos sociais',
    29   => 'Sociologia',
    16   => 'Informática/Computação',
    17   => 'Discilpinas profissionalizantes',
    20   => 'Disciplinas voltadas ao atendimento às necessidades educacionais especificas dos alunos que são alvo da educação
           especial e às praticas educacionais inclusivas',
    21   => 'Disciplinas voltadas à diversidade sociocultural',
    23   => 'LIBRAS',
    25   => 'Disciplinas pedagógicas',
    26   => 'Ensino religioso',
    27   => 'Língua indígena',
    99   => 'Outras disciplinas'
  );

  public function getData(){
    return $this->_data;
  }

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}
