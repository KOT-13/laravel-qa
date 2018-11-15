<?php
namespace App;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait VotableTrait
 * @package App
 */
trait VotableTrait
{
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