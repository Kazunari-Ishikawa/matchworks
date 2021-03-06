@extends('layouts.app')

@section('description')
<meta name="description" content="パスワード変更ページです。">
@endsection

@section('keywords')
<meta name="keywords" content="パスワード変更">
@endsection

@section('title')
<title>パスワード変更 - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')

<div class="l-container l-container--withSide">

  <!-- サイドバー -->
  @include('components.sidebar')

  <!-- メインコンテンツ -->
  <section class="l-container__body--withSide">
    <h2 class="c-settings__title">パスワード変更</h2>

    <div class="c-settings__contents">
      <form action="{{ route('users.editPassword') }}" method="POST" class="c-form">
        @csrf

        <div class="c-form__group">
          <label for="current_password" class="c-form__label">現在のパスワード</label>
          @error('current_password')
          <span class="c-form__error">{{ $message }}</span>
          @enderror
          <input type="password" class="c-form__input" name="current_password" id="current_password">
        </div>

        <div class="c-form__group">
          <label for="password" class="c-form__label">新しいパスワード</label>
          @error('password')
          <span class="c-form__error">{{ $message }}</span>
          @enderror
          <input type="password" class="c-form__input" name="password" id="password" placeholder="8〜16文字の半角英数字">
        </div>

        <div class="c-form__group">
          <label for="password_confirmation" class="c-form__label">新しいパスワード（再入力）</label>
          <input type="password" class="c-form__input" name="password_confirmation" id="password_confirmation">
        </div>

        <input type="submit" class="c-btn c-btn--full" value="変更する">

      </form>
    </div>
  </section>
</div>

@endsection
