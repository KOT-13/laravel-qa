<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;

/**
 * Class VoteAnswerController
 * @package App\Http\Controllers
 */
class VoteAnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Answer $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Answer $answer)
    {
        $vote = (int) request()->vote;

        auth()->user()->voteAnswer($answer, $vote);

        return back();
    }
}
