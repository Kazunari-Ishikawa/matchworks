<!-- ヘッダー -->
<header class="l-header">

  <div class="l-header--left">
    <a class="l-header__title" href="/">
      <h1>match</h1>
    </a>
    <form action="" class="c-search">
      <input type="text" class="c-search__input" placeholder="キーワードを入力">
      <div class="c-search__btn"><i class="fas fa-search"></i></div>
    </form>
  </div>

  <div class="l-header--right">
    <nav class="l-nav">
      <!-- PC向けメニュー -->
      <!-- 未ログイン時 -->
      @guest
      <ul class="c-menu">
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('works.index') }}">案件を探す</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('register') }}">会員登録</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('login') }}">ログイン</a></li>
      </ul>
      <ul class="c-spMenu">
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('works.index') }}">案件を探す</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('register') }}">会員登録</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('login') }}">ログイン</a></li>
      </ul>

      @endguest

      <!-- ログイン時 -->
      @auth
      <ul class="c-menu">
        <li class="c-menu__item"><a class="c-menu__link" href="{{ route('works.index') }}">案件を探す</a>
        </li>
        <li class="c-menu__item"><a class="c-menu__link" href="likeProjects.html">気になるリスト</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="history.html">閲覧履歴</a></li>
        <li class="c-menu__item"><a class="c-menu__link" href="/mypage">マイページ</a></li>
      </ul>
      <!-- スマホ向けメニュー -->
      <ul class="c-spMenu">
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="/mypage">マイページ</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="{{ route('works.new') }}">案件を登録する</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="registeredProjects.html">登録した案件一覧</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="appliedProjects.html">応募した案件一覧</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="closedProjects.html">成約済み案件一覧</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="likeProjects.html">気になるリスト</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="history.html">閲覧履歴</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="">投稿したコメント一覧</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="">ダイレクトメッセージ一覧</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="editProfile.html">プロフィール編集</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="editPassword.html">パスワード変更</a></li>
        <li class="c-spMenu__item"><a class="c-spMenu__link" href="help.html">ヘルプ</a></li>
        <li class="c-menu__item c-menu__item--side">
          <a href="{{'logout'}}" class="c-menu__link c-menu__link--side" onclick="event.preventDefault();
            document.getElementById('logoutForm').submit();">ログアウト</a>
          <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
      </ul>
      @endauth
    </nav>
  </div>

  <!-- ハンバーガーメニュー -->
  <div class="c-menuTrigger">
    <span class="c-menuTrigger__bar"></span>
    <span class="c-menuTrigger__bar"></span>
    <span class="c-menuTrigger__bar"></span>
  </div>

</header>