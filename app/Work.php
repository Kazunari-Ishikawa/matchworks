<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Work extends Model
{
    protected $table = 'works';

    // 案件種別の定義
    const TYPE = [
        1 => '単発案件',
        2 => 'レベニューシェア'
    ];

    // 案件種別を文字列に変換
    public function getTypeAttribute()
    {
        $type = $this->attributes['type'];

        return self::TYPE[$type];
    }

    // 最小金額を千円単位に変換
    public function getMinPriceAttribute()
    {
        return $this->attributes['min_price'] * 1000;
    }

    // 最大金額を千円単位に変換
    public function getMaxPriceAttribute()
    {
        return $this->attributes['max_price'] * 1000;
    }

    // 日付フォーマットの変換
    public function getCreatedAtAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('Y/n/j');
    }

    // Workの応募数をカウントする
    public function getCountsAttribute()
    {
        return $this->applies()->where('work_id', $this->id)->count();
    }

    // 該当のWorkがユーザーにbookmarkされているか判定する
    public function getBookmarkedAttribute()
    {
        return $this->bookmarks->contains(function($bookmark) {
            return $bookmark->user_id === Auth::id();
        });
    }

    protected $appends = [
        'counts', 'bookmarked'
    ];

    protected $fillable = [
        'title', 'type', 'category_id', 'max_price', 'min_price', 'content', 'user_id', 'is_closed'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function boards()
    {
        return $this->hasMany('App\Board');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function applies()
    {
        return $this->hasMany('App\Apply');
    }

    public function bookmarks()
    {
        return $this->hasMany('App\Bookmark');
    }
}
