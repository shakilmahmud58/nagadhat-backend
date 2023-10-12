<?php

namespace App\Http\Controllers;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SearchController extends Controller
{
   //declare the search engine and save keywords with other information in database

    public function create(Request $req)
    {
        $searchEngine = ['computer', 'laptop', 'mobile'];
        $text = $req->text;
        $user = $req->user;
        
        $filtered_arr = array_filter($searchEngine,function($item) use ($text) {
            return strpos($item, $text) !== false;
        });
        $results=array_values($filtered_arr);
        $savedata =[
            'text' =>$text,
            'user' =>$user,
            'results' =>$results,
        ];
        History::create($savedata);
        return $results;
    }
    
    //loading the history of the given date, if starting or ending date is not given it will return all history
    
    public function show(Request $req)
    {
      $starting_date = $req->startdate;
      $ending_date = $req->enddate;
      if($starting_date=='' || $ending_date=='')
      {
        $history= History::all();
      }
      else
      {
        $history = History::whereBetween('created_at', [$starting_date, $ending_date])->get();
      }
      
      return $history;
    }

    //load the keywords and their count

    public function showkeyword()
    {
      $keywords = History::selectRaw('count(*) as count, text')
      ->groupBy('text')
      ->get();
    return $keywords;
    }
}
