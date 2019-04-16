
    <table summary='' cellspacing='0' class='nvp_tabelaMenu'>
        <tr>
            <td>
                <link rel=stylesheet type='text/css'
                      href='{{ Asset::get('/intranet/styles/buscaMenu.css') }}'/>
                <link rel=stylesheet type='text/css'
                      href='{{ Asset::get('/intranet/scripts/jquery/jquery-ui.min-1.9.2/css/custom/jquery-ui-1.9.2.custom.min.css') }}'/>
                <script type='text/javascript'
                        src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js') }}'></script>
                <script type='text/javascript'
                        src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Utils.js') }}'></script>
                <script type='text/javascript'
                        src='{{ Asset::get('/intranet/scripts/buscaMenu.js') }}'></script>
                <script type='text/javascript'
                        src='{{ Asset::get('/intranet/scripts/jquery/jquery-ui.min-1.9.2/js/jquery-ui-1.9.2.custom.min.js') }}'></script>
                <div title='Busca rápida' class='title-busca-rapida'>
                    <table width='168' class='title active-section-title'
                           style='-moz-user-select: none;'>
                        <tbody style='-moz-user-select: none;'>
                        <tr style='-moz-user-select: none;'>
                            <td style='-moz-user-select: none;'><a
                                    style='outline:none;text-decoration:none;'>Busca rápida</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <ul class='menu'>
                    <li id='busca-menu'><input class='geral ui-autocomplete-input' type='text'
                                               name='menu' id='busca-menu-input' size=50
                                               maxlength=50 placeholder='Informe o nome do menu'
                                               autocomplete=off></li>
                </ul>

                <ul class="ieducar-sidebar-menu">
                @foreach($menu as $item)
                    <li>
                        <a href="{{ $item->link }}"><i class="fa {{$item->icon}}"></i> <span>{{$item->title}}</span></a>
                    </li>
                @endforeach
                </ul>
                <style>
                    .ieducar-sidebar-menu {
                        padding: 0;
                        margin: 0;
                    }
                    .ieducar-sidebar-menu li {
                        padding: 0;
                        margin: 0;
                        list-style: none;
                    }
                    .ieducar-sidebar-menu li a {
                        display: block;
                        color: #47728f;
                        font-family: "Open Sans", sans-serif;
                        font-size: 16px;
                        padding: 17px 15px;
                        margin: 0 10px;
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
                        border-radius: 3px;
                    }
                    .ieducar-sidebar-menu li a:hover {
                        background: #cddce6;
                        text-decoration: none;
                    }
                    .ieducar-sidebar-menu li a i {
                        color: #98b0c3;
                        font-size: 20px;
                        padding-right: 10px;
                    }
                </style>
            </td>
        </tr>
    </table>
</td>
