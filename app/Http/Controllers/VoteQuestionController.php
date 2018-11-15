<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;

/**
 * Class VoteQuestionController
 * @package App\Http\Controllers
 */
class VoteQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Question $question)
    {
        $vote = (int) request()->vote;

        auth()->user()->voteQuestion($question, $vote);

        return back();
    }
}
