<?php

namespace App\Repositories;

use Core\QueryBuilder;
use App\Models\News;

class NewsRepository
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(News::$table, News::class);
    }

    public function getNewsList(): array
    {
        return $this->qb->get();
    }

    public function bulkInsert(array $data): bool
    {
        return $this->qb->bulkInsert($data, true);
    }
}
