
var bstylesNames=["Individual Style","Individual Style","Individual Style","Font",];

//--- Common
var bblankImage="imagens/img/blank.gif";
var bitemCursor="pointer";
var bmenuOrientation=0;
var bselectedItem=0;

//--- Dimensions
var bmenuWidth="100%";
var bmenuHeight="30px";

//--- Positioning
var babsolute=0;
var bleft="120px";
var btop="120px";

//--- Font
var bfontStyle=["bold 8pt Tahoma","",""];
var bfontColor=["#333333","#333333","#000000"];
var bfontDecoration=["none","underline","none"];

//--- Tab-mode
var tabMode=0;
var bselectedSmItem=-1;
var bsmHeight=10;
var bsmBackColor="#FFFFFF";
var bsmBorderColor="#91A7B4";
var bsmBorderWidth=1;
var bsmBorderStyle="solid";
var bsmBorderBottomDraw=1;
var bitemTarget="_blank";
var bsmItemAlign="center";
var bsmItemSpacing=1;
var bsmItemPadding="0px";

//--- Appearance
var bmenuBackColor="";
var bmenuBackImage="";
var bmenuBorderColor="";
var bmenuBorderWidth=0;
var bmenuBorderStyle="ridge";

//--- Tabs Appearance
var bbeforeItemSpace=0;
var bafterItemSpace=0;
var bitemBackColor=["#FFFFFF","#FFFFFF","#FFFFFF"];
var bitemBorderColor=["","",""];
var bitemBorderWidth=0;
var bitemBorderStyle=["ridge","ridge","ridge"];
var bitemAlign="center";
var bitemSpacing=0;
var bitemPadding="0px";
var browSpace=0;

//--- Tabs Images
var bitemBackImage=["imagens/img/styler_n_back.gif","imagens/img/styler_n_back.gif","imagens/img/styler_s_back.gif"];
var bbeforeItemImage=["","",""];
var bafterItemImage=["","",""];
var bbeforeItemImageW=20;
var bbeforeItemImageH=30;
var bafterItemImageW=5;
var bafterItemImageH=30;

//--- Icons
var biconWidth=16;
var biconHeight=16;
var biconAlign="left";
var texpandBtn=["","",""];
var texpandBtnW=9;
var texpandBtnH=9;
var texpandBtnAlign="left";

//--- Separators
var bseparatorWidth="7px";

//--- Transitional Effects
var btransition=24;
var btransOptions="";
var btransDuration=300;

//--- Floatable Menu
var bfloatable=1;
var bfloatIterations=6;

var bstyles = [
    ["bitemWidth=24px","bitemBackImageSpec=imagens/img/styler_nn_center.gif,imagens/img/styler_nn_center.gif,imagens/img/styler_ns_center.gif,imagens/img/styler_nn_center.gif,imagens/img/styler_ns_center.gif,imagens/img/styler_sn_center.gif,imagens/img/styler_sn_center.gif"],
    ["bbeforeItemImage=imagens/img/styler_n_left.gif,imagens/img/styler_n_left.gif,imagens/img/styler_s_left.gif"],
    ["bafterItemImage=imagens/img/styler_n_right.gif,imagens/img/styler_n_right.gif,imagens/img/styler_s_right.gif"],
    ["bfontColor=#FFFFFF,#FFFFFF,#013572","bfontDecoration=none,underline,none"],
];

var bmenuItems = [

<?php
$desabilitado_tab = $_GET['desabilitado_tab'];
$desabilitado_tab = unserialize(stripslashes(urldecode($desabilitado_tab)));

$nomes_tab = $_GET['nomes_tab'];
$nomes_tab = unserialize(stripslashes(urldecode($nomes_tab)));
    foreach ($nomes_tab as $key => $tab)
    {
        $desabilitado = $desabilitado_tab[$key] ? 1 : 0;
        $key = $key+1;
        //   titulo da tab - conteudo que mostra
        // ["Style Name", "div1", "myicon1.gif", "myicon2.gif", "myicon3.gif", "Home Page Tip", "1"],  
        echo "[\"{$tab}\",\"content{$key}\", \"\", \"\", \"\", \"\", \"1\", \"$desabilitado\", \"\", ],\n";
        if($key < count($nomes_tab) )
            echo "[\"-\",\"\", \"\", \"\", \"\", \"\", \"0\", \"\", \"\", ],\n";
    }

?>

];

dtabs_init();
