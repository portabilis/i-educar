
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

                @foreach($menu as $itemMenu)
                    <a href="{{$itemMenu->caminho}}"
                       style="outline:none; text-decoration: none;">
                        <div>
                            <table width="168" class="title active-section-title"
                                   style="-moz-user-select: none;">
                                <tbody style="-moz-user-select: none;">
                                <tr style="-moz-user-select: none;">
                                    <td style="-moz-user-select: none;"><a
                                            href="{{$itemMenu->caminho}}"
                                            id='link1_68' style="outline:none">
                                            <div id="fa-icons"><i class="fa {{$itemMenu->icon_class}}"
                                                                  aria-hidden="true"></i></div>
                                            {{$itemMenu->nm_menu}}</a></td>
                                </tr>
                                </tbody>
                            </table>
                            <div id='div_{{$itemMenu->ref_cod_menu_pai}}' style="display:none;">
                                <ul class="menu"></ul>
                            </div>
                        </div>
                    </a>
                @endforeach
            </td>
        </tr>
    </table>
</td>
