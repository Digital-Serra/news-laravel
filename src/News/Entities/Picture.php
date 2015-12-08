<?php

namespace DigitalSerra\NewsLaravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'path',
        'ext',
        'news_id',
    ];

    /**
     * A picture belongs to a news
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function news()
    {
        return $this->belongsTo(News::class,'news_id');
    }
}
