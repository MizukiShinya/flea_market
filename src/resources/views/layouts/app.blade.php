<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>coachtech_fleaMarket</title>
        <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/common.css') }}">
        @yield('css')
    </head>
    <body>
        <header class="header">
            <div class="header-logo">
                <img src="{{asset('/logo.svg')}}" alt="coachtech">
            </div>
            <!-- ログイン・登録ページでは検索やリンク非表示 -->
            @if(!Route::is('login') && !Route::is('register'))
            <!-- 中央: 検索フォーム -->
            <div class="header-search">
                <form class="search-form" action="{{route('item.search')}}" method="get">
                    <input class="header-search__input" type="text" name="keyword" value="{{ request('keyword')?? session('search_keyword') }}" placeholder="何をお探しですか？">
                </form>
            </div>
            <!-- 右: リンクリスト（ログイン状態によって変える） -->
            <nav>
                <ul class="header-nav">
                    @if(Auth::check())
                        <li class="header-nav__item">
                            <form class="form" action="{{route('logout')}}" method="post">
                                @csrf
                                <button type="submit" class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__item">
                            <button class="header-nav__button-sell" onclick="location.href='{{ route('item.create') }}'">出品</button>
                        </li>
                    @else
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="/login">ログイン</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__button" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__item">
                            <button class="header-nav__button-sell" onclick="location.href='{{ route('item.create') }}'">出品</button>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </header>
        <main>
            @yield('content')
        </main>
        @yield('js')
    </body>
</html>