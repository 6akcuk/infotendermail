<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span></span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs"><strong class="font-bold">{{ Auth::user()->name }}</strong></span>
                            <span class="text-muted text-xs block">Администратор <b class="caret"></b></span>
                        </span>
                    </a>
                </div>
            </li>
            @foreach (config('menu.sidebar') as $top)
            <li @if (call_user_func_array(array(app('request'), 'is'), $top['match'])) class="active" @endif>
                <a href="#">
                    <i class="fa {{ $top['icon'] }}"></i>
                    <span class="nav-label">{{ $top['label'] }}</span>
                    @if ($top['items']) <span class="fa arrow"></span> @endif
                </a>
                @if ($top['items'])
                <ul class="nav nav-second-level collapse">
                    @foreach ($top['items'] as $item)
                    <li @if (Request::is($item['match'])) class="active" @endif>
                        <a href="@if ($item['route']) {{ route($item['route']) }} @endif">{{ $item['label'] }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
</nav>