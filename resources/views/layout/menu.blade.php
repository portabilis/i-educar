<div id="ieducar-quick-search" class="ieducar-quick-search">
    <h4 class="ieducar-quick-search-title">Busca rápida</h4>
    <quick-search></quick-search>
</div>
<ul class="ieducar-sidebar-menu">
@foreach($menu as $item)
    @if($item->hasLinkInSubmenu())
    <li>
        <a href="{{ $item->link }}"><i class="fa {{$item->icon}}"></i> <span>{{$item->title}}</span></a>
    </li>
    @endif
@endforeach
</ul>
