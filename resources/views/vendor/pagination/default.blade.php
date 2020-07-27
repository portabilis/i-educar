<div style="height: 30px;"></div>

<table style="width: 100%">
  <tr>
    <td style="text-align: center">Total de registros: {{ $paginator->total() }}</td>
  </tr>
</table>

<table class="paginacao" border="0" cellpadding="0" cellspacing="0" align="center">
    <tbody>
    <tr>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <td width="23" align="center" class="disabled">
                <a href="#" class="nvp_paginador" title="Ir para a página anterior"> ‹ </a>
            </td>
        @else
            <td width="23" align="center">
                <a href="{{ $paginator->previousPageUrl() }}" class="nvp_paginador"
                   title="Ir para a página anterior"> ‹ </a>
            </td>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <td align="center" style="padding-left:5px;padding-right:5px;" class="disabled">
                    <a href="#" class="nvp_paginador">
                        {{ $element }}
                    </a>
                </td>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage() && $paginator->hasPages())
                        <td align="center" style="padding-left:5px;padding-right:5px;" class="disabled">
                            <a href="#" class="nvp_paginador">
                                {{ $page }}
                            </a>
                        </td>
                    @else
                        <td align="center" style="padding-left:5px;padding-right:5px;">
                            <a href="{{ $url }}" class="nvp_paginador" title="Ir para a página {{ $page }}">
                                {{ $page }}
                            </a>
                        </td>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <td width="23" align="center">
                <a href="{{ $paginator->nextPageUrl() }}" class="nvp_paginador" title="Ir para a próxima pagina">
                    &rsaquo; </a>
            </td>
        @else
            <td width="23" align="center" class="disabled">
                <a href="#" class="nvp_paginador" title="Ir para a próxima pagina"> &rsaquo; </a>
            </td>
        @endif
    </tr>
    </tbody>
</table>

