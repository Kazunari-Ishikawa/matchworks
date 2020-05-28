@extends('layouts.app')

@section('description')
<meta name="description" content="気になる案件一覧ページです。matchworksとは、IT系の仕事探しや仕事を依頼したい方々向けに、かんたん3ステップで繋げるビジネスマッチングサービスです。">
@endsection

@section('keywords')
<meta name="keywords" content="気になる, 案件一覧">
@endsection

@section('title')
<title>気になる案件 - {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('content')
<div class="l-container l-container--withSide">

  <!-- サイドバー -->
  @include('components.sidebar')

  <!-- メインコンテンツ -->
  <section class="l-container__body--withSide">

    <work-list :list-title='@json("気になる案件")'></work-list>

  </section>

</div>

@endsection
