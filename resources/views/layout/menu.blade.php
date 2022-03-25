<?php  
    $baseTecsis = $_SERVER['HTTP_HOST'];
    $corTecsis = "style='color:rgb(243, 135, 42)'";

    if($baseTecsis != "tecsis.tec.br"){$corTecsis = "";}
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

