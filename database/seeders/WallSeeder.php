<?php

namespace Database\Seeders;

use App\Models\Wall;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            $wall = new Wall();
            $wall->name = $faker->word;
            $wall->tags = [$faker->word];
            $wall->source = $faker->url;
            $wall->color = $faker->hexColor;
            $wall->urls = ["full"=> $faker->url, "small"=>$faker->url];
            $wall->categories = [2];
            $wall->license = $faker->word;
            $wall->author = $faker->name;
            $wall->save();
        }
    }
}
