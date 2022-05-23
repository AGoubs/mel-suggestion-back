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

  public static function getAllSuggestions()
  {
    $suggestions = Suggestion::all();

    $suggestions = self::countVotes($suggestions);

    $suggestions = self::isVoted($suggestions);

    return $suggestions;
  }

  public static function getAllVoteSuggestions()
  {
    $suggestions = Suggestion::where('state', 'vote')->orWhere('state', 'validate')->where('user_email', '<>', Session::get('email'))->get();

    $suggestions = self::countVotes($suggestions);

    $suggestions = self::isVoted($suggestions);

    return $suggestions;
  }

  public static function getAllUserSuggestions()
  {
    $suggestions = Suggestion::where('user_email', Session::get('email'))->get();
    $suggestions = self::addMySuggestion($suggestions);

    return $suggestions;
  }

  private function countVotes($suggestions)
  {
    foreach ($suggestions as $suggestion) {
      $votes = Vote::where('suggestion_id', $suggestion->id)->count();
      $suggestion->nb_votes = $votes;
    }
    return $suggestions;
  }

  private function isVoted($suggestions)
  {
    foreach ($suggestions as $suggestion) {
      $voted = Vote::where('suggestion_id', $suggestion->id)->where('user_email', Session::get('email'))->get();
      if (!$voted->isEmpty()) {
        $suggestion->voted = true;
        $suggestion->vote_id = $voted->first()->id;
      }
    }
    return $suggestions;
  }

  private function addMySuggestion($suggestions)
  {
    foreach ($suggestions as $suggestion) {
      $votes = Vote::where('suggestion_id', $suggestion->id)->count();
      $suggestion->nb_votes = $votes;
      $suggestion->my_suggestion = true;
    }
    return $suggestions;
  }
}
