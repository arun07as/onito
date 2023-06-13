<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rating::truncate();

        $csv = Reader::createFromPath(database_path('imports/ratings.csv'))
            ->skipEmptyRecords()
            ->setHeaderOffset(0);

        $insertData = [];
        foreach ($csv as $record) {
            $insertData[] = [
                'tconst' => trim($record['tconst']),
                'average_rating' => trim($record['averageRating']),
                'num_votes' => (int)trim($record['numVotes']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Rating::insert($insertData);
    }
}
