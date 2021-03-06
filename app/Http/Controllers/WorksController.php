<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateWorkRequest;
use App\Notifications\ApplyWorkNotification;
use App\Notifications\CancelApplyNotification;
use App\Notifications\DeleteWorkNotification;
use App\Notifications\CloseWorkNotification;
use App\Work;
use App\Category;
use App\User;
use App\Comment;
use App\Apply;
use App\Bookmark;

class WorksController extends Controller
{
    // 1ページあたりのWorkの表示数
    protected $per_page = 10;

    // Work一覧画面を表示する
    public function index(Request $request)
    {
        $category = 0;
        $type = 0;

        // URLパラメータが存在する場合代入する
        // category, type以外の場合、category=0, type=0とする
        if ($request->category) {
            $category = ((int)$request->category);
            // 存在しないカテゴリの場合、0を代入
            if( $category < 1 || 7 < $category) {
                $category = 0;
            }
        }
        if ($request->type) {
            $type = ((int)$request->type);
            // 存在しない案件種別の場合、0を代入
            if ($type !== 1 && $type !== 2) {
                $type = 0;
            }
        }

        return view('works.index', compact('category', 'type'));
    }

    // Work新規登録画面を表示する
    public function new()
    {
        return view('works.new');
    }

    // WorkをDBへ保存する
    public function create(CreateWorkRequest $request)
    {
        $work = new Work;
        $work->user_id = Auth::id();

        // レベニューシェアの場合金額不要のため、最小、最大金額を0に上書きして登録する
        if ($request->type === '1') {
            $work->fill($request->all())->save();
        } else {
            $work->fill($request->except(['min_price', 'max_price']));
            $work->max_price = 0;
            $work->min_price = 0;
            $work->save();
        }

        return redirect('/mypage')->with('flash_message', '新しく登録しました。');
    }

    // Work詳細画面を表示する
    public function show($id)
    {
        // パラメータが数字でない場合リダイレクト
        if (!ctype_digit($id)){
            return redirect('/works')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        $work = Work::with(['user', 'category'])->find($id);

        // 存在しないworkのIDの場合リダイレクト
        if (!$work) {
            return redirect('/works')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        // 該当のWorkに対して、ユーザーがWorkの登録者であるか判定
        $is_registered = ($work->user_id == Auth::id()) ? true : false;

        // 該当のWorkに対して、ユーザーが応募済みであるか判定
        $applies = Apply::where('work_id', $id)->get();
        $is_applied = false;
        foreach( $applies as $apply) {
            if($apply->user_id == Auth::id()) {
                $is_applied = true;
                break;
            };
        }

        return view('works.show', compact('work', 'is_registered','is_applied'));
    }

    // Work編集画面を表示する
    public function edit($id)
    {
        // パラメータが数字でない場合リダイレクト
        if(!ctype_digit($id)){
            return redirect('/mypage')->with('flash_message',__('Invalid operation was performed.'));
        }

        // 登録者以外が対象のworkを編集しようとした場合リダイレクト
        if (!Auth::user()->works()->find($id)) {
            return redirect('/mypage')->with('flash_message',__('This is not yours! DO NOT EDIT!'));
        }

        $work = Auth::user()->works()->with('category')->find($id);

        return view('works.edit', compact('work'));
    }

    // Workを編集する
    public function update(CreateWorkRequest $request, $id)
    {
        $work = Work::find($id);

        // レベニューシェアの場合金額不要のため、最小、最大金額を0に上書きして登録する
        if ($request->type === '1') {
            $work->fill($request->all())->save();
        } else {
            $work->fill($request->except(['min_price', 'max_price']));
            $work->max_price = 0;
            $work->min_price = 0;
            $work->save();
        }

        return redirect('/mypage')->with('flash_message', '案件を編集しました。');
    }

    // Work削除をする
    public function destroy($id)
    {
        // パラメータが数字でない場合リダイレクト
        if(!ctype_digit($id)){
            return redirect('/mypage')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        // 登録者以外が対象のworkを編集しようとした場合リダイレクト
        if (!Auth::user()->works()->find($id)) {
            return redirect('/mypage')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        $work = Auth::user()->works()->find($id);

        // workのis_closedがtrueの場合、削除処理がすでに行われているため以下の処理は実行しない
        if (!$work->is_closed) {
            // Workに紐づくBoardとMessageを削除
            $boards = $work->boards;
            foreach($boards as $board) {
                $board->messages()->delete();
                $board->delete();
            }

            // Workに紐づくBookmarkを削除
            $work->bookmarks()->delete();

            // Workへの応募者へ削除の通知をし、Applyを削除
            $applies = $work->applies;
            foreach($applies as $apply) {
                $applied_user = $apply->user;
                $applied_user->notify(new DeleteWorkNotification($work, Auth::user()));
                $apply->delete();
            }
        }

        $work->delete();

        return redirect('/mypage')->with('flash_message', '削除しました。');
    }

    // Workの成約処理
    public function close($id)
    {
        if (!ctype_digit($id)) {
            return redirect('/mypage')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        if (!Auth::user()->works()->find($id)) {
            return redirect('/mypage')->with('flash_message', '不正な処理がされました。時間を置いてやり直してください。');
        }

        // workの成約処理
        $work = Auth::user()->works()->find($id);
        $work->is_closed = true;
        $work->save();

        // 成約時には、Workに紐づくBoardとMessageを削除する
        $boards = $work->boards;
        foreach($boards as $board) {
            $board->messages()->delete();
            $board->delete();
        }

        // Workに紐づくBookmarkを削除
        $work->bookmarks()->delete();

        // Workの応募者へ案件が成約済みとなったことを通知し、Applyを削除する
        $applies = $work->applies;
        foreach($applies as $apply) {
            $applied_user = $apply->user;
            $applied_user->notify(new CloseWorkNotification($work, Auth::user()));
            $apply->delete();
        }

        return redirect('/mypage')->with('flash_message', '成約済みに変更しました。');;
    }

    // Workへの応募処理
    public function apply($id)
    {
        $work = Work::with('user')->find($id);
        $applied_user = Auth::user();

        // Applyを保存
        $apply = new Apply;
        $apply->work_id = $work->id;
        $apply->user_id = $applied_user->id;
        $apply->save();

        // BoardsControllerを呼び出して、createメソッドを行う
        // create(work_id, from_user_id, to_user_id)
        $board = app()->make('App\Http\Controllers\BoardsController');
        $board->create($id, $applied_user->id, $work->user_id);

        // Workの登録者へ、応募をメール通知する
        $registered_user = User::find($work->user_id);
        $registered_user->notify(new ApplyWorkNotification($work, $applied_user));

        return redirect('/messages')->with('flash_message','応募しました。メッセージで連絡しましょう。');
    }

    // Workへの応募を取り消す
    public function cancel($id)
    {
        $work = Work::find($id);
        $cancel_user = Auth::user();

        // BoardsControllerを呼び出して、cancelメソッドを行う
        // cancel(work_id, from_user_id, to_user_id)
        $board = app()->make('App\Http\Controllers\BoardsController');
        $board->cancel($id, $cancel_user->id, $work->user_id);

        // 該当するApplyを削除する
        Apply::where(['work_id' => $id, 'user_id' => $cancel_user->id])->delete();

        // Workの登録者へ、応募を取り消したことをメール通知する
        $registered_user = User::find($work->user_id);
        $registered_user->notify(new CancelApplyNotification($work, $cancel_user));

        return redirect('/works/applied')->with('flash_message','応募を取り消しました。メッセージも削除されました。');
    }

    // 登録した案件一覧画面表示
    public function showRegisteredWorks()
    {
        return view('works.registeredWorks');
    }

    // 応募した案件一覧画面表示
    public function showAppliedWorks()
    {
        return view('works.appliedWorks');
    }

    // 成約した案件一覧画面表示
    public function showClosedWorks()
    {
        return view('works.closedWorks');
    }

    // 成約した案件一覧画面表示
    public function showBookmarksWorks()
    {
        return view('works.bookmarks');
    }

    // Work一覧を取得する
    public function getworks()
    {
        $works = Work::with(['user', 'category'])->where('is_closed', false)->orderBy('created_at', 'desc')->paginate($this->per_page);

        return $works;
    }

    // ユーザーが登録したWork一覧を取得する
    public function getRegisteredWorks()
    {
        $works = Work::where(['user_id' => Auth::id(), 'is_closed' => false])->with(['user', 'category'])->paginate($this->per_page);

        return $works;
    }

    // ユーザーが応募しているWork一覧を取得する
    public function getAppliedWorks()
    {
        // ユーザーが応募しているWorkのIDを取得する
        $applied_work_id = Apply::select('work_id')->where('user_id', Auth::id())->get();

        // 該当するWorkを取得
        $works = Work::with(['user', 'category'])->where('is_closed', false)->whereIn('id', $applied_work_id)->paginate($this->per_page);

        return $works;
    }

    // ユーザーがコメントしたWork一覧を取得する
    public function getCommentedWorks()
    {
        // ユーザーがコメントしたWorkのIDを取得する
        $commented_work_id = Comment::select('work_id')->where('user_id', Auth::id())->groupBy('work_id')->get();

        // 該当するWorkを取得
        $works = Work::with(['user', 'category'])->where('is_closed', false)->whereIn('id', $commented_work_id)->paginate($this->per_page);

        return $works;
    }

    // 成約したWork一覧を取得する
    public function getClosedWorks()
    {
        // 成約したWorkを取得する
        $works = Work::with(['user', 'category'])->where(['user_id' => Auth::id(), 'is_closed' => true])->paginate($this->per_page);

        return $works;
    }

    // BookmarkしたWork一覧を取得する
    public function getBookmarksWorks()
    {
        // ユーザーがBookmarkしたWorkのIDを取得する
        $bookmarked_work_id = Bookmark::select('work_id')->where('user_id', Auth::id())->groupBy('work_id')->get();

        // 該当するWorkを取得
        $works = Work::with(['user', 'category'])->where(['is_closed' => false])->whereIn('id', $bookmarked_work_id)->paginate($this->per_page);

        return $works;
    }

    // Workの検索結果を表示する
    public function searchWorks(Request $request)
    {
        // 検索条件
        $type = 0;
        $category = 0;
        $min_price = 0;
        $max_price = 0;

        // 案件種別が指定されていれば入力値を代入
        // 0は指定無しなのでwhere句不要
        if ($request->form['type'] !== 0) {
            $type = $request->form['type'];
        }

        // カテゴリが指定されていれば入力値を代入
        // 0は指定無しなのでwhere句不要
        if ($request->form['category'] !== 0) {
            $category = $request->form['category'];
        }

        // 最小金額が指定されていれば入力値を代入
        // 0は指定無しなのでwhere句不要
        if ($request->form['minPrice'] !== 0) {
            $min_price = $request->form['minPrice'];
            switch ($min_price) {
                case 1:
                    $min_price = 1;
                break;
                case 2:
                    $min_price = 3;
                break;
                case 3:
                    $min_price = 5;
                break;
                case 4:
                    $min_price = 10;
                break;
                case 5:
                    $min_price = 50;
                break;
                case 6:
                    $min_price = 100;
                break;
                case 7:
                    $min_price = 500;
                break;
                case 8:
                    $min_price = 1000;
                    default:
                break;
            }
        }

        // 最大金額が指定されていれば入力値を代入
        // 0は指定無しなのでwhere句不要
        if ($request->form['maxPrice'] !== 0) {
            $max_price = $request->form['maxPrice'];
            switch ($max_price) {
                case 1:
                    $max_price = 1;
                break;
                case 2:
                    $max_price = 3;
                break;
                case 3:
                    $max_price = 5;
                break;
                case 4:
                    $max_price = 10;
                break;
                case 5:
                    $max_price = 50;
                break;
                case 6:
                    $max_price = 100;
                break;
                case 7:
                    $max_price = 500;
                break;
                case 8:
                    $max_price = 1000;
                    default:
                break;
            }
        }

        $works = Work::with(['user', 'category'])
                ->when($type, function($query, $type){
                    return $query->where('type', $type);
                })
                ->when($category, function($query, $category) {
                    return $query->where('category_id', $category);
                })
                ->when($min_price, function($query, $min_price) {
                    return $query->where('min_price', '>=', $min_price);
                })
                ->when($max_price, function($query, $max_price) {
                    return $query->where('max_price', '<=', $max_price);
                })
                ->where('is_closed', false)->orderBy('created_at', 'desc')->paginate($this->per_page);

        return $works;
    }
}
