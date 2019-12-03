<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = array('title', 'thumbnail_url', 'content_url','audio_url','status','picture_url','content');

    public function getStatusDisplayAttribute() {
        return ['normal' => '正常', 'caogao' => '草稿'][$this->attributes['status']];
    }
}
