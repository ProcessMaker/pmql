<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class CreateTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_records', function ($table) {
            $table->increments('id');
            $table->json('data');
            $table->timestamps();
        });

        $now = Carbon::now();

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'data' => json_encode([
                'first_name' => 'Taylor',
                'last_name' => 'Dondich',
                'age' => '37',
                'dob' => '1981-06-02',
            ]),
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'data' => json_encode([
                'first_name' => 'Alan',
                'last_name' => 'Bollinger',
                'age' => '36',
                'dob' => '1982-06-02',
            ]),
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'data' => json_encode([
                'first_name' => 'Mila',
                'last_name' => 'Endo',
                // Sorry Mila, for now you're 35
                'age' => '35',
                'dob' => '1983-06-02',

            ]),
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'data' => json_encode([
                'first_name' => 'Ryan',
                'last_name' => 'Cooley',
                'age' => '34',
                'dob' => '1984-06-02',

            ]),
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'data' => json_encode([
                'first_name' => 'Nolan',
                'last_name' => 'Ehrstrom',
                'age' => '33',
                'dob' => '1985-06-02',
            ]),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('test_records');
    }
}
