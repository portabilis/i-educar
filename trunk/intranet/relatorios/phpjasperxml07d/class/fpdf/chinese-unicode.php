<?php
require_once 'chinese.php';

define ('FPDF_UNICODE_ENCODING', 'UCS-2BE');

class PDF_Unicode extends PDF_Chinese 
{
  var $charset;     // input charset. User must add proper fonts by add font functions like AddUniCNShwFont
  var $isUnicode;   // whether charset belongs to Unicode

  function PDF_Unicode ($layout="P",$uom="mm",$papersize="A4",$charset = 'UTF-8')
  {
    $this->FPDF ($layout, $uom, $papersize);
	//$this->FPDF ('L', 'mm', 'A4');
    $this->charset = strtoupper(str_replace ('-', '', $charset));
    $this->isUnicode = in_array ($this->charset, array ('UTF8', 'UTF16', 'UCS2'));
  }

  function AddUniCNShwFont ($family='Uni', $name='PMingLiU')  // name for Kai font is DFKai-SB
  {
    //Add Unicode font with half-witdh Latin, character code must be utf16be
    for($i=32;$i<=126;$i++)
      $cw[chr($i)]=500;
    $CMap='UniCNS-UCS2-H';  // for compatible with PDF 1.3 (Adobe-CNS1-0), 1.4 (Adobe-CNS1-3), 1.5 (Adobe-CNS1-3)
    //$CMap='UniCNS-UTF16-H';  // for compatible with 1.5 (Adobe-CNS1-4)
    $registry=array('ordering'=>'CNS1','supplement'=>0);
    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
  }

  function AddUniCNSFont ($family='Uni', $name='PMingLiU')  
  {
    //Add Unicode font with propotional Latin, character code must be utf16be
    $cw=$GLOBALS['Big5_widths'];
    $CMap='UniCNS-UCS2-H';
    $registry=array('ordering'=>'CNS1','supplement'=>0);
    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
  }

  function AddUniGBhwFont ($family='uGB', $name='AdobeSongStd-Light')  
  {
    //Add Unicode font with half-witdh Latin, character code must be utf16be
    for($i=32;$i<=126;$i++)
      $cw[chr($i)]=500;
    $CMap='UniGB-UCS2-H';  
    $registry=array('ordering'=>'GB1','supplement'=>4);
    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
  }

  function AddUniGBFont ($family='uGB', $name='AdobeSongStd-Light')  
  {
    //Add Unicode font with propotional Latin, character code must be utf16be
    $cw=$GLOBALS['GB_widths'];
    $CMap='UniGB-UCS2-H';
    $registry=array('ordering'=>'GB1','supplement'=>4);
    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
  }

  // redefinition of FPDF functions

  function GetStringWidth ($s)
  {
    //Get width of a string in the current font
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($s, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $w = $this->GetUniStringWidth ($txt);
      mb_internal_encoding ($oEnc);
      return $w;
    } else
      return parent::GetStringWidth($s);
  }

  function Text ($x, $y, $txt)
  {
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $this->UniText ($x, $y, $txt);
      mb_internal_encoding ($oEnc);
    } else 
      parent::Text ($x, $y, $txt);
  }

  function Cell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  {
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $this->UniCell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
      mb_internal_encoding ($oEnc);
    } else 
      parent::Cell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
  }

  function MultiCell ($w,$h,$txt,$border=0,$align='J',$fill=0)
  {
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $this->UniMultiCell ($w, $h, $txt, $border, $align, $fill);
      mb_internal_encoding ($oEnc);
    } else {
      parent::MultiCell ($w, $h, $txt, $border, $align, $fill);
    }
  }

  function Write ($h,$txt,$link='')
  {
    if ($this->isUnicode) {
      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
      $oEnc = mb_internal_encoding();
      mb_internal_encoding (FPDF_UNICODE_ENCODING);
      $this->UniWrite ($h, $txt, $link);
      mb_internal_encoding ($oEnc);
    } else {
      parent::Write ($h, $txt, $link);
    }
  }

  // implementation in Unicode version 

  function GetUniStringWidth ($s)
  {
    //Unicode version of GetStringWidth()
    $l=0;
    $cw=&$this->CurrentFont['cw'];
    $nb=mb_strlen($s);
    $i=0;
    while($i<$nb) {
      $c=mb_substr($s,$i,1);
      $ord = hexdec(bin2hex($c));
      if($ord<128) {
	$l+=$cw[chr($ord)];
      } else {
	$l+=1000;
      }
      $i++;
    }
    return $l*$this->FontSize/1000;
  }

  function UniText ($x, $y, $txt)
  {
    // copied from parent::Text but just modify the line below
    $s=sprintf('BT %.2f %.2f Td <%s> Tj ET',$x*$this->k,($this->h-$y)*$this->k, bin2hex($txt));

    if($this->underline && $txt!='')
      $s.=' '.$this->_dounderline($x,$y,$txt);
    if($this->ColorFlag)
      $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
  }

  function UniCell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  {
    // copied from parent::Text but just modify the line with an output "BT %.2f %.2f Td <%s> Tj ET" ...
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
      {
	//Automatic page break
	$x=$this->x;
	$ws=$this->ws;
	if($ws>0)
	  {
	    $this->ws=0;
	    $this->_out('0 Tw');
	  }
	$this->AddPage($this->CurOrientation);
	$this->x=$x;
	if($ws>0)
	  {
	    $this->ws=$ws;
	    $this->_out(sprintf('%.3f Tw',$ws*$k));
	  }
      }
    if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
    $s='';
    if($fill==1 || $border==1)
      {
	if($fill==1)
	  $op=($border==1) ? 'B' : 'f';
	else
	  $op='S';
	$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
      }
    if(is_string($border))
      {
	$x=$this->x;
	$y=$this->y;
	if(strpos($border,'L')!==false)
	  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
	if(strpos($border,'T')!==false)
	  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
	if(strpos($border,'R')!==false)
	  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	if(strpos($border,'B')!==false)
	  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
      }
    if($txt!=='')
      {
	if($align=='R')
	  $dx=$w-$this->cMargin-$this->GetUniStringWidth($txt);
	elseif($align=='C')
	  $dx=($w-$this->GetUniStringWidth($txt))/2;
	else
	  $dx=$this->cMargin;
	if($this->ColorFlag)
	  $s.='q '.$this->TextColor.' ';
	$s.=sprintf('BT %.2f %.2f Td <%s> Tj ET',($this->x+$dx)*$k,
		    ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,bin2hex($txt));
	if($this->underline)
	  $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
	if($this->ColorFlag)
	  $s.=' Q';
	if($link)
	  $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetUniStringWidth($txt),$this->FontSize,$link);
      }
    if($s)
      $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
      {
	//Go to next line
	$this->y+=$h;
	if($ln==1)
	  $this->x=$this->lMargin;
      }
    else
      $this->x+=$w;
  }

  function UniMultiCell($w,$h,$txt,$border=0,$align='L',$fill=0)
  {
    //Unicode version of MultiCell()

    $enc = mb_internal_encoding();

    $cw=&$this->CurrentFont['cw'];
    if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = $txt;
    $nb=mb_strlen($s);
    if ($nb>0 && mb_substr($s,-1)==mb_convert_encoding("\n", $enc, $this->charset))
      $nb--;
    $b=0;
    if($border)
      {
	if($border==1)
	  {
	    $border='LTRB';
	    $b='LRT';
	    $b2='LR';
	  }
	else
	  {
	    $b2='';
	    if(is_int(strpos($border,'L')))
	      $b2.='L';
	    if(is_int(strpos($border,'R')))
	      $b2.='R';
	    $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
	  }
      }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
      {
	//Get next character
	$c=mb_substr($s,$i,1);
	$ord = hexdec(bin2hex($c));
	$ascii = ($ord < 128);
	if($c==mb_convert_encoding("\n", $enc, $this->charset))
	  {
	    //Explicit line break
	    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
	    $i++;
	    $sep=-1;
	    $j=$i;
	    $l=0;
	    $nl++;
	    if($border && $nl==2)
	      $b=$b2;
	    continue;
	  }
	if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
	  {
	    $sep=$i;
	    $ls=$l;
	  }
	$l+=$ascii ? $cw[chr($ord)] : 1000;
	if($l>$wmax)
	  {
	    //Automatic line break
	    if($sep==-1 || $i==$j)
	      {
		if($i==$j)
		  $i++; //=$ascii ? 1 : 2;
		$this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
	      }
	    else
	      {
		$this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),$b,2,$align,$fill);
		$i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
	      }
	    $sep=-1;
	    $j=$i;
	    $l=0;
	    $nl++;
	    if($border && $nl==2)
	      $b=$b2;
	  }
	else
	  $i++; //=$ascii ? 1 : 2;
      }
    //Last chunk
    if($border && is_int(strpos($border,'B')))
      $b.='B';
    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
    $this->x=$this->lMargin;
  }

  function UniWrite($h,$txt,$link='')
  {
    //Unicode version of Write()
    $enc = mb_internal_encoding();
    $cw=&$this->CurrentFont['cw'];
    $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = $txt;

    $nb=mb_strlen($s);
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
      {
	//Get next character
	$c=mb_substr($s,$i,1);
	//Check if ASCII or MB
	$ord = hexdec(bin2hex($c));
	$ascii=($ord < 128);
	if($c==mb_convert_encoding("\n", $enc, $this->charset))
	  {
	    //Explicit line break
	    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
	    $i++;
	    $sep=-1;
	    $j=$i;
	    $l=0;
	    if($nl==1)
	      {
		$this->x=$this->lMargin;
		$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	      }
	    $nl++;
	    continue;
	  }
	if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
	  $sep=$i;
	$l+=$ascii ? $cw[chr($ord)] : 1000;
	if($l>$wmax)
	  {
	    //Automatic line break
	    if($sep==-1 || $i==$j)
	      {
		if($this->x>$this->lMargin)
		  {
		    //Move to next line
		    $this->x=$this->lMargin;
		    $this->y+=$h;
		    $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $i++;
		    $nl++;
		    continue;
		  }
		if($i==$j)
		  $i++; //=$ascii ? 1 : 2;
		$this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
	      }
	    else
	      {
		$this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),0,2,'',0,$link);
		$i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
	      }
	    $sep=-1;
	    $j=$i;
	    $l=0;
	    if($nl==1)
	      {
		$this->x=$this->lMargin;
		$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	      }
	    $nl++;
	  }
	else
	  $i++; //=$ascii ? 1 : 2;
      }
    //Last chunk
    if($i!=$j)
      $this->UniCell($l/1000*$this->FontSize,$h,mb_substr($s,$j,$i-$j),0,0,'',0,$link);
  }

}
?>
