@extends('admin.layouts.app')

@section('content')
    <section id="admin-control-panel">
        <div class="row">
            <div class="col s11 offset-s1">
                <div class="row">
                    <div class="col s12">
                        <nav>
                            <div class="nav-wrapper">
                                <ul id="nav-mobile" class="right hide-on-med-and-down">
                                    @if (!empty($response) && !empty($response['panel']))
                                        @foreach ($response['panel'] as $navbar)
                                            <li><a href=" {{ url('/qwentin/' . Request::segment(2) . '/' . Request::segment(3) . '/' . $navbar['alias']) }}">{{ $navbar['name'] }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @yield('content-body')
@endsection
