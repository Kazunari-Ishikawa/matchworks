<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    protected $table = 'applies';

    public function work()
    {
        return $this->belongsTo('App\Work');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
