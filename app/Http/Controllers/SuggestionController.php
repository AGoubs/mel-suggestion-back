<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SuggestionController extends Controller
{

  public function __construct()
  {
    Session::put('email', 'Arnaud@goubier.fr');
  }
  /**
   * Display a listing of the moderate and user's suggestions.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $suggestions = Suggestion::getAllModerateSuggestions();

    //We add all user's suggestions
    $suggestions = $suggestions->concat(Suggestion::getAllUserSuggestions());

    // dd($suggestions);

    return response()->json($suggestions);
  }

  /**
   * Store a newly created suggestion in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|max:255',
      'description' => 'required',
    ]);

    //TODO Static values
    $newSuggestion = new Suggestion([
      'title' => $request->get('title'),
      'description' => $request->get('description'),
      'user_email' => Session::get('email'),
      'state' => 'moderate',
      'instance' => 'bnum',
    ]);

    $newSuggestion->save();
    $newSuggestion->nb_votes = 0;

    return response()->json($newSuggestion);
  }

  /**
   * Display the specified suggestion.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $suggestion = Suggestion::findOrFail($id);
    return response()->json($suggestion);
  }

  /**
   * Update the specified suggestion in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $suggestion = Suggestion::findOrFail($id);

    $request->validate([
      'title' => 'required|max:255',
      'description' => 'required',
    ]);

    $suggestion->title = $request->get('title');
    $suggestion->description = $request->get('description');

    $suggestion->save();

    return response()->json($suggestion);
  }

  /**
   * Remove the specified suggestion from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $suggestion = Suggestion::findOrFail($id);
    $suggestion->delete();

    return response()->json("ok");
  }
}
