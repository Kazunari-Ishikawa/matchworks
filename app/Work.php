<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    // 案件種別
    public function getTypeAttribute()
    {
        switch($this->attributes['type']) {
            case 0:
                return '単発案件';
            case 1:
                return 'レベニューシェア案件';
        }
    }

    protected $fillable = [
        'title', 'type', 'category', 'max_price', 'min_price', 'content', 'user_id', 'is_closed'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
