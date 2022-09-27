<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $urls = ['https://news.yahoo.co.jp/pickup/6439204', 'https://news.yahoo.co.jp/pickup/6439208', 'https://news.yahoo.co.jp/pickup/6439189'];
        $images = ['https://cdn.vuetifyjs.com/images/cards/sunshine.jpg', 'https://cdn.vuetifyjs.com/images/cards/cooking.png'];
        $texts = ['テキスト1', 'テキスト2', 'テキスト3'];

        return [
            'country' => $this->faker->numberBetween(0, 1),
            'category' => $this->faker->numberBetween(0, 6),
            'title' => $this->faker->company,
            'url' => $this->faker->randomElement($urls),
            'author' => $this->faker->name,
            'thumbnail' => $this->faker->randomElement($images),
            'description' => $this->faker->randomElement($texts),
            'publishedAt' => $this->faker->dateTimeBetween('-1year', 'now')->format('Y-m-d H:i:s'),
            'created_at' => $this->faker->dateTimeBetween('-1year', 'now')->format('Y-m-d H:i:s'),
            'updated_at' => $this->faker->dateTimeBetween('-1year', 'now')->format('Y-m-d H:i:s')
        ];
    }
}
