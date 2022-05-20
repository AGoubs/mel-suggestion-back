<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Suggestion extends Model
{
  use HasFactory;
  protected $fillable = [
    'id',
    'title',
    'description',
    'user_email',
    'instance',
    'state',
  ];

  public static function getAllModerateSuggestions()
  {
    $suggestions = Suggestion::where('state', 'moderate')->where('user_email', '<>', Session::get('email'))->get();

    foreach ($suggestions as $suggestion) {
      $votes = Vote::where('suggestion_id', $suggestion->id)->count();
      $suggestion->nb_votes = $votes;

      $voted = Vote::where('suggestion_id', $suggestion->id)->where('user_email', Session::get('email'))->get();
      if (!$voted->isEmpty()) {
        $suggestion->voted = true;
        $suggestion->vote_id = $voted->first()->id;
      }
    }

    return $suggestions;
  }

  public static function getAllUserSuggestions()
  {
    $my_suggestions = Suggestion::where('user_email', Session::get('email'))->get();
    foreach ($my_suggestions as $suggestion) {
      $votes = Vote::where('suggestion_id', $suggestion->id)->count();
      $suggestion->nb_votes = $votes;
      $suggestion->my_vote = true;
    }

    return $my_suggestions;
  }
}
