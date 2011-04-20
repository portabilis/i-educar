<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/* @(#) $Header: /sources/code128php/code128php/code128barcode.class.php,v 1.5 2007/11/10 11:31:28 harding Exp $
 *
 * code128barcode
 *
 * produce a sequence of 1 and 0, according to code128 barcode
 * format, 1 means black and 0 white.
 *
 * USAGE:
 *   $code = new code128barcode;
 *   $bars = $code->output('My code 0123');
 *      // or $bars = $code->output(array('FNC2','abc'));
 *   $checksum = $code->checksum; // optional
 *   $error = $code->error
 *   [see example_fpdf.php for an example using fpdf]
 *   [and example_png.php  for an example using gd  ]
 *
 *   Copyright (C) 2006  Thomas HARDING
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Library General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this library; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
 *   MA  02110-1301  USA 
 *
 *   mailto:thomas.harding@laposte.net
 *   Thomas Harding, 56 rue de la bourie rouge, 45 000 ORLEANS -- FRANCE
 *   
 *
 * code128 specs are available at 
 * http://www.adams1.com/pub/russadam/128code.html
 *
 */

class code128barcode {
    //
    // PUBLIC VARIABLES
    //
    var $output_string = '';
    var $checksum = 0;
    var $error_level = E_USER_WARNING;
    var $error = false;
    var $unoptimized = false;
    var $nozerofill = false;
    
    //
    // PRIVATE VARIABLES
    //
    
    // {{{ private variables
    var $_pattern = array(), $_CODEA = array(), $_CODEB = array(), $_CODEC = array();
    var $_specialcodes = array(), $_startcodes = array();
    var $_barcode = array();
    var $_checksum = 0, $_count = 0;
    var $_current_code = '_CODEB';
    var $_actual_code = '_CODEB';
    // }}}
   
    //
    // PUBLIC FUNCTIONS
    //
   
    // {{{ function code128barcode()
    function code128barcode() {
       
        //error_reporting(E_ALL);
       
        //Fill barcode patterns 
			$this->_pattern[0]		= "212222";
			$this->_pattern[1]		= "222122";
			$this->_pattern[2]		= "222221";
			$this->_pattern[3]		= "121223";
			$this->_pattern[4]		= "121322";
			$this->_pattern[5]		= "131222";
			$this->_pattern[6]		= "122213";
			$this->_pattern[7]		= "122312";
			$this->_pattern[8]		= "132212";
			$this->_pattern[9]		= "221213";
			$this->_pattern[10]		= "221312";
			$this->_pattern[11]		= "231212";
			$this->_pattern[12]		= "112232";
			$this->_pattern[13]		= "122132";
			$this->_pattern[14]		= "122231";
			$this->_pattern[15]		= "113222";
			$this->_pattern[16]		= "123122";
			$this->_pattern[17]		= "123221";
			$this->_pattern[18]		= "223211";
			$this->_pattern[19]		= "221132";
			$this->_pattern[20]		= "221231";
			$this->_pattern[21]		= "213212";
			$this->_pattern[22]		= "223112";
			$this->_pattern[23]		= "312131";
			$this->_pattern[24]		= "311222";
			$this->_pattern[25]		= "321122";
			$this->_pattern[26]		= "321221";
			$this->_pattern[27]		= "312212";
			$this->_pattern[28]		= "322112";
			$this->_pattern[29]		= "322211";
			$this->_pattern[30]		= "212123";
			$this->_pattern[31]		= "212321";
			$this->_pattern[32]		= "232121";
			$this->_pattern[33]		= "111323";
			$this->_pattern[34]		= "131123";
			$this->_pattern[35]		= "131321";
			$this->_pattern[36]		= "112313";
			$this->_pattern[37]		= "132113";
			$this->_pattern[38]		= "132311";
			$this->_pattern[39]		= "211313";
			$this->_pattern[40]		= "231113";
			$this->_pattern[41]		= "231311";
			$this->_pattern[42]		= "112133";
			$this->_pattern[43]		= "112331";
			$this->_pattern[44]		= "132131";
			$this->_pattern[45]		= "113123";
			$this->_pattern[46]		= "113321";
			$this->_pattern[47]		= "133121";
			$this->_pattern[48]		= "313121";
			$this->_pattern[49]		= "211331";
			$this->_pattern[50]		= "231131";
			$this->_pattern[51]		= "213113";
			$this->_pattern[52]		= "213311";
			$this->_pattern[53]		= "213131";
			$this->_pattern[54]		= "311123";
			$this->_pattern[55]		= "311321";
			$this->_pattern[56]		= "331121";
			$this->_pattern[57]		= "312113";
			$this->_pattern[58]		= "312311";
			$this->_pattern[59]		= "332111";
			$this->_pattern[60]		= "314111";
			$this->_pattern[61]		= "221411";
			$this->_pattern[62]		= "431111";
			$this->_pattern[63]		= "111224";
			$this->_pattern[64]		= "111422";
			$this->_pattern[65]		= "121124";
			$this->_pattern[66]		= "121421";
			$this->_pattern[67]		= "141122";
			$this->_pattern[68]		= "141221";
			$this->_pattern[69]		= "112214";
			$this->_pattern[70]		= "112412";
			$this->_pattern[71]		= "122114";
			$this->_pattern[72]		= "122411";
			$this->_pattern[73]		= "142112";
			$this->_pattern[74]		= "142211";
			$this->_pattern[75]		= "241211";
			$this->_pattern[76]		= "221114";
			$this->_pattern[77]		= "413111";
			$this->_pattern[78]		= "241112";
			$this->_pattern[79]		= "134111";
			$this->_pattern[80]		= "111242";
			$this->_pattern[81]		= "121142";
			$this->_pattern[82]		= "121241";
			$this->_pattern[83]		= "114212";
			$this->_pattern[84]		= "124112";
			$this->_pattern[85]		= "124211";
			$this->_pattern[86]		= "411212";
			$this->_pattern[87]		= "421112";
			$this->_pattern[88]		= "421211";
			$this->_pattern[89]		= "212141";
			$this->_pattern[90]		= "214121";
			$this->_pattern[91]		= "412121";
			$this->_pattern[92]		= "111143";
			$this->_pattern[93]		= "111341";
			$this->_pattern[94]		= "131141";
			$this->_pattern[95]		= "114113";
			$this->_pattern[96]		= "114311";
			$this->_pattern[97]		= "411113";
			$this->_pattern[98]		= "411311";
			$this->_pattern[99]		= "113141";
			$this->_pattern[100]		= "114131";
			$this->_pattern[101]		= "311141";
			$this->_pattern[102]		= "411131";
			$this->_pattern[103]		= "211412";
			$this->_pattern[104]		= "211214";
			$this->_pattern[105]		= "211232";
			$this->_pattern[106]		= "2331112";
	
        $this->_CODEA = array(
                        " ","!",'"',"#","$","%","&","'","(",")"
                        ,"*","+",",","-",".","/",
                        "0","1","2","3","4","5","6","7","8","9",
                        ":",";","<","=",">","?","@",
                        "A","B","C","D","E","F","G","H","I","J",
                        "K","L","M","N","O","P","Q","R","S","T",
                        "U","V","W","X","Y","Z",
                        "[","\\","]","^","_",
                        chr(0x00), //NUL
                        chr(0x01), //SOH
                        chr(0x02), //STX
                        chr(0x03), //ETX
                        chr(0x04), //EOT
                        chr(0x05), //ENQ
                        chr(0x06), //ACK
                        chr(0x07), //BEL
                        chr(0x08), //BS
                        chr(0x09), //HT
                        chr(0x0A), //LF
                        chr(0x0B), //VT
                        chr(0x0C), //FF
                        chr(0x0D), //CR
                        chr(0x0E), //SO
                        chr(0x0F), //SI
                        chr(0x10), //DLE
                        chr(0x11), //DC1
                        chr(0x12), //DC2
                        chr(0x13), //DC3
                        chr(0x14), //DC4
                        chr(0x15), //NAK
                        chr(0x16), //SYN
                        chr(0x17), //ETB
                        chr(0x18), //CAN
                        chr(0x19), //EM
                        chr(0x1A), //SUB
                        chr(0x1B), //ESC
                        chr(0x1C), //FS
                        chr(0x1D), //GS
                        chr(0x1E), //RS
                        chr(0x1F), //US
                        );
        
        $this->_CODEB = array(
                        " ","!",'"',"#","$","%","&","'","(",")", //0-9 
                        "*","+",",","-",".","/", // 10-15
                        "0","1","2","3","4","5","6","7","8","9",//16-25
                        ":",";","<","=",">","?","@",//26-32
                        "A","B","C","D","E","F","G","H","I","J",
                        "K","L","M","N","O","P","Q","R","S","T",
                        "U","V","W","X","Y","Z",
                        "[","\\","]","^","_",
                        "`",
                        "a","b","c","d","e","f","g","h","i","j",
                        "k","l","m","n","o","p","q","r","s","t",
                        "u","v","w","x","y","z",
                        "{","|","}","~",
                        chr(0x7f),
                        );
        for ($i = 0; $i <= 9; $i++)
            for ($j = 0 ; $j <= 9 ; $j++)
        $this->_CODEC[] = $i.$j;
    
        $this->_specialcodes = array ("FNC1" => array("_CODEA" => 102,
                                                      "_CODEB" => 102,
                                                      "_CODEC" => 102),
                                      "FNC2" => array("_CODEA" => 97,
                                                      "_CODEB" => 97),
                                      "FNC3" => array("_CODEA" => 96,
                                                      "_CODEB" => 96),
                                      "FNC4" => array("_CODEA" => 101,
                                                      "_CODEB" => 100),
                                      "_CODEA" => array("_CODEB" => 101,
                                                        "_CODEC" => 101),
                                      "_CODEB" => array("_CODEA" => 100,
                                                        "_CODEC" => 100),
                                      "_CODEC" => array("_CODEA" => 99,
                                                        "_CODEB" => 99),
                                      "SHIFT" => array("_CODEA" => 98,
                                                        "_CODEB" => 98),
                                      );
                                      
        $this->_startcodes = array("_CODEA" => 103,
                                   "_CODEB" => 104,
                                   "_CODEC" => 105,
                                   "STOP" => 106,
                                   );
    }
    // }}}
    
    // {{{ function output($data)
    function output($data) {
        
        $this->error = false;
        
        //if (empty($data)) {
        if (!isset($data) || $data=="") {
                trigger_error(
                    sprintf(
                        _("No data to process"),
                        $data[$i]),$this->error_level);
                $this->error = _("No data to process");
        return FALSE;
        }
        
        if (is_array($data))
            $this->parse($data);
        else
            $this->process($data);

        $this->_barcode[] = $this->_checksum %103;
        $this->_barcode[] = $this->_startcodes['STOP'];
        $this->checksum = $this->_checksum %103;

        $string = '';
        foreach ($this->_barcode as $code) {
            $string .= $this->_pattern[$code];
            }
        $data = preg_split('##',$string);
        
        
        $output_string = '';

        // !removing first and last unwanted chunks
        $limit = count($data) - 1;
        for ($i = 0 ; $i < $limit ; $i++) {
            $bar = ($i %2) ? 1 : 0;
            for ($j = 0 ; $j < $data[$i] ; $j++)
                $output_string .= $bar;
            }

        if (!$this->nozerofill)
        // add quiet zone (10 x-dimensions)
            $output_string = '0000000000' . $output_string . '0000000000';
        $this->output_string = $output_string;
        $this->_checksum = $this->_count = 0;
        $this->_barcode = array();
        $this->_current_code = '_CODEB';
        $this->_actual_code = '_CODEB';
    return($output_string);
    }
    // }}}
    
    //
    // PRIVATE FUNCTIONS
    //
   
    // {{{ private functions
    
    // {{{ function parse($datas)
    function parse($datas) {
        
        foreach($datas as $data) {
            switch($data) {
                case 'FNC1':
                case 'FNC2':
                case 'FNC3':
                case 'FNC4':
                case 'SHIFT':
                    $this->specialchar($data);
                    break;
                default:
                    $this->process($data);
                    break;
                }
            }
    }
    // }}}
    
    // {{{ function process($data)
    function process($data) {
        // split datas in an array
        $data = preg_split('##',$data);
        array_pop($data);
        array_shift($data);
        $limit = count($data);
        // process datas chunks
        for ($i = 0 ; $i < $limit ; $i++) {
            if (ord($data[$i]) > 127) {
                trigger_error(
                    sprintf(
                        _("%s: only ASCII code is allowed (no accents)"),
                        $data[$i]),$this->error_level);
                $this->error = _("non ASCII character");
                continue;
                }
            
            $this->_actual_code = $this->_current_code;
            if ((($i + 3 < $limit) && preg_match('#[0-9]{4}#',$data[$i].$data[$i+1].$data[$i+2].$data[$i+3])) or (($this->unoptimized) && ($i + 1 < $limit) && preg_match('#[0-9]{2}#',$data[$i].$data[$i+1])))  {
                $value = $data[$i].$data[$i+1];
                $i++;
                if (!$this->_current_code)
                    $this->start('_CODEC');
                else {    
                    if ($this->_current_code !== '_CODEC')
                        $this->specialchar('_CODEC');
                    $this->_actual_code = $this->_current_code = '_CODEC';
                    }
 
            } else {
                if ($this->_current_code == '_CODEC') {
                    $this->specialchar('_CODEB');
                    $this->_actual_code = $this->_current_code = '_CODEB';
                    }
                $value = $data[$i];
                }     
            
            $actual_code = $this->_actual_code ? $this->_actual_code : '_CODEB';
            while (false === ($code = array_search($value,$this->$actual_code)))
                switch ($this->_actual_code) {
                    case '_CODEB':
                 //       if (in_array($data[$i+1],$this->$actual_code)) {
                 if (isset($data[$i+1]) && in_array($data[$i+1],$this->$actual_code)) {
                            $this->specialchar('SHIFT');
                            $actual_code = $this->_actual_code = '_CODEA';
                        } else {
                            if (!array_key_exists(0,$this->_barcode))
                                $this->start('_CODEA');
                            else    
                                $this->specialchar('_CODEA');
                                $actual_code = $this->_actual_code = $this->_current_code = '_CODEA';
                            }
                        break;
                    case '_CODEA':
                       // if (in_array($data[$i+1],$this->$actual_code)) {
                       if (isset($data[$i+1]) && in_array($data[$i+1],$this->$actual_code)) {
                            $this->specialchar('SHIFT');
                            $actual_code = $this->_actual_code = '_CODEB';
                        } else {
                            $this->specialchar('_CODEB');
                            $actual_code = $this->_current_code = $this->_actual_code = '_CODEB';
                            }
                        break;
                    case '_CODEC': // Will never append, but...
                        $this->specialchar('_CODEB');
                        $actual_code = $this->_current_code = $this->_actual_code = '_CODEB';
                        break;
                    }
                // put a start char if never done
                if (!array_key_exists(0,$this->_barcode))
                    $this->start('_CODEB');
                $this->char($value);
                }
    }
    // }}}

    // {{{ function start($code)
    function start($code) {
        $this->_barcode[] = $this->_startcodes[$code];
        $this->_current_code = $this->_actual_code = $code;
        $this->add_checksum($this->_startcodes[$code]);
    }
    // }}}

    // {{{ function specialchar($specialchar)
    function specialchar($specialchar) {
        if (!array_key_exists(0,$this->_barcode)) {
            $this->start('_CODEB');
            }
        $code = $this->_specialcodes[$specialchar][$this->_current_code];
        if ($code === false) {
            $this->_barcode[] = $code = $this->_specialcodes['_CODEB'][$this->_current_code];
            $this->add_checksum($code);
            $this->_barcode[] = $code = $this->_specialcodes[$specialchar]['_CODEB'];
            $this->add_checksum($code);
        } else {
            $this->_barcode[] = $this->_specialcodes[$specialchar][$this->_current_code];
            $this->add_checksum($code);
            }
            
    }
    // }}}

    // {{{ function char($code)
    function char($code) {
        $actual_code = $this->_actual_code;
        $this->_barcode[] = $code = array_search($code,$this->$actual_code);
        $this->add_checksum($code);
    }
    // }}}
    
    // {{{ function add_checksum($code)
    function add_checksum($code) {
        if ($this->_count === 0)
            $this->_checksum = $code;
        else
            $this->_checksum += $this->_count * $code;
        
        $this->_count ++;
    }
    // }}}
// }}}

}; // end of class
?>
