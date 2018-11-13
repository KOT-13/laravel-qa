<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;

/**
 * Class AcceptAnswerController
 * @package App\Http\Controllers
 */
class AcceptAnswerController extends Controller
{

    /**
     * @param Answer $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Answer $answer)
    {
        $this->authorize('accept', $answer);
        $answer->question->acceptBestAnswer($answer);
        return back();
    }
}
