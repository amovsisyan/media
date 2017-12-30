@if (!empty($response))
    @foreach ($response['navbar'] as $navbar)
        @if(!empty($navbar['subcategory']))
            <ul id="{{$navbar['category']['alias']}}" class="dropdown-content">
                @foreach ($navbar['subcategory'] as $subcat)
                    <li>
                        <a href="{{ url('/' . Request::segment(1) . '/' . $navbar['category']['alias'] . '/' . $subcat['alias'])}}">
                            {{$subcat['name']}}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endforeach
@endif

@if (Auth::guest())
    @if (false)
        {{--delete false when you need user login--}}
        <ul id="guest-login" class="dropdown-content">
            <li><a href="{{ url("/login") }}">Login</a></li>
            <li><a href="{{ url("/register") }}">Register</a></li>
        </ul>
    @endif
@else
    <ul id="loged-logout" class="dropdown-content">
        <li>
            <a href="{{ url('/logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                  style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
@endif

<nav>
    <div class="nav-wrapper">
        <a href="{{ url('/' . $locale)}}" class="brand-logo left_5">logo</a>
        <ul class="right margin_5">
            @if (!empty($response))
                @foreach ($response['navbar'] as $navbar)
                    @if(!empty($navbar['subcategory']))
                        <li>
                            <a class="dropdown-button" href="" data-activates="{{$navbar['category']['alias']}}">{{$navbar['category']['name']}}
                                <i class="material-icons right">arrow_drop_down</i>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
            @if (Auth::guest())
                @if (false)
                    {{--delete false when you need user login--}}
                    <li>
                        <a class="dropdown-button" href="" data-activates="guest-login">
                            Login<i class="material-icons right">arrow_drop_down</i>
                        </a>
                    </li>
                @endif
            @else
                <li>
                    <a class="dropdown-button" href="" data-activates="loged-logout">
                        {{ Auth::user()->name }}<i class="material-icons right">arrow_drop_down</i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>

@if(!empty($response['navbar']['activeLocales']) && count($response['navbar']['activeLocales']) > 1)
    <div id="locale-dropdown" class="fixed-action-btn horizontal click-to-toggle">
        <a class="btn-floating">
            <img src="/img/flags/{{$locale}}.svg" alt="">
        </a>
        <ul>
            @foreach ($response['navbar']['activeLocales'] as $localeLang)
                <li data-localename="{{$localeLang['name']}}">
                    <a class="btn-floating locale-btn">
                        <img src="/img/flags/{{$localeLang['name']}}.svg" alt="" data-locale="{{$localeLang['name']}}">
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif