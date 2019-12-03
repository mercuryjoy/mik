<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Code extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'batch_id'];

    public function batch()
    {
        return $this->belongsTo('App\CodeBatch', 'batch_id');
    }
	public function scopeCode($query, $level) {
		if (strlen($level) > 0) {
			return $query->where('code', 'LIKE', "%$level%");
		}
		return $query;
	}
    public function scopeKeyword($query, $keyword) {
		if (strlen($keyword) > 0) {
			return $query->where('id', '=', "$keyword");
		}
		return $query;
	}

	public function scopeBatchName($query, $name) {
        if (strlen($name) == 0) {
            return $query;
        }

        $codebatchs = CodeBatch::where('name', 'LIKE', "%$name%")->get()->all();

        if (count($codebatchs) > 0) {
            return $query->whereIn('batch_id', array_map(function($n) {return $n->id;}, $codebatchs));
        }
        return $query->where('batch_id', -1);
    }
}
