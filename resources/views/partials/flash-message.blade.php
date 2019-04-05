<div class="flashMessages">
    <div class="flashMessages__container">
        @if ($message = Session::get('success'))
            <div title="Clique para fechar" class="flashMessages__message -success"><time>{{ date('H:i:s', time()) }}</time>{{ $message }}</div>
        @endif

        @if ($message = Session::get('error'))
            <div title="Clique para fechar" class="flashMessages__message -error"><time>{{ date('H:i:s', time()) }}</time>{{ $message }}</div>
        @endif

        @if ($message = Session::get('notice'))
            <div title="Clique para fechar" class="flashMessages__message -notice"><time>{{ date('H:i:s', time()) }}</time>{{ $message }}</div>
        @endif

        @if ($message = Session::get('info'))
            <div title="Clique para fechar" class="flashMessages__message -info"><time>{{ date('H:i:s', time()) }}</time>{{ $message }}</div>
        @endif
    </div>
    <ul class="flashMessages__controls">
        <li><a href="#" data-action="showAll">mostrar todos (+<span>0</span>)</a></li><li><a href="#" data-action="closeAll">fechar todos</a></li>
    </ul>
</div>
