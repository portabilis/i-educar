@if(isset($topmenu))
    <div class="ieducar-menu-container">
    @if($topmenu->children && $topmenu->children->count())
        <ul class="ieducar-menu clearfix">
        @foreach($topmenu->children->sortBy('order') as $submenu)
            <li>
                <a href="javascript:void(0)">{{ $submenu->title }}</a>
                @if($submenu->children && $submenu->children->count())
                <ul class="ieducar-sub-menu clearfix">
                @foreach($submenu->children->sortBy('order') as $c1)
                    <li>
                        <a href="{{ $c1->link ?? 'javascript:void(0)' }}">{{ $c1->title }}</a>
                        @if($c1->children && $c1->children->count())
                            <ul class="ieducar-sub-menu">
                            @foreach($c1->children->sortBy('order') as $c2)
                                <li>
                                    <a href="{{ $c2->link ?? 'javascript:void(0)' }}">{{ $c2->title }}</a>
                                    @if($c2->children && $c2->children->count())
                                        <ul class="ieducar-sub-menu">
                                        @foreach($c2->children->sortBy('order') as $c3)
                                            <li><a href="{{ $c3->link ?? 'javascript:void(0)' }}">{{ $c3->title }}</a></li>
                                        @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
                </ul>
                @endif
            </li>
        @endforeach
        </ul>
    @endif
    </div>
    <style>
        body,
        .ieducar-menu,
        .ieducar-sub-menu {
            margin: 0;
            padding: 0;
        }
        .clearfix:after{
            content: '.';
            display: block;
            clear: both;
            height: 0;
            line-height: 0;
            font-size: 0;
            visibility: hidden;
            overflow: hidden;
        }
        .ieducar-menu,
        .ieducar-sub-menu {
            list-style: none;
            background: #e9eff7;
        }
        .ieducar-sub-menu {
            background: #FFF;
            border: 1px solid #f3f3f3;
        }
        .ieducar-menu a {
            text-decoration: none;
            display: block;
            padding: 10px;
            color: #47728f;
            font-family: "Open Sans", sans-serif;
            font-size: 16px;
        }
        .ieducar-menu li {
            position: relative;
        }
        .ieducar-menu > li {
            float: left;
        }
        .ieducar-menu > li:hover {
            background: #cddce6;
        }
        .ieducar-menu li:hover > .ieducar-sub-menu {
            display: block;
        }
        .ieducar-sub-menu a {
            font-size: 14px;
        }
        .ieducar-sub-menu {
            display: none;
            position: absolute;
            min-width: 220px;
        }
        .ieducar-sub-menu li a {
            color: #646b71;
            padding: 5px 10px;
        }
        .ieducar-sub-menu li + li {
            border-top: 1px solid #f3f3f3;
        }
        .ieducar-sub-menu li:hover {
            background: #cddce6;
        }

        .ieducar-sub-menu .ieducar-sub-menu {
            top: -1px;
            left: 100%;
        }
    </style>
@endif
