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
            <div id='div_68' style="display:none;">
                <ul class="menu"></ul>
            </div>
        </div>
    </a>
@endforeach