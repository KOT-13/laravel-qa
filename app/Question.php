<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class Question
 * @package App
 */
class Question extends Model
{
    use VotableTrait;
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $value
     */
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('questions.show', $this->slug);
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
        if ($this->answers_count > 0) {
            if ($this->best_answer_id) {
                return "answered-accepted";
            }
            return "answered";
        }
        return "unanswered";
    }

    /**
     * @return string
     */
    public function getBodyHtmlAttribute(): string
    {
        return $this->bodyHtml();
    }

    /**
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('votes_count', 'desc');
    }

    /**
     * @param Answer $answer
     */
    public function acceptBestAnswer(Answer $answer): void
    {
        $this->best_answer_id = $answer->id;
        $this->save();
    }

    /**
     * @return BelongsToMany
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * @return bool
     */
    public function isFavorited(): bool
    {
        return $this->favorites()->where('user_id', auth()->id())->count() > 0;
    }

    /**
     * @return mixed
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * @return mixed
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    /**
     * @return string
     */
    public function getExcerptAttribute(): string
    {
        return str_limit(strip_tags($this->bodyHtml()), 250);
    }

    /**
     * @return string
     */
    private function bodyHtml(): string
    {
        return clean(\Parsedown::instance()->text($this->body));
    }
}
