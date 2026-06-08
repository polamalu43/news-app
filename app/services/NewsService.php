<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;

class NewsService
{
    private int $countryNo;
    private int $categoryNo;

    public function __construct(
        private NewsRepository $repository
    ) {}

    public function getNewsList(): array
    {
        return $this->repository->getNewsList();
    }

    public function fetchNews(int $countryNo, int $categoryNo, ?int $pageSize): array {
        $url = config('newsapi.api.news_api_url') . "top-headlines";
        $apiKey = config('newsapi.api.news_api_key');
        $categories = config('newsapi.category');
        $countryies = config('newsapi.country');

        $params = [
            'country'  => $countryies[$countryNo],
            'category' => $categories[$categoryNo],
            'apiKey'   => $apiKey,
        ];

        if ($pageSize !== null) {
            $params['pageSize'] = $pageSize;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'User-Agent: NewsApp/1.0',
                'Accept: application/json',
            ],
        ]);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false || $statusCode !== 200) {
            throw new \RuntimeException("HTTP Error / statusCode: {$statusCode} errorMessage: {curl_error($ch)}");
        }
        $this->countryNo = $countryNo;
        $this->categoryNo = $categoryNo;

        return json_decode($response, true);
    }

    public function insertNews(array $newsList): bool {
        $articles = $newsList['articles'] ?? [];
        $cnt = count($articles);
        if ($cnt < 1) {
            return false;
        }

        $insertList = [];
        foreach ($articles as $item) {
            $news = new News();
            $news->country = $this->countryNo;
            $news->category = $this->categoryNo;
            $news->name = $item['source']['name'];
            $news->title = $item['title'];
            $news->author = $item['author'];
            $news->url   = $item['url'];
            $news->thumbnail = $item['urlToImage'];
            $news->description = $item['description'];
            $news->content = $item['content'];
            $news->published_at = date('Y-m-d H:i:s', strtotime($item['publishedAt']));
            $insertList[] = $news;
        }
        return $this->repository->bulkInsert($insertList);
    }
}
