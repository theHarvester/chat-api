<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create(array(
            'email' => 'tom@test.com',
            'password' => Hash::make('test')
        ));

        User::create(array(
            'email' => 'jane@test.com',
            'password' => Hash::make('test')
        ));

        User::create(array(
            'email' => 'bob@test.com',
            'password' => Hash::make('test')
        ));

    }

}