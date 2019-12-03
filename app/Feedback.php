<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    use SoftDeletes;

    protected $fillable = array('user_id', 'content');

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function getStatusDisplayAttribute() {
        return ['no' => '未回复', 'reply' => '已回复'][$this->attributes['status']];
    }
    public function scopeStatus($query, $status) {
        if (in_array($status, ['no', 'reply'])) {
            return $query->where('status', '=', $status);
        }
        return $query;
    }

    public function getDeletedDisplayAttribute()
    {
        if (! $this->attributes['deleted_at']) {
            return '未回复';
        }
        return '回复';
    }

    public function scopeUserName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $users = User::userName($name)->get()->all();

        if (count($users) > 0) {
            return $query->whereIn('user_id', array_map(function($n) {return $n->id;}, $users));
        }
        return $query->where('user_id', -1);
    }
	public function scopeUserPhone($query, $phone) {
        if (strlen($phone) == 0) {
            return $query;
        }

        $phones = User::where('telephone', 'LIKE', "%$phone%")->get()->all();

        if (count($phones) > 0) {
            return $query->whereIn('user_id', array_map(function($n) {return $n->id;}, $phones));
        }
        return $query->where('user_id', -1);
    }
}
