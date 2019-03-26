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
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });
    
        $now = Carbon::now();
        
        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'first_name' => 'Taylor',
            'last_name' => 'Dondich'
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'first_name' => 'Alan',
            'last_name' => 'Bollinger'
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'first_name' => 'Mila',
            'last_name' => 'Endo'
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'first_name' => 'Ryan',
            'last_name' => 'Cooley'
        ]);

        DB::table('test_records')->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'first_name' => 'Nolan',
            'last_name' => 'Ehrstrom'
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