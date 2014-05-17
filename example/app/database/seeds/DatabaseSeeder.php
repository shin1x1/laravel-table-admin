<?php

use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            DB::table('riders')->truncate();
            DB::table('classes')->delete();
            DB::table('nationalities')->delete();

            Collection::make([
                ['id' => 1, 'name' => 'MotoGP'],
                ['id' => 2, 'name' => 'Moto2'],
                ['id' => 3, 'name' => 'Moto3'],
            ])->each(function($v) {
                    DB::table('classes')->insert($v);
                });

            Collection::make([
                ['id' => 1, 'name' => 'Spanish'],
                ['id' => 2, 'name' => 'Italian'],
            ])->each(function($v) {
                    DB::table('nationalities')->insert($v);
                });

            Collection::make([
                [
                    'class_id' => 1,
                    'nationality_id' => 1,
                    'no' =>'98',
                    'name' => 'Marc',
                ],
                [
                    'class_id' => 2,
                    'nationality_id' => 1,
                    'no' =>'40',
                    'name' => 'Pol',
                ],
                [
                    'class_id' => 3,
                    'nationality_id' => 1,
                    'no' =>'25',
                    'name' => 'Vinales',
                ],
                [
                    'class_id' => 1,
                    'nationality_id' => 2,
                    'no' =>'48',
                    'name' => 'Valentino',
                ],
            ])->each(function($v) {
                    DB::table('riders')->insert($v);
                });

        });
    }
}
