<script type="text/javascript">
    array_menu = [];
    array_id = [];

    @foreach ($topmenu->getTopMenuArray(session('id_pessoa')) as $item)
        array_menu[array_menu.length] = ['{{$item['tt_menu']}} ', {{$item['cod_menu']}},'{{$item['ref_cod_menu_pai']}}','', 'null', '{{$item['caminho']}}', '{{$item['alvo']}}'];

    @if(!$item['ref_cod_menu_pai'])
        array_id[array_id.length] = {{$item['cod_menu']}};
    @endif
    @endforeach
</script>
<script type="text/javascript">
    setTimeout("setXY();", 150);
    MontaMenu();
</script>
