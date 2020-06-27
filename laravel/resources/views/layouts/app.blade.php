<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @section('scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
    @show

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-success shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @include('layouts.parts.menu')
                    {{--  <ul class="navbar-nav mr-auto">
                        @can('MNG')
                        <li class='nav-item'><a href="{{ route('manage.work_type') }}" class='nav-link'>職種設定</a></li>
                        <li class='nav-item'><a href="{{ route('manage.group') }}" class='nav-link'>グループ設定</a></li>
                        @elsecan('G-MNG')
                        <li class='nav-item'><a href="{{ route('g-manage.user')}}" class='nav-link'>ユーザ設定</a></li>
                        <li class='nav-item'><a href="{{ route('g-manage.shift.view') }}"class='nav-link'>シフト作成</a></li>
                        <li class='nav-item'><a href="{{ route('member.shift') }}" class='nav-link'>シフト表示</a></li>
                        @elsecan('MEMBER')
                        <li class='nav-item'><a href="{{ route('member.shift') }}" class='nav-link'>シフト表示</a></li>
                        <li class='nav-item'><a href="{{ route('member.desired') }}" class="nav-link">シフト提出</a></li>
                        @endcan
                    </ul>  --}}

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            {{--
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            --}}
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            {{-- TODO 管理者権限のみ --}}
                            @can('ADM')
                            <li class="nav-item">
                                <a href="{{ route('admin.contract') }}" class='nav-link'>契約管理</a>
                            </li>
                            @endcan
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @can("G-MNG") {{-- グループ管理者はグループ表示 --}}
                                    【{{ session('group_name') }}】
                                    @endcan
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @can('ADM')
                                        <a class="dropdown-item" href="{{ route('admin.home')}}">管理機能</a>
                                        <div class="dropdown-divider"></div>
                                    @endcan
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-2">
            <div class='container-fluid'>
                <h4>@yield('page_title')</h4>
                @yield('content')
            </div>
        </main>
    </div>
</body>
{{--  @stack('scripts')  --}}
</html>
