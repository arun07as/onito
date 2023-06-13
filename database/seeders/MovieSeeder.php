<?php

namespace Database\Seeders;

use App\Models\Movie;
use League\Csv\Reader;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::truncate();

        $csv = Reader::createFromPath(database_path('imports/movies.csv'))
            ->skipEmptyRecords()
            ->setHeaderOffset(0);

        $insertData = [];
        foreach ($csv as $record) {
            $insertData[] = [
                'tconst' => trim($record['tconst']),
                'title_type' => trim($record['titleType']),
                'primary_title' => trim($record['primaryTitle']),
                'runtime_minutes' => trim($record['runtimeMinutes']),
                'genres' => trim($record['genres']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Movie::insert($insertData);
    }
}
