@if ($breadcrumb->pages())
    <div class="breadcrumb">
        <a href="{{ route('home') }}" title="Ir para o Início">
            <i class="fa fa-home" aria-hidden="true"></i> <span>Início</span>
        </a>

        @if($breadcrumb->hasPages())
            <a class="breadcrumb-separator"> / </a>
        @endif

        @foreach ($breadcrumb->pages() as $page)
            <a href="{{ $page->link }}" title="{{ $page->label }}">{{ $page->label }}</a>
            <a class="breadcrumb-separator"> / </a>
        @endforeach

        <span class="breadcrumb-current">{{ $breadcrumb->currentPage() }}</span>

    </div>
@elseif ($breadcrumb->getLegacy())
    {!! $breadcrumb->getLegacy() !!}
@endif
