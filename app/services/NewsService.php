<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;

class NewsService
{
    private string $newsSource = 'qiita';
    private const QIITA_RSS_URL = 'https://qiita.com/popular-items/feed';

    public function __construct(
        private NewsRepository $repository
    ) {}

    public function getNewsList(): array
    {
        return $this->repository->getNewsList();
    }

    public function fetchQiitaNews(): void {
        $xml = simplexml_load_file(self::QIITA_RSS_URL);
        $newsList = [];
        foreach ($xml->entry as $item) {
            $news = new News();
            $news->title = (string)$item->title;
            $news->url   = (string)$item->link['href'];
            $news->source = $this->newsSource;
            $news->published_at = date('Y-m-d H:i:s', strtotime($item->published));
            $newsList[] = $news;
        }
        $this->repository->bulkInsert($newsList);
    }
}
