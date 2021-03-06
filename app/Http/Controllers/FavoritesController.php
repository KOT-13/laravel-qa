<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class FavoritesController
 * @package App\Http\Controllers
 */
class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Question $question
     * @return RedirectResponse
     */
    public function store(Question $question): RedirectResponse
    {
        $question->favorites()->attach(auth()->id());

        return back();
    }

    /**
     * @param Question $question
     * @return RedirectResponse
     */
    public function destroy(Question $question): RedirectResponse
    {
        $question->favorites()->detach(auth()->id());

        return back();
    }
}
