<?php  
    $baseTecsis = $_SERVER['SERVER_NAME'];
    $corTecsis = "style='color:rgb(243, 135, 42)'";
?>

<div id="ieducar-quick-search" class="ieducar-quick-search">
    <h4 class="ieducar-quick-search-title">Busca rápida</h4>
    <quick-search></quick-search>
</div>
<ul class="ieducar-sidebar-menu">
@foreach($menu as $item)
    @if($item->hasLinkInSubmenu())
    <li>
        <a href="{{ $item->link }}"><i class="fa {{$item->icon}}" id="iconID"
            <?php // Icones se adaptam a cor da tecsis
                if($baseTecsis == "tecsis.tec.br"){echo $corTecsis;}?> 
                ></i> <span>{{$item->title}}</span></a>
    </li>
    @endif
@endforeach
</ul>

