<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\Vote;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
  /**
   * Display a listing of the suggestions.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $suggestions = Suggestion::all();

    foreach ($suggestions as $suggestion) {
      $votes = Vote::where('suggestion_id', $suggestion->id)->count();
      $suggestion->nb_votes = $votes;
    }
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
      'user_email' => 'required',
      'state' => 'required'
    ]);

    $newSuggestion = new Suggestion([
      'title' => $request->get('title'),
      'description' => $request->get('description'),
      'user_email' => $request->get('user_email'),
      'state' => $request->get('state')
    ]);

    $newSuggestion->save();

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
      'user_email' => 'required',
      'state' => 'required'
    ]);

    $suggestion->title = $request->get('title');
    $suggestion->description = $request->get('description');
    $suggestion->user_email = $request->get('user_email');
    $suggestion->state = $request->get('state');

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

    return response()->json($suggestion::all());
  }
}
