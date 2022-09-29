@if(isset($mainmenu) && isset($menu))
    @php
    $topmenu = $menu->where('id', $mainmenu)->first();
    @endphp
    <div class="ieducar-menu-container">
    @if($topmenu->children && $topmenu->children->count())
        @if($topmenu->hasLinkInSubmenu())
        <ul class="ieducar-menu clearfix">
        @foreach($topmenu->children->sortBy('order') as $submenu)
            @if($submenu->hasLink())
            <li>
                <a href="javascript:void(0)">{{ $submenu->title }}</a>
                @if($submenu->hasLinkInSubmenu())
                <ul class="ieducar-sub-menu clearfix">
                @foreach($submenu->children->sortBy('order') as $c1)
                    @if($c1->hasLink())
                    <li>
                        <a href="{{ $c1->link ?? 'javascript:void(0)' }}">{{ $c1->title }}</a>
                        @if($c1->hasLinkInSubmenu())
                            <ul class="ieducar-sub-menu">
                            @foreach($c1->children->sortBy('order') as $c2)
                                @if($c2->hasLink())
                                <li>
                                    <a href="{{ $c2->link ?? 'javascript:void(0)' }}">{{ $c2->title }}</a>
                                    @if($c2->hasLinkInSubmenu())
                                        <ul class="ieducar-sub-menu">
                                        @foreach($c2->children->sortBy('order') as $c3)
                                            @if($c3->isLink())
                                            <li><a href="{{ $c3->link ?? 'javascript:void(0)' }}">{{ $c3->title }}</a></li>
                                            @endif
                                        @endforeach
                                        </ul>
                                    @endif
                                </li>
                                @endif
                            @endforeach
                            </ul>
                        @endif
                    </li>
                    @endif
                @endforeach
                </ul>
                @endif
            </li>
            @endif
        @endforeach
        </ul>
        @endif
    @endif
    </div>
@endif
