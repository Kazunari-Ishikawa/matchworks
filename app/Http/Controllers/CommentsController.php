<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comment;

class CommentsController extends Controller
{
    // パラメータで指定されたWorkに対するCommentを取得する
    public function getComments($id)
    {
        $comments = Comment::where('work_id', $id)->with(['user'])->get();

        return response($comments);
    }

    // コメントを投稿する
    public function create(Request $request, $id)
    {
        // ログインユーザーでない場合ログインページへ
        if (!Auth::check()) {
            return redirect('/login');
        }
        // パラメータが数字でない場合リダイレクト
        if(!ctype_digit($id)){
            return redirect('/mypage')->with('flash_message',__('Invalid operation was performed.'));
        }
        $comment = new Comment;
        $comment->work_id = $id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->save();

        // DBへ保存後、Work詳細表示へリダイレクト
        return redirect()->route('works.show', ['id' => $id]);
    }
}
