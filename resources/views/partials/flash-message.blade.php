<div class="flashMessages">
    <div class="flashMessages__container">
        @foreach (['success', 'error', 'notice', 'info', 'legacy'] as $msgType)
            @if (Session::get($msgType))
                @if (is_array(Session::get($msgType)))
                    @foreach (Session::get($msgType) as $message)
                        <div class="flashMessages__message -{{ $msgType }}"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
                    @endforeach
                @else
                    <div class="flashMessages__message -{{ $msgType }}"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! Session::get($msgType) !!}</div>
                @endif
            @endif
        @endforeach

        @if (Session::has('errors'))
            @foreach (Session::get('errors')->all() as $message)
                <div class="flashMessages__message -error"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
            @endforeach
        @endif
    </div>
    <ul class="flashMessages__controls">
        <li><a href="#" data-action="showAll">mostrar todos (+<span>0</span>)</a></li><li><a href="#" data-action="closeAll">fechar todos</a></li>
    </ul>
</div>
