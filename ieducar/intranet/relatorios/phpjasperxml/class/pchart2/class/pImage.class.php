<?php
 /*
     pDraw - pChart core class

     Version     : 2.1.1
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 28/03/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 /* The GD extension is mandatory */
 if (!extension_loaded('gd') && !extension_loaded('gd2'))
  {
   echo "GD extension must be loaded. \r\n";
   exit();
  }

 class pImage extends pDraw
  {
   /* Image settings, size, quality, .. */
   var $XSize             = NULL;			// Width of the picture
   var $YSize             = NULL;			// Height of the picture
   var $Picture           = NULL;			// GD picture object
   var $Antialias         = TRUE;			// Turn antialias on or off
   var $AntialiasQuality  = 0;				// Quality of the antialiasing implementation (0-1)
   var $Mask              = "";				// Already drawn pixels mask (Filled circle implementation)
   var $TransparentBackground = FALSE;			// Just to know if we need to flush the alpha channels when rendering

   /* Graph area settings */
   var $GraphAreaX1       = NULL;			// Graph area X origin
   var $GraphAreaY1       = NULL;			// Graph area Y origin
   var $GraphAreaX2       = NULL;			// Graph area bottom right X position
   var $GraphAreaY2       = NULL;			// Graph area bottom right Y position

   /* Scale settings */
   var $ScaleMinDivHeight = 20;				// Minimum height for scame divs

   /* Font properties */
   var $FontName          = "fonts/GeosansLight.ttf";	// Default font file
   var $FontSize          = 12;				// Default font size
   var $FontBox           = NULL;			// Return the bounding box of the last written string
   var $FontColorR        = 0;				// Default color settings
   var $FontColorG        = 0;				// Default color settings
   var $FontColorB        = 0;				// Default color settings
   var $FontColorA        = 100;			// Default transparency

   /* Shadow properties */
   var $Shadow            = TRUE;			// Turn shadows on or off
   var $ShadowX           = NULL;			// X Offset of the shadow
   var $ShadowY           = NULL;			// Y Offset of the shadow
   var $ShadowR           = NULL;			// R component of the shadow
   var $ShadowG           = NULL;			// G component of the shadow
   var $ShadowB           = NULL;			// B component of the shadow
   var $Shadowa           = NULL;			// Alpha level of the shadow

   /* Data Set */
   var $DataSet           = NULL;			// Attached dataset

   /* Class constructor */
   function pImage($XSize,$YSize,$DataSet=NULL,$TransparentBackground=FALSE)
    {
     $this->TransparentBackground = $TransparentBackground;

     if ( $DataSet != NULL ) { $this->DataSet = $DataSet; }

     $this->XSize   = $XSize;
     $this->YSize   = $YSize;
     $this->Picture = imagecreatetruecolor($XSize,$YSize);

     if ( $this->TransparentBackground )
      {
       imagealphablending($this->Picture,FALSE);
       imagefilledrectangle($this->Picture, 0,0,$XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 127));
       imagealphablending($this->Picture,TRUE);
       imagesavealpha($this->Picture,true); 
      }
     else
      {
       $C_White = $this->AllocateColor($this->Picture,255,255,255);
       imagefilledrectangle($this->Picture,0,0,$XSize,$YSize,$C_White);
      }
    }

   /* Enable / Disable and set shadow properties */
   function setShadow($Enabled=TRUE,$Format="")
    {
     $X	    = isset($Format["X"]) ? $Format["X"] : 2;
     $Y	    = isset($Format["Y"]) ? $Format["Y"] : 2;
     $R	    = isset($Format["R"]) ? $Format["R"] : 0;
     $G	    = isset($Format["G"]) ? $Format["G"] : 0;
     $B	    = isset($Format["B"]) ? $Format["B"] : 0;
     $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 10;

     $this->Shadow  = $Enabled;
     $this->ShadowX = $X;
     $this->ShadowY = $Y;
     $this->ShadowR = $R;
     $this->ShadowG = $G;
     $this->ShadowB = $B;
     $this->Shadowa = $Alpha;
    }

   /* Set the graph area position */
   function setGraphArea($X1,$Y1,$X2,$Y2)
    {
     if ( $X2 < $X1 || $X1 == $X2 || $Y2 < $Y1 || $Y1 == $Y2 ) { return(-1); }

     $this->GraphAreaX1 = $X1; $this->DataSet->Data["GraphArea"]["X1"] = $X1;
     $this->GraphAreaY1 = $Y1; $this->DataSet->Data["GraphArea"]["Y1"] = $Y1;
     $this->GraphAreaX2 = $X2; $this->DataSet->Data["GraphArea"]["X2"] = $X2;
     $this->GraphAreaY2 = $Y2; $this->DataSet->Data["GraphArea"]["Y2"] = $Y2;
    }

   /* Return the width of the picture */
   function getWidth()
    { return($this->XSize); }

   /* Return the heigth of the picture */
   function getHeight()
    { return($this->YSize); }

   /* Render the picture to a file */
   function render($FileName)
    {
     if ( $this->TransparentBackground ) { imagealphablending($this->Picture,false); imagesavealpha($this->Picture,true); }
     imagepng($this->Picture,$FileName);
    }

   /* Render the picture to a web browser stream */
   function stroke()
    {
     if ( $this->TransparentBackground ) { imagealphablending($this->Picture,false); imagesavealpha($this->Picture,true); }

     header('Content-type: image/png');
     imagepng($this->Picture);
    }

   /* Automatic output method based on the calling interface */
   function autoOutput($FileName="output.png")
    {
     if (php_sapi_name() == "cli")
      $this->Render($FileName);
     else
      $this->Stroke();
    }

   /* Return the length between two points */
   function getLength($X1,$Y1,$X2,$Y2)
    { return(sqrt(pow(max($X1,$X2)-min($X1,$X2),2)+pow(max($Y1,$Y2)-min($Y1,$Y2),2))); }

   /* Return the orientation of a line */
   function getAngle($X1,$Y1,$X2,$Y2)
    {
     $Opposite = $Y2 - $Y1; $Adjacent = $X2 - $X1;$Angle = rad2deg(atan2($Opposite,$Adjacent));
     if ($Angle > 0) { return($Angle); } else { return(360-abs($Angle)); }
    }

   /* Return the surrounding box of text area */
   function getTextBox_deprecated($X,$Y,$FontName,$FontSize,$Angle,$Text)
    {
     $Size    = imagettfbbox($FontSize,$Angle,$FontName,$Text);
     $Width   = $this->getLength($Size[0],$Size[1],$Size[2],$Size[3])+1;
     $Height  = $this->getLength($Size[2],$Size[3],$Size[4],$Size[5])+1;

     $RealPos[0]["X"] = $X; $RealPos[0]["Y"] = $Y;
     $RealPos[1]["X"] = cos((360-$Angle)*PI/180)*$Width + $RealPos[0]["X"]; $RealPos[1]["Y"] = sin((360-$Angle)*PI/180)*$Width + $RealPos[0]["Y"];
     $RealPos[2]["X"] = cos((270-$Angle)*PI/180)*$Height + $RealPos[1]["X"]; $RealPos[2]["Y"] = sin((270-$Angle)*PI/180)*$Height + $RealPos[1]["Y"];
     $RealPos[3]["X"] = cos((180-$Angle)*PI/180)*$Width + $RealPos[2]["X"]; $RealPos[3]["Y"] = sin((180-$Angle)*PI/180)*$Width + $RealPos[2]["Y"];

     $RealPos[TEXT_ALIGN_BOTTOMLEFT]["X"] = $RealPos[0]["X"];	$RealPos[TEXT_ALIGN_BOTTOMLEFT]["Y"] = $RealPos[0]["Y"];
     $RealPos[TEXT_ALIGN_BOTTOMRIGHT]["X"] = $RealPos[1]["X"];	$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["Y"] = $RealPos[1]["Y"];

     return($RealPos);
    }

   /* Return the surrounding box of text area */
   function getTextBox($X,$Y,$FontName,$FontSize,$Angle,$Text)
    {
     $coords = imagettfbbox($FontSize, 0, $FontName, $Text);

     $a = deg2rad($Angle); $ca = cos($a); $sa = sin($a); $RealPos = array();
     for($i = 0; $i < 7; $i += 2)
      {
       $RealPos[$i/2]["X"] = $X + round($coords[$i] * $ca + $coords[$i+1] * $sa);
       $RealPos[$i/2]["Y"] = $Y + round($coords[$i+1] * $ca - $coords[$i] * $sa);
      }

     $RealPos[TEXT_ALIGN_BOTTOMLEFT]["X"]	= $RealPos[0]["X"];	$RealPos[TEXT_ALIGN_BOTTOMLEFT]["Y"]	= $RealPos[0]["Y"];
     $RealPos[TEXT_ALIGN_BOTTOMRIGHT]["X"]	= $RealPos[1]["X"];	$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["Y"]	= $RealPos[1]["Y"];
     $RealPos[TEXT_ALIGN_TOPLEFT]["X"]		= $RealPos[3]["X"];	$RealPos[TEXT_ALIGN_TOPLEFT]["Y"]	= $RealPos[3]["Y"];
     $RealPos[TEXT_ALIGN_TOPRIGHT]["X"]		= $RealPos[2]["X"];	$RealPos[TEXT_ALIGN_TOPRIGHT]["Y"]	= $RealPos[2]["Y"];
     $RealPos[TEXT_ALIGN_BOTTOMMIDDLE]["X"]	= ($RealPos[1]["X"]-$RealPos[0]["X"])/2+$RealPos[0]["X"];	$RealPos[TEXT_ALIGN_BOTTOMMIDDLE]["Y"]	= ($RealPos[0]["Y"]-$RealPos[1]["Y"])/2+$RealPos[1]["Y"];
     $RealPos[TEXT_ALIGN_TOPMIDDLE]["X"]	= ($RealPos[2]["X"]-$RealPos[3]["X"])/2+$RealPos[3]["X"];	$RealPos[TEXT_ALIGN_TOPMIDDLE]["Y"]	= ($RealPos[3]["Y"]-$RealPos[2]["Y"])/2+$RealPos[2]["Y"];
     $RealPos[TEXT_ALIGN_MIDDLELEFT]["X"]	= ($RealPos[0]["X"]-$RealPos[3]["X"])/2+$RealPos[3]["X"];	$RealPos[TEXT_ALIGN_MIDDLELEFT]["Y"]	= ($RealPos[0]["Y"]-$RealPos[3]["Y"])/2+$RealPos[3]["Y"];
     $RealPos[TEXT_ALIGN_MIDDLERIGHT]["X"]	= ($RealPos[1]["X"]-$RealPos[2]["X"])/2+$RealPos[2]["X"];	$RealPos[TEXT_ALIGN_MIDDLERIGHT]["Y"]	= ($RealPos[1]["Y"]-$RealPos[2]["Y"])/2+$RealPos[2]["Y"];
     $RealPos[TEXT_ALIGN_MIDDLEMIDDLE]["X"]	= ($RealPos[1]["X"]-$RealPos[3]["X"])/2+$RealPos[3]["X"];	$RealPos[TEXT_ALIGN_MIDDLEMIDDLE]["Y"]	= ($RealPos[0]["Y"]-$RealPos[2]["Y"])/2+$RealPos[2]["Y"];

     return($RealPos);
    }

   /* Set current font properties */
   function setFontProperties($Format="")
    {
     $R		= isset($Format["R"]) ? $Format["R"] : -1;
     $G		= isset($Format["G"]) ? $Format["G"] : -1;
     $B		= isset($Format["B"]) ? $Format["B"] : -1;
     $Alpha	= isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
     $FontName	= isset($Format["FontName"]) ? $Format["FontName"] : NULL;
     $FontSize	= isset($Format["FontSize"]) ? $Format["FontSize"] : NULL;

     if ( $R != -1)       {  $this->FontColorR = $R; }
     if ( $G != -1)       {  $this->FontColorG = $G; }
     if ( $B != -1)       {  $this->FontColorB = $B; }
     if ( $Alpha != NULL) {  $this->FontColorA = $Alpha; }

     if ( $FontName != NULL  )
      $this->FontName = $FontName;
 
     if ( $FontSize != NULL  )
      $this->FontSize = $FontSize;
    }

   /* Returns the 1st decimal values (used to correct AA bugs) */
   function getFirstDecimal($Value)
    {
     $Values = preg_split("/\./",$Value);
     if ( isset($Values[1]) ) { return(substr($Values[1],0,1)); } else { return(0); }
    }

   /* Attach a dataset to your pChart Object */
   function setDataSet(&$DataSet)
    { $this->DataSet = $DataSet; }

   /* Print attached dataset contents to STDOUT */
   function printDataSet()
    { print_r($this->DataSet); }
  }
?>