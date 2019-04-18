<div class="flashMessages">
    <div class="flashMessages__container">
        @if ($message = Session::get('success'))
            <div class="flashMessages__message -success"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif

        @if ($message = Session::get('error'))
            <div class="flashMessages__message -error"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif

        @if ($message = Session::get('notice'))
            <div class="flashMessages__message -notice"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif

        @if ($message = Session::get('info'))
            <div class="flashMessages__message -info"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif

        @if ($message = Session::get('legacy'))
            <div class="flashMessages__message -legacy"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif

        @if (Session::has('errors'))
            @php
                $msgs = [];

                foreach (Session::get('errors')->all() as $error) {
                    $msgs[] = $error;
                }

                $message = join('<br>', $msgs);
            @endphp

            <div class="flashMessages__message -error"><a href="#" class="close-msg">×</a><time>{{ date('H:i:s', time()) }}</time>{!! $message !!}</div>
        @endif
    </div>
    <ul class="flashMessages__controls">
        <li><a href="#" data-action="showAll">mostrar todos (+<span>0</span>)</a></li><li><a href="#" data-action="closeAll">fechar todos</a></li>
    </ul>
</div>
