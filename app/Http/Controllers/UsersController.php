<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use App\User;

class UsersController extends Controller
{
    // マイページ表示
    public function mypage()
    {
        $user = Auth::user();

        return view('users.mypage', ['user' => $user]);
    }

    // ユーザー詳細画面表示
    public function show($id)
    {
        // パラメータが数字でない場合リダイレクト
        if (!ctype_digit($id)){
            return redirect('/')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        $user = User::with(['works'])->find($id);

        // 存在しないuserのIDの場合リダイレクト
        if (!$user) {
            return redirect('/')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        return view('users.show', compact('user'));
    }

    // プロフィール編集画面表示
    public function edit()
    {
        $user = Auth::user();

        return view('users.edit', ['user' => $user]);
    }
    // プロフィールを変更する
    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();
        // icon以外の入力を代入
        $user->fill($request->except('icon'));
        // リクエストにファイルが存在し、アップロードに成功した場合、ファイル名をモデルへ代入
        if ($request->hasFile('icon')) {
            if ($request->file('icon')->isValid()) {
                $path = $request->icon->store('public/img/icons');
                $user->icon = basename($path);
            }
        }
        $user->save();

        return redirect('/mypage');
    }
    // パスワード変更画面表示
    public function editPassword()
    {
        return view('users.editPassword');
    }
    // パスワードを変更する
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect('/mypage');
    }
    // 退会画面表示
    public function showWithdrawForm()
    {
        return view('users.withdraw');
    }
    // 退会する
    public function withdraw()
    {
        $user_id = Auth::id();
        $works = User::find($user_id)->works;
        foreach ($works as $work) {
            $work->delete();
        }
        \Log::debug($works);
        User::find($user_id)->delete();
        Auth::logout();
        return redirect('/');
    }
}
