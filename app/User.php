<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function getUrlAttribute()
    {
 //       return route('users.show', $this->id);
        return '#';
    }

    /**
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return string
     */
    public function getAvatarAttribute()
    {
        $email = $this->email;
        $size = 32;

        return "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?&s=" . $size;
    }

    /**
     * @return BelongsToMany
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'favorites')->withTimestamps();
    }

    /**
     * @return MorphToMany
     */
    public function voteQuestions(): MorphToMany
    {
        return $this->morphedByMany(Question::class, 'votable');
    }

    /**
     * @return MorphToMany
     */
    public function voteAnswers(): MorphToMany
    {
        return $this->morphedByMany(Answer::class, 'votable');
    }

    /**
     * @param Question $question
     * @param $vote
     */
    public function voteQuestion(Question $question, $vote): void
    {
        $voteQuestions = $this->voteQuestions();

        $this->_vote($voteQuestions, $question, $vote);
    }


    /**
     * @param Answer $answer
     * @param $vote
     */
    public function voteAnswer(Answer $answer, $vote): void
    {
        $voteAnswers = $this->voteAnswers();

        $this->_vote($voteAnswers, $answer, $vote);
    }

    /**
     * @param $relationship
     * @param $model
     * @param $vote
     */
    private function _vote($relationship, $model, $vote): void
    {
        if ($relationship->where('votable_id', $model->id)->exists()) {
            $relationship->updateExistingPivot($model, ['vote' => $vote]);
        }
        else {
            $relationship->attach($model, ['vote' => $vote]);
        }

        $model->load('votes');
        $downVotes = (int) $model->downVotes()->sum('vote');
        $upVotes = (int) $model->upVotes()->sum('vote');

        $model->votes_count = $upVotes + $downVotes;
        $model->save();
    }
}
