<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

use Carbon\Carbon;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'category',
        'title',
        'url',
        'author',
        'thumbnail',
        'description',
        'publishedAt',
    ];

    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_article');
    }

    public static function getArticles($countryNo, $categoryNo, $pageSize){
        $categoryies = config('newscategory');
        $countryies = config('newscountry');

        try {
            $url = config('newsapi.news_api_url') . "top-headlines";
            $method = "GET";
            $options = [
                'query' => [
                    'country' => $countryies[$countryNo],
                    'category' => $categoryies[$categoryNo],
                    'pageSize' => $pageSize,
                    'apiKey' => config('newsapi.news_api_key')
                ],
            ];

            $client = new Client(['verify' => false]);
            $response = $client->request($method, $url, $options);
            $results = $response->getBody()->getContents();
            $datas = json_decode($results, true);

            $articles = [];

            for ($i = 0; $i < count($datas['articles']); $i++) {
                $carbon = new Carbon($datas['articles'][$i]['publishedAt']);
                $now = Carbon::now();
                array_push($articles, [
                    'country' => $countryNo,
                    'category' => $categoryNo,
                    'title' => $datas['articles'][$i]['title'],
                    'author' => $datas['articles'][$i]['author'],
                    'url' => $datas['articles'][$i]['url'],
                    'thumbnail' => $datas['articles'][$i]['urlToImage'],
                    'description' => $datas['articles'][$i]['description'],
                    'publishedAt' => $carbon->format('Y-m-d h:i:s'),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        } catch (RequestException $e) {
            echo Psr7\Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\Message::toString($e->getResponse());
            }
        }

        return $articles;
    }
}