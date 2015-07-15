<?php
//version 0.7a
class PHPJasperXML{
private $adjust=1.2;
private $pdflib;
private $lang;
public $debugsql=false;
public $newPageGroup = false;

public function PHPJasperXML($lang="en",$pdflib="fpdf")
{	
$this->lang=$lang;
$this->pdflib=$pdflib;
}

public function connect($db_host,$db_user,$db_pass,$db_name)
{
 	 if(!$this->con)  
         { 
             $myconn = @mysql_connect($db_host,$db_user,$db_pass);  
	     if($myconn)  
             {   
                 $seldb = @mysql_select_db($db_name,$myconn);  
                 if($seldb)  
                 {  
                     $this->con = true;   
                     return true;   
                 }
		 else  
                 {  
                     return false;   
                 }  
             } else  
             {  
                 return false;   
             }  
         } else  
         {  
             return true;   
         }  
}

public function disconnect()  
    {  
        if($this->con)  
        {  
            if(@mysql_close())  
            {  
                           $this->con = false;   
                return true;   
            }  
           else  
           {  
              return false;   
           }  
       }  
   }     

public function xml_dismantle($xml)
{	
	$this->page_setting($xml);
	foreach ($xml as $k=>$out)
	{
	switch($k)
	 {
	    case "parameter":
		$this->parameter_handler($out);
		break;
	    case "queryString":
		$this->queryString_handler($out);
		break;
	    case "field":
		$this->field_handler($out);
		break;
	    case "variable":
		$this->variable_handler($out);
		break;
	    case "group":
		$this->group_handler($out);
		break;
	    case "background":
		$this->pointer=&$this->arraybackground;
		$this->pointer[]=array("height"=>$out->band[height],"splitType"=>$out->band[splitType]);
		foreach ($out as $bg)
		{
		$this->default_handler($bg);
		}
		break;
	    default:
		foreach ($out as $object)
		{
		eval("\$this->pointer=&"."\$this->array$k".";");
		$this->arrayband[]=array("name"=>$k);
		$this->pointer[]=array("type"=>"band","height"=>$object[height],"splitType"=>$object[splitType],"y_axis"=>$this->y_axis);
		$this->default_handler($object);}
		$this->y_axis=$this->y_axis+$out->band[height];	//after handle , then adjust y axis
		break;
	     
	 }
	
	}
}

public function page_setting($xml_path)		//read level 0,Jasperreport page setting
{
	$this->arrayPageSetting[orientation]="P";
	$this->arrayPageSetting[name]=$xml_path[name];
	$this->arrayPageSetting[language]=$xml_path[language];
	$this->arrayPageSetting[pageWidth]=$xml_path[pageWidth];
	$this->arrayPageSetting[pageHeight]=$xml_path[pageHeight];
	if(isset($xml_path[orientation])){$this->arrayPageSetting[orientation]=substr($xml_path[orientation],0,1);}
	$this->arrayPageSetting[columnWidth]=$xml_path[columnWidth];
	$this->arrayPageSetting[leftMargin]=$xml_path[leftMargin];
	$this->arrayPageSetting[rightMargin]=$xml_path[rightMargin];
	$this->arrayPageSetting[topMargin]=$xml_path[topMargin];	$this->y_axis=$xml_path[topMargin];
	$this->arrayPageSetting[bottomMargin]=$xml_path[bottomMargin];
}


public function parameter_handler($xml_path)
{
	$this->arrayParameter["$xml_path[name]"];
}

public function queryString_handler($xml_path)
{
		$this->sql =$xml_path;
		if(isset($this->arrayParameter))
		{
			foreach($this->arrayParameter as  $v => $a)
			{
			   $this->sql = str_replace('$P{'.$v.'}', $a, $this->sql);
			}
		}
}

public function field_handler($xml_path)
{
	$this->arrayfield[]=$xml_path[name];
}

public function variable_handler($xml_path)
{	
	$this->arrayVariable["$xml_path[name]"]=array("calculation"=>$xml_path[calculation],"target"=>substr($xml_path->variableExpression,3,-1),"class"=>$xml_path['class']);
}

public function group_handler($xml_path)
{
	$this->arrayband[]=array("gname"=>$xml_path[name],"isStartNewPage"=>$xml_path[isStartNewPage],"groupExpression"=>substr($xml_path->groupExpression,3,-1),"name"=>"group");
		foreach($xml_path as $tag=>$out)
		{	
		switch ($tag)
		{
		case (groupHeader):
		$this->pointer=&$this->arraygroup["$xml_path[name]"][groupHeader];
		$this->pointer[]=array("type"=>"band","height"=>$out->band[height]+0,"y_axis"=>"","groupExpression"=>substr($xml_path->groupExpression,3,-1));
			foreach($out as $band)
			{
			$this->default_handler($band);
			}
		$this->y_axis=$this->y_axis+$out->band[height];		//after handle , then adjust y axis 
		break;
		case (groupFooter):
		$this->pointer=&$this->arraygroup["$xml_path[name]"][groupFooter];
		$this->pointer[]=array("type"=>"band","height"=>$out->band[height]+0,"y_axis"=>"","groupExpression"=>substr($xml_path->groupExpression,3,-1));
			foreach($out as $band)
			{
			$this->default_handler($band);
			}
		break;
		default:
		break;
		}
	
	}
}

public function default_handler($xml_path)
{
	foreach($xml_path as $k=>$out)
	{
	
	switch($k)
	 {
	    case "staticText":
		$this->element_staticText($out);
		break;
	    case "image":
		$this->element_image($out);
		break;
	    case "line":
		$this->element_line($out);
		break;
	    case "rectangle":
		$this->element_rectangle($out);
		break;
	    case "textField":
		$this->element_textField($out);
		break;
	    default:
		break;
	 }
	};
}

public function element_staticText($data)
{
	$align="L";	$fill=0;	$border=0;	$fontsize=10;	$font="helvetica";	$fontstyle="";
	$textcolor = array("r"=>0,"g"=>0,"b"=>0);	$fillcolor = array("r"=>255,"g"=>255,"b"=>255);	$txt="";
	$drawcolor=array("r"=>0,"g"=>0,"b"=>0);	$height=$data->reportElement[height];
	$stretchoverflow="true";		$printoverflow="false";
	if(isset($data->reportElement[forecolor]))
	{$textcolor = array("r"=>hexdec(substr($data->reportElement[forecolor],1,2)),"g"=>hexdec(substr($data->reportElement[forecolor],3,2)),"b"=>hexdec(substr($data->reportElement[forecolor],5,2)));}
	if(isset($data->reportElement[backcolor]))
	{$fillcolor = array("r"=>hexdec(substr($data->reportElement[backcolor],1,2)),"g"=>hexdec(substr($data->reportElement[backcolor],3,2)),"b"=>hexdec(substr($data->reportElement[backcolor],5,2)));
	}
	if($data->reportElement[mode]=="Opaque"){$fill=1;}
	if(isset($data[isStretchWithOverflow])&&$data[isStretchWithOverflow]=="true"){$stretchoverflow="true";}
	if(isset($data->reportElement[isPrintWhenDetailOverflows])&&$data->reportElement[isPrintWhenDetailOverflows]=="true"){$printoverflow="true"; $stretchoverflow="false";}
	if((isset($data->box))&&($data->box->pen[lineWidth]>0))
	{$border=1;
		if(isset($data->box->pen[lineColor]))
		{$drawcolor=array("r"=>hexdec(substr($data->box->pen[lineColor],1,2)),"g"=>hexdec(substr($data->box->pen[lineColor],3,2)),"b"=>hexdec(substr($data->box->pen[lineColor],5,2)));}
	}
	if(isset($data->textElement[textAlignment])){$align=$this->get_first_value($data->textElement[textAlignment]);}
	if(isset($data->textElement->font[pdfFontName])){$font=$data->textElement->font[pdfFontName];}
	if(isset($data->textElement->font[size])){$fontsize=$data->textElement->font[size];}	
	if(isset($data->textElement->font[isBold])&&$data->textElement->font[isBold]=="true"){$fontstyle=$fontstyle."B";}
	if(isset($data->textElement->font[isItalic])&&$data->textElement->font[isItalic]=="true"){$fontstyle=$fontstyle."I";}
	if(isset($data->textElement->font[isUnderline])&&$data->textElement->font[isUnderline]=="true"){$fontstyle=$fontstyle."U";}
	if(isset($data->reportElement[key])){$height=$fontsize*$this->adjust;}
	$this->pointer[]=array("type"=>"SetXY","x"=>$data->reportElement[x],"y"=>$data->reportElement[y],"hidden_type"=>"SetXY");
	$this->pointer[]=array("type"=>"SetTextColor","r"=>$textcolor[r],"g"=>$textcolor[g],"b"=>$textcolor[b],"hidden_type"=>"textcolor");
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>$drawcolor[r],"g"=>$drawcolor[g],"b"=>$drawcolor[b],"hidden_type"=>"drawcolor");	
	$this->pointer[]=array("type"=>"SetFillColor","r"=>$fillcolor[r],"g"=>$fillcolor[g],"b"=>$fillcolor[b],"hidden_type"=>"fillcolor");
	$this->pointer[]=array("type"=>"SetFont","font"=>$font,"fontstyle"=>$fontstyle,"fontsize"=>$fontsize,"hidden_type"=>"font");
	//"height"=>$data->reportElement[height]
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>$data->text,"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"statictext","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow);

}

public function element_image($data)
{
	$imagepath=$data->imageExpression;
	//$imagepath= substr($data->imageExpression, 1, -1);
	//$imagetype= substr($imagepath,-3);

	switch($data[scaleImage])
	{
	case "FillFrame":
	$this->pointer[]=array("type"=>"Image","path"=>$imagepath,"x"=>$data->reportElement[x]+0,"y"=>$data->reportElement[y]+0,"width"=>$data->reportElement[width]+0,"height"=>$data->reportElement[height]+0,"imgtype"=>$imagetype,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"hidden_type"=>"image");
	break;
	default:
	$this->pointer[]=array("type"=>"Image","path"=>$imagepath,"x"=>$data->reportElement[x]+0,"y"=>$data->reportElement[y]+0,"width"=>$data->reportElement[width]+0,"height"=>$data->reportElement[height]+0,"imgtype"=>$imagetype,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"hidden_type"=>"image");
	break;
	}
}

public function element_line($data)
{	//default line width=0.567(no detect line width)
	$drawcolor=array("r"=>0,"g"=>0,"b"=>0); $hidden_type="line";
	if(isset($data->reportElement[forecolor]))
	{$drawcolor=array("r"=>hexdec(substr($data->reportElement[forecolor],1,2)),"g"=>hexdec(substr($data->reportElement[forecolor],3,2)),"b"=>hexdec(substr($data->reportElement[forecolor],5,2)));}
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>$drawcolor[r],"g"=>$drawcolor[g],"b"=>$drawcolor[b],"hidden_type"=>"drawcolor");	
	if(isset($data->reportElement[positionType])&&$data->reportElement[positionType]=="FixRelativeToBottom")
	{$hidden_type="relativebottomline";}
	if($data->reportElement[width][0]+0 > $data->reportElement[height][0]+0)	//width > height means horizontal line
	{$this->pointer[]=array("type"=>"Line", "x1"=>$data->reportElement[x],"y1"=>$data->reportElement[y],"x2"=>$data->reportElement[x]+$data->reportElement[width],"y2"=>$data->reportElement[y]+$data->reportElement[height]-1,"hidden_type"=>$hidden_type);}
	elseif($data->reportElement[height][0]+0>$data->reportElement[width][0]+0)		//vertical line
	{$this->pointer[]=array("type"=>"Line", "x1"=>$data->reportElement[x],"y1"=>$data->reportElement[y],"x2"=>$data->reportElement[x]+$data->reportElement[width]-1,"y2"=>$data->reportElement[y]+$data->reportElement[height],"hidden_type"=>$hidden_type);}
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>0,"g"=>0,"b"=>0,"hidden_type"=>"drawcolor");
}

public function element_rectangle($data)
{
	$drawcolor=array("r"=>0,"g"=>0,"b"=>0);
	if(isset($data->reportElement[forecolor]))
	{$drawcolor=array("r"=>hexdec(substr($data->reportElement[forecolor],1,2)),"g"=>hexdec(substr($data->reportElement[forecolor],3,2)),"b"=>hexdec(substr($data->reportElement[forecolor],5,2)));}
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>$drawcolor[r],"g"=>$drawcolor[g],"b"=>$drawcolor[b],"hidden_type"=>"drawcolor");
	$this->pointer[]=array("type"=>"Rect","x"=>$data->reportElement[x],"y"=>$data->reportElement[y],"width"=>$data->reportElement[width],"height"=>$data->reportElement[height],"hidden_type"=>"rect");
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>0,"g"=>0,"b"=>0,"hidden_type"=>"drawcolor");
}

public function element_textField($data)
{
	$align="L";	$fill=0;	$border=0;	$fontsize=10;	$font="helvetica";	$fontstyle="";
	$textcolor = array("r"=>0,"g"=>0,"b"=>0);	$fillcolor = array("r"=>255,"g"=>255,"b"=>255);
	$stretchoverflow="false";		$printoverflow="false";	$height=$data->reportElement[height];
	$drawcolor=array("r"=>0,"g"=>0,"b"=>0);
	if(isset($data->reportElement[forecolor]))
	{$textcolor = array("r"=>hexdec(substr($data->reportElement[forecolor],1,2)),"g"=>hexdec(substr($data->reportElement[forecolor],3,2)),"b"=>hexdec(substr($data->reportElement[forecolor],5,2)));}
	if(isset($data->reportElement[backcolor]))
	{$fillcolor = array("r"=>hexdec(substr($data->reportElement[backcolor],1,2)),"g"=>hexdec(substr($data->reportElement[backcolor],3,2)),"b"=>hexdec(substr($data->reportElement[backcolor],5,2)));}
	if($data->reportElement[mode]=="Opaque"){$fill=1;}
	if(isset($data[isStretchWithOverflow])&&$data[isStretchWithOverflow]=="true"){$stretchoverflow="true";}
	if(isset($data->reportElement[isPrintWhenDetailOverflows])&&$data->reportElement[isPrintWhenDetailOverflows]=="true"){$printoverflow="true";}
	if(isset($data->box)&&$data->box->pen[lineWidth]>0)
	{$border=1;
		if(isset($data->box->pen[lineColor]))
		{$drawcolor=array("r"=>hexdec(substr($data->box->pen[lineColor],1,2)),"g"=>hexdec(substr($data->box->pen[lineColor],3,2)),"b"=>hexdec(substr($data->box->pen[lineColor],5,2)));}
	}
	if(isset($data->reportElement[key])){$height=$fontsize*$this->adjust;}
	if(isset($data->textElement[textAlignment])){$align=$this->get_first_value($data->textElement[textAlignment]);}
	if(isset($data->textElement->font[pdfFontName])){$font=$data->textElement->font[pdfFontName];}
	if(isset($data->textElement->font[size])){$fontsize=$data->textElement->font[size];}	
	if(isset($data->textElement->font[isBold])&&$data->textElement->font[isBold]=="true"){$fontstyle=$fontstyle."B";}
	if(isset($data->textElement->font[isItalic])&&$data->textElement->font[isItalic]=="true"){$fontstyle=$fontstyle."I";}
	if(isset($data->textElement->font[isUnderline])&&$data->textElement->font[isUnderline]=="true"){$fontstyle=$fontstyle."U";}
	$this->pointer[]=array("type"=>"SetXY","x"=>$data->reportElement["x"],"y"=>$data->reportElement["y"],"hidden_type"=>"SetXY");
	$this->pointer[]=array("type"=>"SetTextColor","r"=>$textcolor[r],"g"=>$textcolor[g],"b"=>$textcolor[b],"hidden_type"=>"textcolor");
	$this->pointer[]=array("type"=>"SetDrawColor","r"=>$drawcolor[r],"g"=>$drawcolor[g],"b"=>$drawcolor[b],"hidden_type"=>"drawcolor");	
	$this->pointer[]=array("type"=>"SetFillColor","r"=>$fillcolor[r],"g"=>$fillcolor[g],"b"=>$fillcolor[b],"hidden_type"=>"fillcolor");
	
	$this->pointer[]=array("type"=>"SetFont","font"=>$font,"fontstyle"=>$fontstyle,"fontsize"=>$fontsize,"hidden_type"=>"font");
	
	switch ($data->textFieldExpression)
	{
	case 'new java.util.Date()':
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>date("d/m/y h:i A", time()),"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"date","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);
	break;
	case '"Page "+$V{PAGE_NUMBER}+" of"':
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>'Page $this->PageNo() of',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);
	break;
	case '$V{PAGE_NUMBER}':
		if(isset($data[evaluationTime])&&$data[evaluationTime]=="Report")
		{$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>'{nb}',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);}
		else{$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>'$this->PageNo()',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);}
	break;
	case '" " + $V{PAGE_NUMBER}':
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>' {nb}',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"nb","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);
	break;
	case '$V{REPORT_COUNT}':
	$this->report_count=0;
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>&$this->report_count,"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"report_count","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern]);
	break;
	default:
        $writeHTML=false;
        if($data->reportElement->property[name]=="writeHTML")
        $writeHTML=$data->reportElement->property[value];
	$this->pointer[]=array("type"=>"MultiCell","width"=>$data->reportElement[width],"height"=>$height,"txt"=>$data->textFieldExpression,"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"field","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"printWhenExpression"=>$data->reportElement->printWhenExpression,"link"=>substr($data->hyperlinkReferenceExpression,1,-1),"pattern"=>$data[pattern],"writeHTML"=>$writeHTML);
	break;
	}
}

public function transferDBtoArray($host,$user,$password,$db,$sql=false)  
{ $this->m=0;
	if(!$this->connect($host,$user,$password,$db))	//connect database
	{
	echo "Falha ao conectar com o banco";
	exit(0);
	}
	if($this->debugsql==true){
	echo $this->sql;
	die;
	}
	if($sql){
		$this->sql = $sql;
	}
	$result = @mysql_query($this->sql); //query from db
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		foreach($this->arrayfield as $out)
		{
		$this->arraysqltable[$this->m]["$out"]=utf8_decode($row["$out"]);
		}
		$this->m++;
	}
	$this->disconnect();	//close connection to db

	if(isset($this->arrayVariable))	//if self define variable existing, go to do the calculation
	{$this->variable_calculation($m);}
}  

public function time_to_sec($time) {
    $hours = substr($time, 0, -6);
    $minutes = substr($time, -5, 2);
    $seconds = substr($time, -2);

    return $hours * 3600 + $minutes * 60 + $seconds;
}

public function sec_to_time($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor($seconds % 3600 / 60);
    $seconds = $seconds % 60;

    return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
}

public function variable_calculation($m=0)
{
	foreach($this->arrayVariable as $k=>$out)
	{
		switch($out[calculation])
		{case "Sum":
			$sum=0;
			if(isset($this->arrayVariable[$k]['class'])&&$this->arrayVariable[$k]['class']=="java.sql.Time")
			{
				foreach($this->arraysqltable as $table)
				{
                                    $sum=$sum+$this->time_to_sec($table["$out[target]"]);
				//$sum=$sum+substr($table["$out[target]"],0,2)*3600+substr($table["$out[target]"],3,2)*60+substr($table["$out[target]"],6,2);
				}
				//$sum= floor($sum / 3600).":".floor($sum%3600 / 60);
				//if($sum=="0:0"){$sum="00:00";}
                                $sum=$this->sec_to_time($sum);
			}
			else
			{
			foreach($this->arraysqltable as $table)
			{
			$sum=$sum+$table["$out[target]"];
			$table["$out[target]"];
			}
			}
			
			$this->arrayVariable[$k][ans]=$sum;
		break;
		case "Average":
			$sum=0;
			foreach($this->arraysqltable as $table)
			{$sum=$sum+$table["$out[target]"];
			}
			$this->arrayVariable[$k][ans]=$sum/$m;	
		break;
		case "DistinctCount":
		break;
		case "Lowest":
			
			foreach($this->arraysqltable as $table)
			{	$lowest=$table["$out[target]"];
				if($table["$out[target]"]<$lowest)
				{$lowest=$table["$out[target]"];}
				$this->arrayVariable[$k][ans]=$lowest;
			}
		break;
		case "Highest":
			$out[ans]=0;
			foreach($this->arraysqltable as $table)
			{
				if($table["$out[target]"]>$out[ans])
				{$this->arrayVariable[$k][ans]=$table["$out[target]"];}
			}
		break;
		default:
		$out[target]=0;		//other cases needed, temporary leave 0 if not suitable case
		break;
		
		}
	}
}
/////////////////////////////////////////////////////display to pdf part/////////////////////////////////////////////////////////////

public function outpage($out_method="I")
{
    if($this->lang=="cn"){
	if($this->arrayPageSetting[orientation]=="P")
	{
            $this->pdf=new PDF_Unicode($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageWidth],$this->arrayPageSetting[pageHeight]));
        	$this->pdf->AddUniGBhwFont("uGB");
        }
	else
	{$this->pdf=new PDF_Unicode($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageHeight],$this->arrayPageSetting[pageWidth]));
	$this->pdf->AddUniGBhwFont("uGB");
	}
    }
    else{

        if($this->pdflib=="TCPDF"){
	 if($this->arrayPageSetting[orientation]=="P")
            $this->pdf=new TCPDF($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageWidth],$this->arrayPageSetting[pageHeight]));
	 else
            $this->pdf=new TCPDF($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageHeight],$this->arrayPageSetting[pageWidth]));
        }else{
	 if($this->arrayPageSetting[orientation]=="P")
            $this->pdf=new FPDF($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageWidth],$this->arrayPageSetting[pageHeight]));
	 else
            $this->pdf=new FPDF($this->arrayPageSetting[orientation],'pt',array($this->arrayPageSetting[pageHeight],$this->arrayPageSetting[pageWidth]));
        }
    }
	//$this->arrayPageSetting[language]=$xml_path[language];
	$this->pdf->SetLeftMargin($this->arrayPageSetting[leftMargin]);
	$this->pdf->SetRightMargin($this->arrayPageSetting[rightMargin]);
	$this->pdf->SetTopMargin($this->arrayPageSetting[topMargin]);
	$this->pdf->SetAutoPageBreak(true,$this->arrayPageSetting[bottomMargin]/2);
	$this->pdf->AddPage();
	$this->pdf->AliasNbPages();
	$this->global_pointer=0;
	$this->background();
	foreach ($this->arrayband as $band)
	{
		switch($band[name])
		{
		case "pageHeader":
			if(!$this->newPageGroup){
				$headerY = $this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];
				$this->pageHeader($headerY);
			}else{
				$this->pageHeaderNewPage();
			}
		break;
		case "detail":
			if(!$this->newPageGroup){
				$this->detail();
			}else{
				$this->detailNewPage();
			}
		break;
		case "group":
			$this->group_pointer=$band[groupExpression];
			$this->group_name=$band[gname];
		break;
		default:
		break;
		
		}

	}
	return $this->pdf->Output($this->arrayPageSetting[name].".pdf",$out_method);	//send out the complete page
	
}
public function background()
{
	foreach ($this->arraybackground as $out)
	{
	switch($out[hidden_type])
	{
	case "field":
	$this->display($out,$this->arrayPageSetting[topMargin],true);
	break;
	default:
	$this->display($out,$this->arrayPageSetting[topMargin],false);
	break;
	}
	}
}

public function pageHeader($headerY)
{
	if(isset($this->arraypageHeader))
	{$this->arraypageHeader[0][y_axis]=$this->arrayPageSetting[topMargin];}
	foreach ($this->arraypageHeader as $out)
	{
	switch($out[hidden_type])
	{
	case "field":
	$this->display($out,$this->arraypageHeader[0][y_axis],true);
	break;
	default:
	$this->display($out,$this->arraypageHeader[0][y_axis],false);
	break;
	}
	}
	if(isset($this->arraygroup))
	{$this->group($headerY);}
}

public function group($headerY)
{	$gname=$this->arrayband[0][gname]."";
	if(isset($this->arraypageHeader))
	{$this->arraygroup[$gname][groupHeader][0][y_axis]=$headerY;}
	if(isset($this->arraypageFooter))
	{$this->arraygroup[$gname][groupFooter][0][y_axis]=$this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin]-$this->arraygroup[$gname][groupFooter][0][height];}
	else{$this->arraygroup[$gname][groupFooter][0][y_axis]=$this->arrayPageSetting[pageHeight]-$this->arrayPageSetting[bottomMargin]-$this->arraygroup[$gname][groupFooter][0][height];}

	if(isset($this->arraygroup))
	{
	foreach($this->arraygroup[$gname] as $name=>$out)
	{
		switch($name)
		{
		case "groupHeader":
			foreach($out as $path)
			{	switch($path[hidden_type])
				{
				case "field":
				$this->display($path,$this->arraygroup[$gname][groupHeader][0][y_axis],true);
				break;
				default:
				$this->display($path,$this->arraygroup[$gname][groupHeader][0][y_axis],false);
				break;
				}
			}
		break;
		case "groupFooter":
			foreach($out as $path)
			{	switch($path[hidden_type])
				{
				case "field":
				$this->display($path,$this->arraygroup[$gname][groupFooter][0][y_axis],true);
				break;
				default:
				$this->display($path,$this->arraygroup[$gname][groupFooter][0][y_axis],false);
				break;
				}
			}
		break;
		default:
		break;
		}
	}
	}
}

public function pageHeaderNewPage()
{
	if(isset($this->arraypageHeader))
	{$this->arraypageHeader[0][y_axis]=$this->arrayPageSetting[topMargin];}
	foreach ($this->arraypageHeader as $out)
	{
	switch($out[hidden_type])
	{
	case "field":
	$this->display($out,$this->arraypageHeader[0][y_axis],true);
	break;
	default:
	$this->display($out,$this->arraypageHeader[0][y_axis],false);
	break;
	}
	}
	if(isset($this->arraygroup))
	{$this->groupNewPage();}
}

public function groupNewPage()
{	$gname=$this->arrayband[0][gname]."";
	if(isset($this->arraypageHeader))
	{$this->arraygroup[$gname][groupHeader][0][y_axis]=$this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];}
	if(isset($this->arraypageFooter))
	{$this->arraygroup[$gname][groupFooter][0][y_axis]=$this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin]-$this->arraygroup[$gname][groupFooter][0][height];}
	else{$this->arraygroup[$gname][groupFooter][0][y_axis]=$this->arrayPageSetting[pageHeight]-$this->arrayPageSetting[bottomMargin]-$this->arraygroup[$gname][groupFooter][0][height];}

	if(isset($this->arraygroup))
	{
	foreach($this->arraygroup[$gname] as $name=>$out)
	{
		switch($name)
		{
		case "groupHeader":
			foreach($out as $path)
			{	switch($path[hidden_type])
				{
				case "field":
				$this->display($path,$this->arraygroup[$gname][groupHeader][0][y_axis],true);
				break;
				default:
				$this->display($path,$this->arraygroup[$gname][groupHeader][0][y_axis],false);
				break;
				}
			}
		break;
		case "groupFooter":
			foreach($out as $path)
			{	switch($path[hidden_type])
				{
				case "field":
				$this->display($path,$this->arraygroup[$gname][groupFooter][0][y_axis],true);
				break;
				default:
				$this->display($path,$this->arraygroup[$gname][groupFooter][0][y_axis],false);
				break;
				}
			}
		break;
		default:
		break;
		}
	}
	}
}

public function pageFooter()
{
	if(isset($this->arraypageFooter))
	{
		foreach ($this->arraypageFooter as $out)
		{
		switch($out[hidden_type])
		{
		case "field":
		$this->display($out,$this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin],true);
		break;
		default:
		$this->display($out,$this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin],false);
		break;
		}
		}
	}
	else{$this->lastPageFooter();}
}

public function lastPageFooter()
{
	if(isset($this->arraylastPageFooter))
	{
	foreach ($this->arraylastPageFooter as $out)
	{
	switch($out[hidden_type])
	{
	case "field":
	$this->display($out,$this->arrayPageSetting[pageHeight]-$this->arraylastPageFooter[0][height]-$this->arrayPageSetting[bottomMargin],true);
	break;
	default:
	$this->display($out,$this->arrayPageSetting[pageHeight]-$this->arraylastPageFooter[0][height]-$this->arrayPageSetting[bottomMargin],false);
	break;
	}
	}
	}
}


public function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->pdf->CurrentFont['cw'];
	if($w==0)
		$w=$this->pdf->w-$this->pdf->rMargin-$this->pdf->x;
	$wmax=($w-2*$this->pdf->cMargin)*1000/$this->pdf->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}

public function detail()
{$field_pos_y=$this->arraydetail[0][y_axis];	$biggestY=0;	$checkpoint=$this->arraydetail[0][y_axis];	$tempY=$this->arraydetail[0][y_axis];
if($this->arraysqltable){
foreach($this->arraysqltable as $row)
{	
	
	if(isset($this->arraygroup)&&($this->global_pointer>0)&&($this->arraysqltable["$this->global_pointer"]["$this->group_pointer"]!=$this->arraysqltable["$this->global_pointer"-1]["$this->group_pointer"]))	//check the group's groupExpression existed and same or not
	{
	//$this->pageFooter();
	//$this->pdf->AddPage();
	$this->background();
	$headerY = $biggestY+40;
	$this->pageHeader($headerY);
	$checkpoint=$headerY+40;
	$biggestY = $headerY+40;	
	$tempY=$this->arraydetail[0][y_axis];
	}	

	foreach($this->arraydetail as $compare)	//this loop is to count possible biggest Y of the coming row
	{
		switch($compare[hidden_type])
		{
		case "field":
		$txt=$this->analyse_expression($row["$compare[txt]"]);
		if(isset($this->arraygroup["$this->group_name"][groupFooter])&&(($checkpoint+($compare[height]*$txt))>($this->arrayPageSetting[pageHeight]-$this->arraygroup["$this->group_name"][groupFooter][0][height]-$this->arrayPageSetting[bottomMargin])))//check group footer existed or not
		{
		$this->pageFooter();
		$this->pdf->AddPage();
		$this->background();
		$this->pageHeader(0);
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	
		$tempY=$this->arraydetail[0][y_axis];
		}
		elseif(isset($this->arraypageFooter)&&(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>($this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin])))//check pagefooter existed or not
		{
		$this->pageFooter();
		$this->pdf->AddPage();
		$this->background();
		$headerY = $this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];
		$this->pageHeader($headerY);
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	
		$tempY=$this->arraydetail[0][y_axis];
		}
		elseif(isset($this->arraylastPageFooter)&&(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>($this->arrayPageSetting[pageHeight]-$this->arraylastPageFooter[0][height]-$this->arrayPageSetting[bottomMargin])))//check lastpagefooter existed or not
		{
		$this->lastPageFooter();
		$this->pdf->AddPage();
		$this->background();
		$this->pageHeader(0);
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	$tempY=$this->arraydetail[0][y_axis];
		}
	
		if(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>$tempY)
		{$tempY=$checkpoint+($compare[height]*($this->NbLines($compare[width],$txt)));}
		break;
		case "relativebottomline":
		break;
		case "report_count":
			$this->report_count++;
		break;
		default:
		$this->display($compare,$checkpoint);
		break;
		}
	}

	

	if($checkpoint+$this->arraydetail[0][height]>($this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin]))	//check the upcoming band is greater than footer position or not
	{$this->pageFooter();
	$this->pdf->AddPage();
	$this->background();
	$headerY = $this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];
	$this->pageHeader($headerY);
	$checkpoint=$this->arraydetail[0][y_axis];
	$biggestY=0; $tempY=$this->arraydetail[0][y_axis];}

	foreach ($this->arraydetail as $out)
	{
		switch ($out[hidden_type])
		{
		case "field":
	
		$this->prepare_print_array=array("type"=>"MultiCell","width"=>$out[width],"height"=>$out[height],"txt"=>$out[txt],"border"=>$out[border],"align"=>$out[align],"fill"=>$out[fill],"hidden_type"=>$out[hidden_type],"printWhenExpression"=>$out[printWhenExpression],"soverflow"=>$out[soverflow],"poverflow"=>$out[poverflow],"link"=>$out[link],"pattern"=>$out[pattern]);
		$this->display($this->prepare_print_array,0,true);
		
		if($this->pdf->GetY() > $biggestY)
		{$biggestY = $this->pdf->GetY();}
		break;
		case "relativebottomline":
		//$this->relativebottomline($out,$tempY);
		$this->relativebottomline($out,$biggestY);	
		break;
		default:

		$this->display($out,$checkpoint);
		
		//$checkpoint=$this->pdf->GetY();
		break;		
		}
	}
	$this->pdf->SetY($biggestY);
	if($biggestY>$checkpoint+$this->arraydetail[0][height])
	{$checkpoint=$biggestY;}
	elseif($biggestY<$checkpoint+$this->arraydetail[0][height])
	{$checkpoint=$checkpoint+$this->arraydetail[0][height];}
	else{$checkpoint=$biggestY;}

	//if(isset($this->arraygroup)){$this->global_pointer++;}
	$this->global_pointer++;
}
}else{
	echo utf8_decode("Não ha resultados para a consulta");
	exit(0);
}	
$this->global_pointer--;
if(isset($this->arraylastPageFooter))
{$this->lastPageFooter();}
else{$this->pageFooter();}
}

public function detailNewPage()
{$field_pos_y=$this->arraydetail[0][y_axis];	$biggestY=0;	$checkpoint=$this->arraydetail[0][y_axis];	$tempY=$this->arraydetail[0][y_axis];
if($this->arraysqltable){
foreach($this->arraysqltable as $row)
{	
	
	if(isset($this->arraygroup)&&($this->global_pointer>0)&&($this->arraysqltable["$this->global_pointer"]["$this->group_pointer"]!=$this->arraysqltable["$this->global_pointer"-1]["$this->group_pointer"]))	//check the group's groupExpression existed and same or not
	{
	$this->pageFooter();
	$this->pdf->AddPage();
	$this->background();
	$this->pageHeaderNewPage();
	$checkpoint=$this->arraydetail[0][y_axis];
	$biggestY = 0;	
	$tempY=$this->arraydetail[0][y_axis];
	}	

	foreach($this->arraydetail as $compare)	//this loop is to count possible biggest Y of the coming row
	{
		switch($compare[hidden_type])
		{
		case "field":
		$txt=$this->analyse_expression($row["$compare[txt]"]);
		if(isset($this->arraygroup["$this->group_name"][groupFooter])&&(($checkpoint+($compare[height]*$txt))>($this->arrayPageSetting[pageHeight]-$this->arraygroup["$this->group_name"][groupFooter][0][height]-$this->arrayPageSetting[bottomMargin])))//check group footer existed or not
		{
		$this->pageFooter();
		$this->pdf->AddPage();
		$this->background();
		$this->pageHeaderNewPage();
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	
		$tempY=$this->arraydetail[0][y_axis];
		}
		elseif(isset($this->arraypageFooter)&&(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>($this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin])))//check pagefooter existed or not
		{
		$this->pageFooter();
		$this->pdf->AddPage();
		$this->background();
		$headerY = $this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];
		$this->pageHeaderNewPage();
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	
		$tempY=$this->arraydetail[0][y_axis];
		}
		elseif(isset($this->arraylastPageFooter)&&(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>($this->arrayPageSetting[pageHeight]-$this->arraylastPageFooter[0][height]-$this->arrayPageSetting[bottomMargin])))//check lastpagefooter existed or not
		{
		$this->lastPageFooter();
		$this->pdf->AddPage();
		$this->background();
		$this->pageHeaderNewPage();
		$checkpoint=$this->arraydetail[0][y_axis];
		$biggestY=0;	$tempY=$this->arraydetail[0][y_axis];
		}
	
		if(($checkpoint+($compare[height]*($this->NbLines($compare[width],$txt))))>$tempY)
		{$tempY=$checkpoint+($compare[height]*($this->NbLines($compare[width],$txt)));}
		break;
		case "relativebottomline":
		break;
		case "report_count":
			$this->report_count++;
		break;
		default:
		$this->display($compare,$checkpoint);
		break;
		}
	}

	

	if($checkpoint+$this->arraydetail[0][height]>($this->arrayPageSetting[pageHeight]-$this->arraypageFooter[0][height]-$this->arrayPageSetting[bottomMargin]))	//check the upcoming band is greater than footer position or not
	{$this->pageFooter();
	$this->pdf->AddPage();
	$this->background();
	$headerY = $this->arrayPageSetting[topMargin]+$this->arraypageHeader[0][height];
	$this->pageHeaderNewPage();
	$checkpoint=$this->arraydetail[0][y_axis];
	$biggestY=0; $tempY=$this->arraydetail[0][y_axis];}

	foreach ($this->arraydetail as $out)
	{
		switch ($out[hidden_type])
		{
		case "field":
	
		$this->prepare_print_array=array("type"=>"MultiCell","width"=>$out[width],"height"=>$out[height],"txt"=>$out[txt],"border"=>$out[border],"align"=>$out[align],"fill"=>$out[fill],"hidden_type"=>$out[hidden_type],"printWhenExpression"=>$out[printWhenExpression],"soverflow"=>$out[soverflow],"poverflow"=>$out[poverflow],"link"=>$out[link],"pattern"=>$out[pattern]);
		$this->display($this->prepare_print_array,0,true);
		
		if($this->pdf->GetY() > $biggestY)
		{$biggestY = $this->pdf->GetY();}
		break;
		case "relativebottomline":
		//$this->relativebottomline($out,$tempY);
		$this->relativebottomline($out,$biggestY);	
		break;
		default:

		$this->display($out,$checkpoint);
		
		//$checkpoint=$this->pdf->GetY();
		break;		
		}
	}
	$this->pdf->SetY($biggestY);
	if($biggestY>$checkpoint+$this->arraydetail[0][height])
	{$checkpoint=$biggestY;}
	elseif($biggestY<$checkpoint+$this->arraydetail[0][height])
	{$checkpoint=$checkpoint+$this->arraydetail[0][height];}
	else{$checkpoint=$biggestY;}

	//if(isset($this->arraygroup)){$this->global_pointer++;}
	$this->global_pointer++;
}
}else{
	echo utf8_decode("Sorry cause there is not result from this query.");
	exit(0);
}
$this->global_pointer--;
if(isset($this->arraylastPageFooter))
{$this->lastPageFooter();}
else{$this->pageFooter();}
}


public function display($arraydata,$y_axis=0,$fielddata=false)
{   
	$arraydata[txt] = utf8_decode($arraydata[txt]);
	
	if($arraydata[type]=="SetFont")
	{
           if($arraydata[font]=='uGB')
            	$this->pdf->isUnicode=true;
           else
            $this->pdf->isUnicode=false;

            $this->pdf->SetFont($arraydata[font],$arraydata[fontstyle],$arraydata[fontsize]);

            }
	elseif($arraydata[type]=="MultiCell")
	{
          
		if($fielddata==false)
		{
		$this->checkoverflow($arraydata,$this->updatePageNo($arraydata[txt]));
		}
		elseif($fielddata==true)
		{
		$this->checkoverflow($arraydata,$this->updatePageNo($this->analyse_expression($arraydata[txt])));
		}
	}		
	elseif($arraydata[type]=="SetXY")
	{$this->pdf->SetXY($arraydata[x]+$this->arrayPageSetting[leftMargin],$arraydata[y]+$y_axis);}
	elseif($arraydata[type]=="Cell")
	{$this->pdf->Cell($arraydata[width],$arraydata[height],$this->updatePageNo($arraydata[txt]),$arraydata[border],$arraydata[ln],$arraydata[align],$arraydata[fill],$arraydata[link]);}
	elseif($arraydata[type]=="Rect")
	{$this->pdf->Rect($arraydata[x]+$this->arrayPageSetting[leftMargin],$arraydata[y]+$y_axis,$arraydata[width],$arraydata[height]);}
	elseif($arraydata[type]=="Image")
	{
	 $path=$this->analyse_expression($arraydata[path]);
	$imgtype=substr($path,-3);

	$this->pdf->Image($path,$arraydata[x]+$this->arrayPageSetting[leftMargin],$arraydata[y]+$y_axis,$arraydata[width],$arraydata[height],$imgtype,$arraydata[link]);}
	
	elseif($arraydata[type]=="SetTextColor")
	{$this->pdf->SetTextColor($arraydata[r],$arraydata[g],$arraydata[b]);}
	elseif($arraydata[type]=="SetDrawColor")
	{$this->pdf->SetDrawColor($arraydata[r],$arraydata[g],$arraydata[b]);}
	elseif($arraydata[type]=="SetLineWidth")
	{$this->pdf->SetLineWidth($arraydata[width]);}
	elseif($arraydata[type]=="Line")
	{$this->pdf->Line($arraydata[x1]+$this->arrayPageSetting[leftMargin],$arraydata[y1]+$y_axis,$arraydata[x2]+$this->arrayPageSetting[leftMargin],$arraydata[y2]+$y_axis);}

	elseif($arraydata[type]=="SetFillColor")
	{$this->pdf->SetFillColor($arraydata[r],$arraydata[g],$arraydata[b]);}	

}

public function relativebottomline($path,$y)
{
	$extra=$y-$path[y1];
	$this->display($path,$extra);
}

public function updatePageNo($s)
{
 return str_replace('$this->PageNo()', $this->pdf->PageNo(),$s);
}

public function staticText($xml_path)
{//$this->pointer[]=array("type"=>"SetXY","x"=>$xml_path->reportElement[x],"y"=>$xml_path->reportElement[y]);
}

public function checkoverflow($arraydata,$txt="")
{	$this->print_expression($arraydata);
    
	if($this->print_expression_result==true)
	{

        if($arraydata["writeHTML"]==1 && $this->pdflib=="TCPDF"){
            $this->pdf->writeHTML($txt);
        }
	elseif($arraydata[poverflow]=="true"&&$arraydata[soverflow]=="false")
	{$this->pdf->Cell($arraydata[width], $arraydata[height], $this->formatText($txt, $arraydata[pattern]),$arraydata[border],"",$arraydata[align],$arraydata[fill],$arraydata[link]);
	//$this->pdf->MultiCell($arraydata[width], $arraydata[height], $txt,$arraydata[border],$arraydata[align],$arraydata[fill]);
	}
	elseif($arraydata[poverflow]=="false"&&$arraydata[soverflow]=="false")
	{
		while($this->pdf->GetStringWidth($txt) > $arraydata[width])
		{
			$txt=substr_replace($txt,"",-1);
		}
		$this->pdf->Cell($arraydata[width], $arraydata[height],$this->formatText($txt, $arraydata[pattern]),$arraydata[border],"",$arraydata[align],$arraydata[fill],$arraydata[link]);
	}
	elseif($arraydata[poverflow]=="false"&&$arraydata[soverflow]=="true")
	{
		$this->pdf->MultiCell($arraydata[width], $arraydata[height], $this->formatText($txt, $arraydata[pattern]), $arraydata[border], $arraydata[align], $arraydata[fill]);
	}
	else
	{
		$this->pdf->MultiCell($arraydata[width], $arraydata[height], $this->formatText($txt, $arraydata[pattern]), $arraydata[border], $arraydata[align], $arraydata[fill]);
	}
	}
	$this->print_expression_result=false;
}

public function hex_code_color($value)
{	$r=hexdec(substr($value,1,2));
	$g=hexdec(substr($value,3,2));
	$b=hexdec(substr($value,5,2));
	return array("r"=>$r,"g"=>$g,"b"=>$b);
}

public function get_first_value($value)
{	
	return (substr($value,0,1));
}

function right($value, $count){

    return substr($value, ($count*-1));

}
function left($string, $count){
    return substr($string, 0, $count);
}
public function analyse_expression($data)
{       
        $arrdata=explode("+",$data);
        $i=0;
            foreach($arrdata as $num=>$out)
            {$i++;
		$arrdata["$num"]=str_replace('"',"",$out);
		if(substr($out,0,3)=='$F{')
		{$arrdata["$num"]=$this->arraysqltable[$this->global_pointer][substr($out,3,-1)];}
		elseif(substr($out,0,3)=='$V{')
		{$arrdata["$num"]=&$this->arrayVariable[substr($out,3,-1)]["ans"];}
		elseif(substr($out,0,3)=='$P{')
		{$arrdata["$num"]=$this->arrayParameter[substr($out,3,-1)];}
            }

    if($this->left($data,3)=='"("' && $this->right($data,3)=='")"'){
        $total=0;
        
       foreach($arrdata as $num=>$out)
       {  if($num>0 && $num<$i)
           $total+=$out;
//           echo "$num = $out to $total<br/>";
       }
        return $total;

        }
    else{
            
        	return implode($arrdata);
        }
}


public function formatText($txt,$pattern){
    if($pattern=="###0")
        return number_format($txt,0,"","");
    elseif($pattern=="#,##0")
        return number_format($txt,0,"",",");
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
    else
     return $txt;

    
}
public function print_expression($data)
{	
	$expression=$data[printWhenExpression];
	$expression=str_replace('$F{','$this->arraysqltable[$this->global_pointer][',$expression);
	$expression=str_replace('$P{','$this->arraysqltable[$this->global_pointer][',$expression);
	$expression=str_replace('$V{','$this->arraysqltable[$this->global_pointer][',$expression);
	$expression=str_replace('}',']',$expression);
	$this->print_expression_result=false;
	if($expression!="")
	{eval('if('.$expression.'){$this->print_expression_result=true;}');}
	elseif($expression=="")
	{$this->print_expression_result=true;}
	//echo 'if('.$expression.'){return true;}'."<br>";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function transferXMLtoArray($fileName) 
{ 
if(!file_exists($fileName)) 
echo "File - $fileName does not exist"; 
else 
{ 
$this->m=0; 
$xmlAry = $this->xmlobj2arr(simplexml_load_file($fileName)); 

 
foreach($xmlAry[header] as $key => $value) 
$this->arraysqltable["$this->m"]["$key"]=$value; 

foreach($xmlAry[detail][record]["$this->m"] as $key2 => $value2) 
$this->arraysqltable["$this->m"]["$key2"]=$value2; 
} 

if(isset($this->arrayVariable))	//if self define variable existing, go to do the calculation 
$this->variable_calculation($m); 

} 
//wrote by huzursuz at mailinator dot com on 02-Feb-2009 04:44 
//http://hk.php.net/manual/en/function.get-object-vars.php 
public function xmlobj2arr($Data) 
{ 
if (is_object($Data)) 
{ 
foreach (get_object_vars($Data) as $key => $val) 
$ret[$key] = $this->xmlobj2arr($val); 
return $ret; 
} 
elseif (is_array($Data)) 
{ 
foreach ($Data as $key => $val) 
$ret[$key] = $this->xmlobj2arr($val); 
return $ret; 
} 
else 
return $Data; 
} 
}
?>
