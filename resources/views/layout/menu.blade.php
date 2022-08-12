<?php  
    define('HOST', isset($_SERVER['HTTP_HOST']) === true ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_ADDR']) === true ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']));
    $domain_parts = explode('.', HOST); 
    $corTecsis = "style='color:rgb(243, 135, 42)'";

    if($domain_parts[2] != "tec"){$corTecsis = "";}
?>

<ul class="ieducar-sidebar-menu">
@foreach($menu as $item)
    @if($item->hasLinkInSubmenu())
    <li>
        <a href="{{ $item->link }}"><i class="fa {{$item->icon}}" <?=$corTecsis?> ></i> <span>{{$item->title}}</span></a>
    </li>
    @endif
@endforeach
</ul>

