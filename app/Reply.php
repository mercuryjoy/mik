<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = ['content'];

    public function feedbacks() {
        return $this->belongsTo('App\Feedback', 'feedback_id');
    }
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

}