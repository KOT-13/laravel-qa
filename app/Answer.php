<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class Answer
 * @package App
 */
class Answer extends Model
{
    protected $fillable = ['body', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getBodyHtmlAttribute(): string
    {
        return \Parsedown::instance()->text($this->body);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($answer) {
            $answer->question->increment('answers_count');
        });

        static::deleted(function ($answer) {
            $answer->question->decrement('answers_count');
        });
    }

    /**
     * @return mixed
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->isBest() ? 'vote-accepted' : '';
    }

    /**
     * @return bool
     */
    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    /**
     * @return bool
     */
    public function isBest(): bool
    {
        return $this->id === $this->question->best_answer_id;
    }

    /**
     * @return MorphToMany
     */
    public function votes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'votable');
    }

    /**
     * @return MorphToMany
     */
    public function upVotes(): MorphToMany
    {
        return $this->votes()->wherePivot('vote', 1);
    }

    /**
     * @return MorphToMany
     */
    public function downVotes(): MorphToMany
    {
        return $this->votes()->wherePivot('vote', -1);
    }
}
