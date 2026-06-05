<?php

namespace Core;

class Response
{
    public function __construct(
        private mixed $body,
        private int $status = 200,
        private array $headers = []
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }
        echo $this->body;
    }

    public static function json(
        mixed $data,
        int $status = 200
    ): self {
        return new self(
            headers: [
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            status: $status,
            body: json_encode($data, JSON_UNESCAPED_UNICODE),
        );
    }
}
