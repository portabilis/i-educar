@if ($breadcrumb->pages())
    <div id="localizacao">
        <a href="{{ route('home') }}" title="Ir para o Início">
            <i class="fa fa-home" aria-hidden="true"></i><span>Início</span>
        </a>

        @if($breadcrumb->hasPages())
            <a class="flechinha"> / </a>
        @endif

        @foreach ($breadcrumb->pages() as $page)
            <a href="{{ $page->link }}" title="{{ $page->label }}">{{ $page->label }}</a>
            <a class="flechinha"> / </a>
        @endforeach

        <span class="pagina_atual">{{ $breadcrumb->currentPage() }}</span>

    </div>
@elseif ($breadcrumb->getLegacy())
    {!! $breadcrumb->getLegacy() !!}
@endif
