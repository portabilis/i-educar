<?php

class ExportXLS{
    public $wb;
    public $ws;
    public $arrayband;
    public $arraypageHeader;
    public $arraypageFooter;
    public $arraydetail;
    public $arraybackground;
    public $arraytitle;
    public $arraysummary;
    public $arraygroup;
    public $relativex=0;
    public $relativey=0;
    public $lastrow=0;
    public $pageHeight;
    public $pageWidth;
    public $cols=array();
    public $rows=array();
    public $vunitmultiply=0.15;
    public $hunitmultiply=0.15;
    public $headerbandheight;
    public $arraysqltable;
    public $global_pointer;
    public $detailrowcount;
    public $groupnochange=0;
    public $headerrowcount;
    public $arrayVariable;
    public $arrayParameter;
    public $arraygroupfoot;
    public $report_count=0;
    public  $offsetposition=0;
    public $arraygrouphead;
    public $rowswithdata=array();
    public $uselib=0;
    public $debughtml=false;
    public $forcenolib=false;
    private $currentband="";
    public function ExportXLS($raw,$filename, $type='Excel5',$out_method='I'){
    $type='Excel5';
                 if(extension_loaded( "excel" ) && !$this->forcenolib){
					 $this->uselib=1;
  		              $this->wb 	= Excel::create(1, 'UTF-8');
			          $this->ws	= $this->wb->getWorkSheet(0);
			          $this->wformat= new ExcelCellFormat($this->wb);
                                  $this->blankformat=$this->wformat;
			          $this->wfont = new ExcelFont(ExcelFont::WEIGHT_NORMAL); 
                                 $this->blankwfont= $this->wfont  ;
			          
                 }
                 else{
                         include dirname(__FILE__)."/PHPExcel.php";
        	             $this->wb  = new PHPExcel();
                         $this->ws=$this->wb->getActiveSheet(0);
                  
                 }

           
           
                $this->arrayband=$raw->arrayband;
                $this->arraypageHeader=$raw->arraypageHeader;
                $this->arraypageFooter=$raw->arraypageFooter;
                $this->arraydetail=$raw->arraydetail;
                $this->arraybackground=$raw->arraybackground;
                $this->arraytitle=$raw->arraytitle;
                $this->arraysummary=$raw->arraysummary;
                $this->arraygroup=$raw->arraygroup;
                $this->arraylastPageFooter=$raw->arraylastPageFooter;
                //$this->arraypageFooter=$raw->arraypageFooter;
                
                $this->headerbandheight=$raw->headerbandheight;
                $this->arraysqltable=$raw->arraysqltable; 
                $this->pageWidth=$raw->arrayPageSetting['pageWidth']; 
                $this->pageHeight=$raw->pageHeight; 
                $this->arrayVariable=$raw->arrayVariable;
                $this->arrayParameter=$raw->arrayParameter;
                $this->arrayfield=$raw->arrayfield;
                $this->grouplist=$raw->grouplist;
                $this->arraygroupfoot=$raw->arraygroupfoot;
                $this->arraygrouphead=$raw->arraygrouphead;
                $this->totalgroup=$raw->totalgroup;
                $this->summaryexit=false;
  
    
        $this->global_pointer=0;

          $this->arrangeColumn();
          $printeddetail=false;
          $printsummary=false;


        foreach ($raw->arrayband as $band) {
          
          
            if($band["name"]== "title"){
                  if($raw->arraytitle[0]["height"]>0){
                            $this->title();
					$this->maxrow--;

                  }
            }
                 elseif($band["name"]== "pageHeader"){
                    
                  if($raw->arraypageHeader[0]["height"]>0){
                        $this->pageHeader();
                  $this->maxrow--;
                  }
                 }
                 elseif($band["name"]== "detail"){
                
                     
                     if($raw->arraydetail[0][0]["height"]>0 && $printeddetail==false){
                        
                        $this->detail();
                        $printeddetail=true;
           
                     }
                                      
                 }
                 elseif($band["name"]== "pageFooter"){
                
                     if($raw->arraylastPageFooter[0]["height"]==0 && $raw->arraypageFooter[0]["height"]>0){
                        $this->pageFooter();
                       $this->maxrow--;
                     }
                 }
                 elseif(($band["name"]== "lastPageFooter" || $band["name"]== "summary" ) && $printsummary==false){
           
                     if($raw->arraysummary[0]["height"]>0){
                        $this->summary();                  
                        $this->maxrow--;
                     }
                     if($raw->arraylastPageFooter[0]["height"]>0){
                        $this->lastPageFooter();                  
                        $this->maxrow--;
                     }
                     $printsummary=true;

                 }
                elseif($band["name"]== "group"){
                  }

        }

         if($this->debughtml==true)
         die;
//         $this->deleteEmptyRow();
         //die;
          // $this->ws->removeRow(2,1);
         
 $filename=trim($filename);


         if($filename==''){
             if($type=='XLS' || $type=='xls'){
                    $filename="report.xls";
                    $contenttype="application/application/vnd.ms-excel";
                    }
             elseif($type=='XLST'|| $type=='XLSX' || $type=='xlsx'){
                $filename="report.xlsx";
                $contenttype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                }
         
         }
       if($out_method=='F' || $out_method=='f'){
          
             $this->savexls($filename,$type,$out_method);   
//        $objWriter = PHPExcel_IOFactory::createWriter($this->wb, $type);
  //         $objWriter->save($filename);
          $raw->generatestatus=true;
       
       }
       else{
		$filename=str_replace(".xlsx",".xls",$filename);

          set_time_limit(0);
         header('HTTP/1.0 200 OK', true, 200);
        header('Content-Type:'.$contenttype);
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        
        header('Cache-Control: max-age=0');
                 $this->savexls($filename,$type,'d');
                 
                         
    //    $objWriter = PHPExcel_IOFactory::createWriter($this->wb, $type);
        
        
        //if(PHP_OS=='WINNT')
      //   $objWriter->save('php://output');

        
//ob_end_clean();
        
       }
          // die;
       
    }

    
    public function arrangeColumn(){
        
        $cols=array();
        $cx=0;
        foreach($this->arraypageHeader as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }
            $i=0;
       foreach($this->arraydetail as $detailband){
          
        foreach($detailband as $out){
            
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
                $cx=intval($out['x']);

            }
            
           if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
                //echo $out['width']." + $cx <hr>";
            }
            // echo $i.".".$out['type']."=",$out['x'].$out['txt'].":";     print_r($cols);echo "<hr>";
             $i++;
        }
       
       }
       
       /*                $this->grouplist[$this->totalgroup]=array(
                        "name"=>$xml_path["name"]."",
                        "isnewpage"=>$newPageGroup,
                        "groupheadheight"=>$groupheadheight,
                        "groupfootheight"=> $groupfootheight,
                        "headercontent"=>$headercontent,
                        "footercontent"=>$footercontent
             
            );
*/

    foreach($this->grouplist[0]['headercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
        }
        
        
       foreach($this->grouplist[0]['footercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }
        
    foreach($this->grouplist[1]['headercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
        }
        
        
       foreach($this->grouplist[1]['footercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }
        
   
        foreach($this->grouplist[2]['headercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
        }
        
        
       foreach($this->grouplist[2]['footercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }
        
     foreach($this->grouplist[3]['headercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
        }
        
        
       foreach($this->grouplist[3]['footercontent'] as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }
        
   
    
            foreach($this->arraypageFooter as $out){
          
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
          
            }
            
        }
    
            foreach($this->arraylastPageFooter as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);
            
            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }
            
            //print_r($cols);echo "<hr>";
        }

        foreach($this->arraysummary as $out){
          //  print_r($out);echo "<hr>";
            if($out['type']=="SetXY"){
                $cols[]=intval($out['x']);
            $cx=intval($out['x']);

            }
            if($out['type']=="Cell" ||$out['type']=="MultiCell"){
                $cols[]=intval($out['width'] + $cx);
               // echo $out['width']." + $cx <hr>";
            }

            //print_r($cols);echo "<hr>";
        }
        
//                print_r($cols);echo "<hr>";
        $cols=array_unique($cols);
             sort($cols);



             $i=0;
             
             foreach($cols as $index => $xposition){
                $nextxposition=$cols[($i+1)];
                 if($nextxposition=="")
                    $nextxposition=$this->pageWidth;
              //  echo " $index ($nextxposition-$xposition)";echo "<hr>";

  	            $this->setColumnWidth($index,($nextxposition-$xposition));
//                 $this->ws->getColumnDimensionByColumn($index)->setWidth($this->hunitmultiply*($nextxposition-$xposition));
                 $this->cols=array_merge($this->cols, array("c".$xposition=>$i));
                 $i++;
             }
             
             
        
    }
    
//      
    public function arrangeRows($myband,$debug=false,$changeheight=true){
        $this->rows=array();
        $beginrow=$this->maxrow;
        $rows=array(); //(y, height, type, rowno)
        $pos=array();
        $emptyrowheight=0.01;
        foreach($myband as $out){
            if($out['type']=="Cell" || $out['type']=="MultiCell"){
            $y=$out['y']+0;
            $height=$out['height']+0;
            
         //   if($y%2 >0)
          //      $y--;
//            
           // if($height%2 >0)
             //   $height--;
                
                $pos[]=$y;
                $pos[]=$y+$height;
                $rows[]=array($y,$height,"field");
            
              }
              
              
        }
                        $pos[]=0;
                        $pos[]=$myband[0]['height']+0;
                          sort($pos);
                       $pos=array_unique($pos);
                         
                        $rows=array_unique($rows,SORT_REGULAR);
                             array_multisort($rows);
                             $rows[]=array(0,$myband[0]['height']+0,"band");
                             
               if($debug==true){
                   echo "row:";print_r($rows);echo "<hr>";
                   echo "pos:"; print_r($pos);echo "<hr>";
             //      die;
                }
             
             $i=0;
             foreach($pos as $index => $content){
               $this->rows=array_merge($this->rows, array("r".$content=>($i+1)));                   
               $i++;
             }
             
             $this->lastrow=$i+$beginrow;
             
//             if($changeheight)
//                 for($l=$beginrow; $l <= $this->lastrow; $l++)
//                    if($beginrow>=1)
//                       $this->ws->getRowDimension($l)->setRowHeight(1);
//
//                  $lastrow=0;    
                foreach($rows as $r =>$rowcontent){
                 $tmpy=$rowcontent[0];  
                    $rowposincurrentband=$this->rows['r'.$tmpy];
                    $this->rowswithdata[]=$rowposincurrentband+$beginrow;
                }

//                    
//                     //   $rowheight=$this->vunitmultiply*$rowcontent[1]*10;
//                       // if($changeheight)
//                         //$this->ws->getRowDimension($this->rows['r'.$rowcontent[0]] + $beginrow)->setRowHeight(-1);                
//                   
//                           
//               }
//               
               
             
             if($debug==true)die;
             
             
           //   $this->ws->getRowDimension(1)->setRowHeight(30);
             return ($i-1);
        
    }
    
    
    public function title(){
    $this->currentband="title";
       $this->titlerowcount=$this->arrangeRows($this->arraytitle,true);
$i=0;
foreach($this->arraytitle as $out){
   
            $this->display($out,$this->maxrow);
     $i++;
            }
                $this->maxrow+=$this->titlerowcount;

        
    }
    
    public function pageHeader(){
    $this->currentband="pageHeader";
       $this->headerrowcount= $this->arrangeRows($this->arraypageHeader,false,true);
       
      $this->maxrow=$this->headerrowcount;
        foreach($this->arraypageHeader as $out){
            $this->display($out,0);
            
        }
        
        //   $this->lastrow--;
      // echo "header:".$this->lastrow;echo "<hr>";
    }
    
    public function detail(){
                                $this->group_count[$this->grouplist[0]["name"]]=0;
                                $this->group_count[$this->grouplist[1]["name"]]=0;
                                $this->group_count[$this->grouplist[2]["name"]]=0;
                                $this->group_count[$this->grouplist[3]["name"]]=0;
        $i=0;
         $this->groupnochange=0;
        $this->showGroupHeader(false);
        $isgroupfooterprinted=false;
        
        foreach($this->arraysqltable as $row){
            $this->report_count++;
           
            
            if($this->checkSwitchGroup("header"))	{
                                 //   echo '<New group header>';
                                 $this->showGroupHeader(true);
                                }
                                $this->group_count[$this->grouplist[0]["name"]]++;
                                $this->group_count[$this->grouplist[1]["name"]]++;
                                $this->group_count[$this->grouplist[2]["name"]]++;
                                $this->group_count[$this->grouplist[3]["name"]]++;
                                
                 if(isset($this->arrayVariable))	
                                $this->variable_calculation($i);
                $this->currentband='detail';
$d=0;
$r=0;
      foreach($this->arraydetail as $detail){
          $detailheight= $this->arrangeRows($detail);
               
          		

        foreach($detail as $out){
    $this->currentband="detail";                    
          //($this->headerrowcount+($this->detailrowcount*$i)
            
         
               $this->display($out,$this->maxrow);
           
            $d++;
                
        }
        $d=0;
        $r++;
        
          $this->maxrow+= $detailheight;
         
      }
     
	
        $this->global_pointer++;
           $i++;
       }
        
       $this->showGroupFooter();
               $this->maxrow+=$this->groupfootrowcount+2;
             

    }
    
     public function showGroupHeader($printgroupfooter=false) {
        $this->currentband='groupHeader';

        
        if($printgroupfooter==true)
            $this->showGroupFooter();
        else
            $this->groupnochange=-1;
            
        
            for($groupno=$this->groupnochange+1; $groupno  <$this->totalgroup;$groupno++){
            $groupname=$this->grouplist[$groupno]["name"];
      
            foreach($this->arrayVariable as $v=>$a){
                
                if($a["resetGroup"]!=""&& $a["resetGroup"]==$groupname){
                 $this->arrayVariable[$v]["ans"]=0;
                }
            }
            
              $headercontent=$this->grouplist[$groupno]["headercontent"];
         $rr=$this->analyse_expression($headercontent[0]["printWhenExpression"]);
            //echo "Header:".print_r($headercontent[0],true)."<br/><br/>";
         if($headercontent[0]["printWhenExpression"]!=""){
                if(!$rr){
                    $yplusbandheight-=$y;
                    continue;
                }
         }
         
                $j=0;
                $currentheaderheight=$this->arrangeRows($headercontent,false,true);
                foreach ($headercontent as $out){
                         $this->display($out,$this->maxrow);
                    $j++;
                }
                
                 $this->maxrow+=$currentheaderheight;
            }
            
           // die;
            
            
              if($printgroupfooter==false)
         $this->report_count=0;
      else
          $this->report_count++;
          
     $this->maxrow--;  
    }
    public function showGroupFooter() {
        
        $this->report_count--;
        $this->offsetposition=-1;
        $this->currentband='groupFooter';
        
       for($groupno=$this->totalgroup;$groupno  >$this->groupnochange;$groupno--){
      $footercontent=$this->grouplist[$groupno]["footercontent"];
      
      $rr=$this->analyse_expression($footercontent[0]["printWhenExpression"]);
         if($footercontent[0]["printWhenExpression"]!=""){
                if(!$rr){
                    $yplusbandheight-=$y;
                    continue;
                }
         }
         
      $curfooterheight=$this->arrangeRows($footercontent,false,true);
      foreach ($footercontent as $out) {
            $this->display($out,$this->maxrow);
        }
         $this->maxrow+=$curfooterheight;
     }
      $this->offsetposition=0;
      for($i=$this->groupnochange+1;$i<$this->totalgroup; $i++){
                             $this->group_count[$this->grouplist[$i]["name"]]=1;
                        }
        $this->currentband='';
        $this->maxrow--;

    }

    
    
    
    public function pageFooter(){
    $this->currentband="pageFooter";
        $this->footerrowcount=$this->arrangeRows($this->arraypageFooter);
        foreach($this->arraypageFooter as $out){
            $this->display($out,$this->maxrow);
        }
        $this->maxrow+=$this->footerrowcount;
    }
    
    public function lastPageFooter(){
//print_r($this->arraylastPageFooter);echo "<hr>lastpage footer";


       $this->lastfooterrowcount=$this->arrangeRows($this->arraylastPageFooter,false);

       $i=0;
foreach($this->arraylastPageFooter as $out){
    
            $this->display($out,$this->maxrow);
     $i++;
            }
            //echo "complete last page footer";
                $this->maxrow+=$this->lastfooterrowcount;

    }
    public function summary(){
    $this->currentband="summary";
        $this->summaryrowcount=$this->arrangeRows($this->arraysummary);
        foreach($this->arraysummary as $out){
            $this->display($out,$this->maxrow);
        }
       $this->maxrow+=$this->summaryrowcount;
       $this->summaryexit=true;
    }
    
    public function display($arraydata,$rowpos){
     
    
        if($this->relativex=='')
            $this->relativex=0;
            
      if($this->debughtml)
        echo $arraydata['type']." :";

        switch($arraydata['type']){
            case "MultiCell":       
                if($this->relativey=="")
                    $this->relativey=0;
                    $this->mergeCells(    $this->relativex,  ($this->relativey+$rowpos),   ($this->cols['c'.($this->mergex+$arraydata['width'])]-1),   ($this->relativey+$rowpos)  );
    
               $txt=$this->analyse_expression($arraydata['txt']);
               //if($arraydata['pattern']!='')
               //   $txt= $this->formatText ($txt, $arraydata['pattern']);
  	  			if($this->debughtml)
  	  			   echo  $txt.",align:".$arraydata['align']."<br/>";

                  $this->setText($this->relativex,($this->relativey+$rowpos),  $txt,$arraydata['align'], $arraydata['pattern']);                
                break;
            case "Cell":
  

            $this->SetText($this->relativex, ($this->relativey+$rowpos),$this->analyse_expression($arraydata['txt']),$arraydata['align'], $arraydata['pattern']);
  	  			if($this->debughtml)
  	  			   echo  $txt."<br/>";

                break;
            case "SetXY":
                $myx=intval($arraydata['x']);
                $myy=intval($arraydata['y']);
                $this->relativex=$this->cols['c'.$myx];
                $this->relativey=$this->rows['r'.$myy];
                $this->mergex=$myx;
                $this->mergey=$myy;//$arraydata['y'];
                break;
        
          case "SetFont":
          if($this->debughtml)
  	  			   echo  $arraydata['font'].",".$arraydata["fontsize"].",".$arraydata['fontstyle']."<br/>";
		       $this->SetFonts($this->relativex, ($this->relativey+$rowpos),$arraydata['font'],$arraydata["fontsize"],
                                $arraydata['fontstyle']);     
  	  			//if($this->debughtml)


            break;
   
          case "SetTextColor":
            $cl= str_replace('#','',$arraydata['forecolor']);
           
              if($cl!=''){
              $this->SetTextColor($this->relativex, ($this->relativey+$rowpos),$cl);

              }
  	  			if($this->debughtml)
						echo "$cl<br/>";
              break; 
          case "SetFillColor":
              if($arraydata['fill']==true){
              $cl= str_replace('#','',$arraydata['backcolor']);
               if($cl!=''){
               $this->SetFillColor($this->relativex, ($this->relativey+$rowpos),$cl);
               }
              }
  	  			if($this->debughtml)
						echo "$cl<br/>";

              break;

          case "Line":
          
          if($this->uselib==false){
              $printline=false;
            if($arraydata['printWhenExpression']=="")
                $printline=true;
            else
                $printline=$this->analyse_expression($arraydata['printWhenExpression']);                
            if($printline){                
              $x1=$arraydata["x1"];
              $x2=$arraydata["x2"];
              $y1=$arraydata["y1"];
              $y2=$arraydata["y2"];
              $linewidth=$arraydata["style"]["width"];
              $linedash=$arraydata["style"]["dash"];
              $linecolor=  str_replace('#','',$arraydata["forecolor"]);
              $col1=$this->cols['c'.$x1];
              $col2=$this->cols['c'.$x2];
              $row1=$this->rows['r'.$y1]+$this->maxrow;
              $row2=$this->rows['r'.$y2]+$this->maxrow;
              $col1=PHPExcel_Cell::stringFromColumnIndex($col1);
              $col2=PHPExcel_Cell::stringFromColumnIndex($col2);
              if($linewidth==0)
                  $linewidth=PHPExcel_Style_Border::BORDER_NONE;
              elseif($linewidth<=0.25)
                  $linewidth=PHPExcel_Style_Border::BORDER_HAIR;
              elseif($linewidth<=0.5)
                  $linewidth=PHPExcel_Style_Border::BORDER_THIN;
              elseif($linewidth<=0.75)
                  $linewidth=PHPExcel_Style_Border::medium;
              elseif($linewidth<=1)
                  $linewidth=PHPExcel_Style_Border::thick;
              else
                $linewidth=PHPExcel_Style_Border::BORDER_HAIR;
                $linewidth=PHPExcel_Style_Border::BORDER_THIN;
              if($x1==$x2){
                    $styleArray = array('borders' => array('left' => array('style' =>$linewidth,'color'=>array('rgb'=>$linecolor))));
              }elseif($y1==$y2){
                  $styleArray = array('borders' => array('top' => array('style' => $linewidth,'color'=>array('rgb'=>$linecolor))));
              }                  
                    $this->ws->getStyle("$col1$row1:$col2$row2")->applyFromArray($styleArray);   
            }
            }else{
			//ech "12312312<br/>";
//            echo "format 1,1:".print_r($this->ws->getFormat(0,0),true)."end line";
            
            
            }
              break;
          case "SetLineWidth":
              break;
          
          
         
          
          
        }
        
       
        
    }
    
        public function formatText($txt,$pattern) {
        if($pattern=="###0")
            return number_format($txt,0,"","");
        elseif($pattern=="#,##0")
            return number_format($txt,0,".",",");
        elseif($pattern=="###0.0")
            return number_format($txt,1,".","");
        elseif($pattern=="#,##0.0")
            return number_format($txt,1,".",",");
        elseif($pattern=="###0.00")
            return number_format($txt,2,".","");
        elseif($pattern=="#,##0.00")
            return number_format($txt,2,".",",");
        elseif($pattern=="###0.000")
            return number_format($txt,3,".","");
        elseif($pattern=="#,##0.000")
            return number_format($txt,3,".",",");
        elseif($pattern=="#,##0.0000")
            return number_format($txt,4,".",",");
        elseif($pattern=="###0.0000")
            return number_format($txt,4,".","");
        elseif($pattern=="dd/MM/yyyy" && $txt !="")
            return date("d/m/Y",strtotime($txt));
        elseif($pattern=="MM/dd/yyyy" && $txt !="")
            return date("m/d/Y",strtotime($txt));
        elseif($pattern=="yyyy/MM/dd" && $txt !="")
            return date("Y/m/d",strtotime($txt));
        elseif($pattern=="dd-MMM-yy" && $txt !="")
            return date("d-M-Y",strtotime($txt));
        elseif($pattern=="dd-MMM-yy" && $txt !="")
            return date("d-M-Y",strtotime($txt));
        elseif($pattern=="dd/MM/yyyy h.mm a" && $txt !="")
            return date("d/m/Y h:i a",strtotime($txt));
        elseif($pattern=="dd/MM/yyyy HH.mm.ss" && $txt !="")
            return date("d-m-Y H:i:s",strtotime($txt));
        else
            return $txt;


    }
    
    public function analyse_expression($data,$isPrintRepeatedValue="true") {
       //echo $data."<br/>";
       $tmpplussymbol='/````/';
        $pointerposition=$this->global_pointer+$this->offsetposition;
        $i=0;
        $backcurl='___';
                $singlequote="|_q_|";
        $doublequote="|_qq_|";

       $fm=str_replace('{',"_",$data);
       $fm=str_replace('}',$backcurl,$fm);
       
        //$fm=str_replace('$V_REPORT_COUNT',$this->report_count,$fm);
       $isstring=false;
       
        
//        if($this->report_count>10 && $data=='$F{qty}' || $data=='$V{qty2}')  {
//               echo "$data =  $fm<br/>";
//             }
       foreach($this->arrayVariable as $vv=>$av){
            $i++;
            $vv=str_replace('$V{',"",$vv);
            $vv=str_replace('}',$backcurl,$vv);
            $vv=str_replace("'", $singlequote,$vv);
            $vv=str_replace('"', $doublequote,$vv);

            //echo $vv.' to become '.$this->grouplist[1]["name"]."_COUNT <br/  >";
//           if($vv==$this->grouplist[0]["name"]."_COUNT" ){
//               
//             $fm=str_replace('$V_'.$vv."_COUNT",39992,$fm1);
//             //echo 39992 . "<br/>";
//           }
//           elseif($vv==$this->grouplist[1]["name"]."_COUNT"){
//             $fm=str_replace('$V_'.$vv."_COUNT",$this->group_count[$this->grouplist[1]["name"]],$fm1);
//             //echo 39992 . "<br/>";
//           }
//           elseif($vv==$this->grouplist[2]["name"]."_COUNT"){
//               $fm=str_replace('$V_'.$vv."_COUNT",$this->group_count[$this->grouplist[2]["name"]],$fm1);
//           }
//           elseif($vv==$this->grouplist[3]["name"]."_COUNT"){
//               $fm=str_replace('$V_'.$vv."_COUNT",$this->group_count[$this->grouplist[3]["name"]],$fm1);
//           }
             if(strpos($fm,'_COUNT')!==false){
             if($this->group_count[$this->grouplist[0]["name"]]==1)$this->group_count[$this->grouplist[0]["name"]]=2;
             if($this->group_count[$this->grouplist[1]["name"]]==1)$this->group_count[$this->grouplist[1]["name"]]=2;
             if($this->group_count[$this->grouplist[2]["name"]]==1)$this->group_count[$this->grouplist[2]["name"]]=2;
             if($this->group_count[$this->grouplist[3]["name"]]==1)$this->group_count[$this->grouplist[3]["name"]]=2;
                 $fm=str_replace('$V_'.$this->grouplist[0]["name"].'_COUNT'.$backcurl,($this->group_count[$this->grouplist[0]["name"]]-1),$fm);
                 $fm=str_replace('$V_'.$this->grouplist[1]["name"].'_COUNT'.$backcurl,($this->group_count[$this->grouplist[1]["name"]]-1),$fm);
                 $fm=str_replace('$V_'.$this->grouplist[2]["name"].'_COUNT'.$backcurl,($this->group_count[$this->grouplist[2]["name"]]-1),$fm);
                 $fm=str_replace('$V_'.$this->grouplist[3]["name"].'_COUNT'.$backcurl,($this->group_count[$this->grouplist[3]["name"]]-1),$fm);
                 $fm=str_replace('$V_REPORT_COUNT'.$backcurl,$this->report_count,$fm);
                 
             }
           else{
               
            if($av["ans"]!="" && is_numeric($av["ans"])&& ($this->left($av["ans"],1)||left($av["ans"],1)=='-' )>0){
                 $av["ans"]=str_replace("+",$tmpplussymbol,$av["ans"]);
                 $fm=str_replace('$V_'.$vv.$backcurl,$av["ans"],$fm);
            }
            else{
                $av["ans"]=str_replace("+",$tmpplussymbol,$av["ans"]);
                 $fm=str_replace('$V_'.$vv.$backcurl,"'".$av["ans"]."'",$fm);
            $isstring=true;
            }
                
            
            

           }
       }
      
       
     
       foreach($this->arrayParameter as  $pv => $ap) {
           $ap=str_replace("+",$tmpplussymbol,$ap);
                             $ap=str_replace("'", $singlequote,$ap);
                       $ap=str_replace('"', $doublequote,$ap);
     
           if(is_numeric($ap)&&$ap!=''&& ($this->left($ap,1)>0 || $this->left($ap,1)=='-')){
                  $fm = str_replace('$P_'.$pv.$backcurl, $ap,$fm);
           }
           else{
            $fm = str_replace('$P_'.$pv.$backcurl, "'".$ap."'",$fm);
               $isstring=true;
           } 
        }
            
       //     print_r($this->arrayfield);
       foreach($this->arrayfield as $af){
           $tmpfieldvalue=str_replace("+",$tmpplussymbol,$this->arraysqltable[$pointerposition][$af.""]);
                                  $tmpfieldvalue=str_replace("'", $singlequote,$tmpfieldvalue);
                       $tmpfieldvalue=str_replace('"', $doublequote,$tmpfieldvalue);

           if(is_numeric($tmpfieldvalue) && $tmpfieldvalue!="" && ($this->left($tmpfieldvalue,1)>0||left($tmpfieldvalue,1)=='-')){
            $fm =str_replace('$F_'.$af.$backcurl,$tmpfieldvalue,$fm);
            
           }
           else{
               $fm =str_replace('$F_'.$af.$backcurl,"'".$tmpfieldvalue."'",$fm);
            $isstring=true;
           }
           
       }
       
       if($fm=='')
           return "";
       else
       {
           
     
           //echo $fm."<br/>";
             $fm=str_replace($tmpplussymbol,"+",$fm);
             
             
//              $fm=str_replace('+',".",$fm);
             // echo $fm."<br/>";
          if(strpos($fm, '"')!==false)
            $fm=str_replace('+'," . ",$fm);
          if(strpos($fm, "'")!==false)
            $fm=str_replace('+'," . ",$fm);
     $fm=str_replace('$this->PageNo()','Not applicable',$fm);



                       $fm=str_replace($singlequote,"\'" ,$fm);
                       $fm=str_replace( $doublequote,'"',$fm);
        if((strpos('"',$fm)==false) || (strpos("'",$fm)==false)){
                           $fm=str_replace('--', '- -', $fm);
                           $fm=str_replace('++', '+ +', $fm);
                       }
                       
                       
      eval("\$result= ".$fm.";");
         
 
          
      
     //if($this->debughyperlink==true) 
    
      return $result;
      
       }
      
      
      
    }
  

    function right($value, $count) {

        return substr($value, ($count*-1));

    }

    function left($string, $count) {
        return substr($string, 0, $count);
    }
    

      public function variable_calculation($rowno) {


        foreach($this->arrayVariable as $k=>$out) {

            if($out["calculation"]!=""){
                      $out['target']=str_replace(array('$F{','}'),'',$out['target']);//,  (strlen($out['target'])-1) ); 

                
            }
                
         //   echo $out['resetType']. "<br/><br/>";
            switch($out["calculation"]) {
                case "Sum":

                        $value=$this->arrayVariable[$k]["ans"];
                    
                    
                    if($out['resetType']==''){
                            if(isset($this->arrayVariable[$k]['class'])&&$this->arrayVariable[$k]['class']=="java.sql.Time") {
                            //    foreach($this->arraysqltable as $table) {
                                    $value=$this->time_to_sec($value);

                                    $value+=$this->time_to_sec($this->arraysqltable[$rowno]["$out[target]"]);
                                    //$sum=$sum+substr($table["$out[target]"],0,2)*3600+substr($table["$out[target]"],3,2)*60+substr($table["$out[target]"],6,2);
                               // }
                                //$sum= floor($sum / 3600).":".floor($sum%3600 / 60);
                                //if($sum=="0:0"){$sum="00:00";}
                                $value=$this->sec_to_time($value);
                            }
                            else {
                                //resetGroup
                               // foreach($this->arraysqltable as $table) {
                              
                                         $value+=$this->arraysqltable[$rowno]["$out[target]"];
                                        //echo "k=$k, $value<br/>";
                              //      $table[$out["target"]];
                             //   }
                            }
                         
                    }// finisish resettype=''
                    elseif($out['resetType']=='Group') //reset type='group'
                    {
                  
                        
//                       print_r($this->grouplist);
//                       echo "<br/>";
//                       echo $out['resetGroup'] ."<br/>";
//                       //                        if( $this->arraysqltable[$this->global_pointer][$this->group_pointer]!=$this->arraysqltable[$this->global_pointer-1][$this->group_pointer])
//                        if( $this->arraysqltable[$this->global_pointer][$this->group_pointer]!=$this->arraysqltable[$this->global_pointer-1][$this->group_pointer])
  //                           $value=0;
  //            
                       if($this->groupnochange>=0){
                            
                            
                       //     for($g=$this->groupnochange;$g<4;$g++){
                         //        $value=0;    
//                                  $this->arrayVariable[$k]["ans"]=0;
  //                                echo $this->grouplist[$g]["name"].":".$this->groupnochange."<br/>";
                           // }
                       }
                      //    echo $this->global_pointer.",".$this->group_pointer.",".$this->arraysqltable[$this->global_pointer][$this->group_pointer].",".$this->arraysqltable[$this->global_pointer-1][$this->group_pointer].",".$this->arraysqltable[$rowno]["$out[target]"];
                                 if(isset($this->arrayVariable[$k]['class'])&&$this->arrayVariable[$k]['class']=="java.sql.Time") {
                                      $value+=$this->time_to_sec($this->arraysqltable[$rowno]["$out[target]"]);
                                //$sum= floor($sum / 3600).":".floor($sum%3600 / 60);
                                //if($sum=="0:0"){$sum="00:00";}
                                $value=$this->sec_to_time($value);
                                 }
                                else {
                                    
                                      $value+=$this->arraysqltable[$rowno]["$out[target]"];
                                                           
 
                                }
                                  
                    }

                        
                    $this->arrayVariable[$k]["ans"]=$value;
                    
              //      echo ",$value<br/>";
                    break;
                case "Average":
    $value=$this->arrayVariable[$k]["ans"];
                    
                    
                    if($out['resetType']==''){
                            if(isset($this->arrayVariable[$k]['class'])&&$this->arrayVariable[$k]['class']=="java.sql.Time") {
                            //    foreach($this->arraysqltable as $table) {
                                    $value=$this->time_to_sec($value);

                                    $value+=$this->time_to_sec($this->arraysqltable[$rowno]["$out[target]"]);
                                    //$sum=$sum+substr($table["$out[target]"],0,2)*3600+substr($table["$out[target]"],3,2)*60+substr($table["$out[target]"],6,2);
                               // }
                                //$sum= floor($sum / 3600).":".floor($sum%3600 / 60);
                                //if($sum=="0:0"){$sum="00:00";}
                                $value=$this->sec_to_time($value);
                            }
                            else {
                                //resetGroup
                               // foreach($this->arraysqltable as $table) {
                              
                                         $value=($value*($this->report_count-1)+$this->arraysqltable[$rowno]["$out[target]"])/$this->report_count;
                                        //echo "k=$k, $value<br/>";
                              //      $table[$out["target"]];
                             //   }
                            }
                         
                    }// finisish resettype=''
                    elseif($out['resetType']=='Group') //reset type='group'
                    {
                  
                        
//                       print_r($this->grouplist);
//                       echo "<br/>";
//                       echo $out['resetGroup'] ."<br/>";
//                       //                        if( $this->arraysqltable[$this->global_pointer][$this->group_pointer]!=$this->arraysqltable[$this->global_pointer-1][$this->group_pointer])
//                        if( $this->arraysqltable[$this->global_pointer][$this->group_pointer]!=$this->arraysqltable[$this->global_pointer-1][$this->group_pointer])
  //                           $value=0;
  //            
                       if($this->groupnochange>=0){
                            
                            
                       //     for($g=$this->groupnochange;$g<4;$g++){
                         //        $value=0;    
//                                  $this->arrayVariable[$k]["ans"]=0;
  //                                echo $this->grouplist[$g]["name"].":".$this->groupnochange."<br/>";
                           // }
                       }
                      //    echo $this->global_pointer.",".$this->group_pointer.",".$this->arraysqltable[$this->global_pointer][$this->group_pointer].",".$this->arraysqltable[$this->global_pointer-1][$this->group_pointer].",".$this->arraysqltable[$rowno]["$out[target]"];
                                 if(isset($this->arrayVariable[$k]['class'])&&$this->arrayVariable[$k]['class']=="java.sql.Time") {
                                      $value+=$this->time_to_sec($this->arraysqltable[$rowno]["$out[target]"]);
                                //$sum= floor($sum / 3600).":".floor($sum%3600 / 60);
                                //if($sum=="0:0"){$sum="00:00";}
                                $value=$this->sec_to_time($value);
                                 }
                                else {
                                    $previousgroupcount=$this->group_count[$out['resetGroup']]-2;
                                    $newgroupcount=$this->group_count[$out['resetGroup']]-1;
                                    $previoustotal=$value*$previousgroupcount;
                                    $newtotal=$previoustotal+$this->arraysqltable[$rowno]["$out[target]"];
                                    
                                    //echo "value= ($newtotal)/$newgroupcount <br/>";
                                    $value=($newtotal)/$newgroupcount;
                                    //echo "($value + " .($this->arraysqltable[$rowno]["$out[target]"]*($this->group_count[$out['resetGroup']]-2)).") / ".($this->group_count[$out['resetGroup']]-1)."<br/>";
                                      
                                                           
 
                                }
                                  
                    }

                        
                    $this->arrayVariable[$k]["ans"]=$value;

                    break;
                case "DistinctCount":
                    break;
                case "Lowest":

                    foreach($this->arraysqltable as $table) {
                        $lowest=$table[$out["target"]];
                        if($table[$out["target"]]<$lowest) {
                            $lowest=$table[$out["target"]];
                        }
                        $this->arrayVariable[$k]["ans"]=$lowest;
                    }
                    break;
                case "Highest":
                    $out["ans"]=0;
                    foreach($this->arraysqltable as $table) {
                        if($table[$out["target"]]>$out["ans"]) {
                            $this->arrayVariable[$k]["ans"]=$table[$out["target"]];
                        }
                    }
                    break;
//### A Count for groups, as a variable. Not tested yet, but seemed to work in print_r()					
                case "Count":
					$value=$this->arrayVariable[$k]["ans"];
					if( $this->arraysqltable[$this->global_pointer][$this->group_pointer]!=$this->arraysqltable[$this->global_pointer-1][$this->group_pointer])
                       $value=0;
					$value++;
                    $this->arrayVariable[$k]["ans"]=$value;
				break;
//### End of modification
                case "":
                   // $out["target"]=0;
                    if(strpos( $out["target"], "_COUNT")==-1)
                     $this->arrayVariable[$k]["ans"]=$this->analyse_expression( $out['target'], true);
                    
//                     $out["target"]= $this->analyse_expression( $out['target'], true);
                    
                    //other cases needed, temporary leave 0 if not suitable case
                    break;

            }
              
        }
    }



private function checkSwitchGroup($type="header"){

    
    /*
     * 1. loop record
     * 2. start loop group check (for i)
     *      if current last group no difference, return false
     *      if last group have difference, print that last group footer set changegroupno=i
     *    stop loop group check
     * 3. print all new group header start from i to totalgroup
     */
     $this->groupnochange=-1;
//       echo sizeof($this->grouplist).",$this->global_pointer,$type<br/>";
      if(sizeof($this->grouplist)>0 && ($this->global_pointer>0)){
  
          $i=-1;
          
          foreach($this->grouplist as $g){
             
              if($type=="header"){
                  
                  //echo ->groupExpression."<br/>";
                  
                 if($this->arraysqltable[$this->global_pointer][$g['headercontent'][0]["groupExpression"]] != 
                    $this->arraysqltable[$this->global_pointer-1][$g['headercontent'][0]["groupExpression"]] ){
                     
                    
                             //   if($this->groupnochange=="")
                               //     $this->groupnochange=0;
                               // else
                    
                     
                     
                                   
             $this->groupnochange=$i;
             
            //  echo  $this->arraysqltable[$this->global_pointer][$g["name"]] ." match ". $this->arraysqltable[$this->global_pointer-1][$g["name"]] .":".$this->groupnochange."<br/>"; 
                               return true;
                 
          }
          $i++;
          }
       }
       
       
      // if($this->groupnochange==-1)
           return false;
       //else{
         //  $this->groupnochange++;
           //return  true; //return got change group
       //}
      }	
    
}
//publish method = 'd:download, f:store as file'

public function savexls($filename,$type,$publishmethod="d"){
//echo $filename="/tmp/aaa.xls";
				$tmpfile=sys_get_temp_dir()."/".rand().".xls";
	if($this->uselib==1){
		if($publishmethod=='d'){
					$this->wb->saveAs($tmpfile);
					echo file_get_contents($tmpfile);
		}else{
					$this->wb->saveAs($filename);
		}
	
	}
	else{
	        $objWriter = PHPExcel_IOFactory::createWriter($this->wb, $type);
	        if($publishmethod=='d')
	         $objWriter->save('php://output');
	        else
            $objWriter->save($filename);
	}
//	echo "exported file";die;
	
}


public function deleteEmptyRow(){
  	if($this->uselib==0){      
         for($l=1;$l<$this->maxrow;$l++)
             $this->ws->getRowDimension($l)->setRowHeight(1);
         sort($this->rowswithdata);
         $this->rowswithdata=array_unique($this->rowswithdata);
           
         foreach($this->rowswithdata as $index =>$r){
             $this->ws->getRowDimension($r)->setRowHeight(-1);
         }
		$rrow=array();
		$lastemptyrow=0;
		 $lastrowcontinual=0;
		 $emptrowgroup=array();
         for($l=1;$l<=$this->maxrow;$l++){
            
             $rh=$this->ws->getRowDimension($l)->getRowHeight();
             
             if($rh==1){
                    if($lastemptyrow==0){
                        $lastemptyrow=$l;
                        $lastrowcontinual=0;
                    }
                    elseif($l==$lastemptyrow+$lastrowcontinual+1){
                        
                        //$lastemptyrow=$l;
                        $lastrowcontinual++;
                    }
                    else{
                        
                        $emptrowgroup[]=array("row"=>$lastemptyrow,"count"=>$lastrowcontinual);
                        $lastemptyrow=$l;
                        $lastrowcontinual=0;
                        
                    }
                     
                 
             }
                    
                 
         }
        // print_r($emptrowgroup);
         //die;
         
         for($cc=count($emptrowgroup)-1;$cc>=0;$cc--){
              $this->ws->removeRow($emptrowgroup[$cc]["row"],$emptrowgroup[$cc]["count"]+1);
          //    echo $emptrowgroup[$cc]["row"]."->".$emptrowgroup[$cc]["count"]."<br/>";
              
         }
    }else{
    	//	echo "delete empty rows";
    }
         
}


public function setColumnWidth($index,$width){
if($this->uselib==0)
	$this->ws->getColumnDimensionByColumn($index)->setWidth($width*$this->hunitmultiply);
else
	$this->ws->SetColWidth($index,  $width*40);


}   

public function setText($x,$y,$txt,$align,$pattern){
$myformat='';
if($this->uselib==0){
               
               //$stlen=strlen($txt);
               
              


               
               if(strpos($pattern,".")!==false || strpos($pattern,"#")!==false){    
                   $this->ws->getCellByColumnAndRow($x, $y)->setValueExplicit($txt, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $this->ws->getStyleByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode($pattern);	
               }else{
                   $this->ws->getCellByColumnAndRow($x, $y)->setValueExplicit($txt, PHPExcel_Cell_DataType::TYPE_STRING);
                   
               }
                 /*if(strpos($pattern,".")!==false || strpos($pattern,"#")!==false){    
                                         
                                }
                                else
                                    $this->ws->getStyleByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode('@');
               */
               //$newstrken=($this->ws->getCellByColumnAndRow($x, $y)->getValue());
               //if($this->left($txt,1)=='0' && $stlen>$newstrken){
                   
                  // for($kkk=0;$kkk<$stlen;$kkk++){
                   //$myformat.="0";
                  // echo $myformat.",$txt<br/>";
                 //  }
                   //$this->ws->getCellByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode($myformat);
               //}

                       //setCellValueByColumnAndRow($x,$y,$txt);
               

               
               if($align=='C')
                      $this->ws->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                elseif($align=='R')
                    $this->ws->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                else
                    $this->ws->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


               }
else{

	$EXCEL_HALIGN_GENERAL		= 0x00;
	$EXCEL_HALIGN_LEFT			= 0x01;
	$EXCEL_HALIGN_CENTRED		= 0x02;
	$EXCEL_HALIGN_RIGHT			= 0x03;
	$EXCEL_HALIGN_FILLED			= 0x04;
	$EXCEL_HALIGN_JUSITFIED		= 0x05;
	$EXCEL_HALIGN_SEL_CENTRED	= 0x06;	// centred across selection
	$EXCEL_HALIGN_DISTRIBUTED	= 0x07;	//
                if($align=='C')
                     $align=$EXCEL_HALIGN_CENTRED;
                elseif($align=='R')
                     $align=$EXCEL_HALIGN_RIGHT;
                else
                    $align=$EXCEL_HALIGN_LEFT;
           //$this->wfont = new ExcelFont(ExcelFont::WEIGHT_NORMAL); 
	   
        
           
                           $this->wformat->setFont($this->wfont);
                           $this->wformat->setAlignment($align);  
                                if(strpos($pattern,".")!==false || strpos($pattern,"#")!==false){    
                                         
                                         $this->wformat->setFormatString($pattern);
                                         
                                //         $this->wformat->setFont($this->wfont);  
                        		$this->ws->setDouble($x,$y-1,$txt,$this->wformat);
                                }
                                else{

                                    
                                    	$this->ws->setAnsiString($x,$y-1,$txt,$this->wformat); //Mac OSX's iconv not able to convert char * to wchar_t* well.
                                }
                                if($this->debughtml==true)
                                    echo "Reset Font/format to default<br/>";                                
				}
			
}


public function mergeCells($x1,$y1,$x2,$y2){
if($this->uselib==0){
	if($x2=="")$x2=$x1;
	if($y2=="")$y2=$y1;

	$this->ws->mergeCellsByColumnAndRow($x1,$y1,$x2, $y2);
	}
else{
	if($x2=="")$x2=0;
	if($y2=="")$y2=0;

	$this->ws->mergeCells($x1,$y1-1,($x2-$x1)+1, ($y2-$y1)+1);
	}
}


public function  SetFonts($x,$y,$font,$fontsize,$fontstyle){


if($this->uselib==0){
//echo "phpexcel";
             $f=$this->ws->getStyleByColumnAndRow($x, $y)->getFont();

             $f->setName($font);
             
             $f->setSize(intVal($fontsize));
             
                if(strpos($fontstyle,'B')!==false)
                        $f->setBold(true);
                else
                            $f->setBold(false);

                if(strpos($fontstyle,'U')!==false)
                        $f->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                else
                        $f->setUnderline(PHPExcel_Style_Font::UNDERLINE_NONE);

                if(strpos($fontstyle,'I')!==false)
                        $f->setItalic(true);
                else
                        $f->setItalic(false);
             
}else{
 //$this->ws->setFormat($this->blankformat);
 //$this->wformat= new ExcelCellFormat($this->wb);
 
     if(strpos($fontstyle,'B')!==false)
		$this->wfont=new ExcelFont(ExcelFont::WEIGHT_BOLD); 
        else	
		$this->wfont=new ExcelFont(ExcelFont::WEIGHT_NORMAL); 
   
        
        
 if(strpos($fontstyle,'I')!==false)
        $this->wfont->setItalic(true);
    else
        $this->wfont->setItalic(false);
   if(strpos($fontstyle,'U')!==false)
        $this->wfont->setUnderline(true);
    else
     	$this->wfont->setUnderline(false);
    
     
   
 	$this->wfont->setFontName($font);        
	$this->wfont->setFontSize($fontsize);
        
        
        //$this->ws->setAnsiString($x,$y-1,"$fontstyle",$this->wformat);
    /* 
   
    * 
    *     * 
    */     

	
	




}

}


public function SetTextColor($x,$y,$cl){
	if($this->uselib==0){
              $this->ws->getStyleByColumnAndRow($x, $y)->getFont()->getColor()->setARGB("FF".$cl);
 }else{
/*
  	EGA_BLACK	= 0,	// 000000H
	EGA_WHITE	= 1,	// FFFFFFH
	EGA_RED		= 2,	// FF0000H
	EGA_GREEN	= 3,	// 00FF00H
	EGA_BLUE	= 4,	// 0000FFH
	EGA_YELLOW	= 5,	// FFFF00H
	EGA_MAGENTA	= 6,	// FF00FFH
	EGA_CYAN	= 7		// 00FFFFH
	*/
//	 	$this->wfont->setColor(0);
              }
              
              } 
              
public function SetFillColor($x,$y,$cl){
	if($this->uselib==0){
               $this->ws->getStyleByColumnAndRow($x,$y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $this->ws->getStyleByColumnAndRow($x,$y)->getFill()->getStartColor()->setARGB('FF'.$cl);
 }else{
 
  	/*
  	EGA_BLACK	= 0,	// 000000H
	EGA_WHITE	= 1,	// FFFFFFH
	EGA_RED		= 2,	// FF0000H
	EGA_GREEN	= 3,	// 00FF00H
	EGA_BLUE	= 4,	// 0000FFH
	EGA_YELLOW	= 5,	// FFFF00H
	EGA_MAGENTA	= 6,	// FF00FFH
	EGA_CYAN	= 7		// 00FFFFH
	*/
// 	$this->wformat->setBackGround(1);

 

              }


}
}
