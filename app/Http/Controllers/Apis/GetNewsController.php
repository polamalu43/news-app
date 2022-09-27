<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

use Carbon\Carbon;
use Log;

class GetNewsController extends Controller
{
    public function index()
    {
        $this->insertArticles();
        dd($this->selectArticles());

        return view(
            'api.index',
            // compact('news')
        );
    }

    private function insertArticles()
    {
        $articles = [];

        // Article::getArticles(0, 2, 19);

        for($i = 0; $i < count(config('newscountry')); $i++){
            for ($i2 = 0; $i2 < count(config('newscategory')); $i2++) {
                array_push($articles, Article::getArticles($i, $i2, 30));
            }
        }

        $articles2 = [];
        foreach ($articles as $v) {
            foreach ($v as $v2) {
                array_push($articles2, $v2);
            }
        }

        Article::insert($articles2);
    }

    public function selectArticles(){
        $userId = Auth::user() ? Auth::user()->id : null;
        $articles = DB::table('articles AS a')
            ->leftJoin('user_article AS u', function($join) use($userId){
                $join->on('a.id', '=', 'u.article_id')
                    ->where('u.user_id', '=', $userId)
                    ->orWhere('u.user_id', '=', null);
            })
            ->selectRaw('a.*, u.user_id, CASE WHEN u.user_id = ? AND a.id IS NOT NULL THEN 1 ELSE 0 END AS isRecommended', [$userId])
            ->orderBy('publishedAt', 'DESC')
            // ->orderBy('isRecommended', 'DESC')
            ->limit(630)
            ->get()
            ->toArray();

        $countryies = config('newscountry');
        $categoryies = config('newscategory');
        $datas['articles'] = [];

        for($i = 0; $i < count($countryies); $i++){
            $datas['articles'][$countryies[$i]] = [];
            for ($i2 = 0; $i2 < count($categoryies); $i2++) {
                $datas['articles'][$countryies[$i]][$categoryies[$i2]] = [];
            }
        }

        foreach ($articles as $v) {
            $datas['articles'][$countryies[$v->country]][$categoryies[$v->category]][] = $v;
        }

        $datas['countryies'] = $countryies;
        $datas['categoryies'] = $categoryies;

        return json_encode($datas);
    }

    public function changeIsRecommended(Request $request)
    {
        $userId = Auth::user() ? Auth::user()->id : null;
        $articleId = $request['articleId'];
        $articles = User::find($userId)->articles();

        if(User::isRecommended($userId, $articleId)){
            $articles->detach($articleId);
        }else{
            $articles->attach($articleId);
        }

    }
}
