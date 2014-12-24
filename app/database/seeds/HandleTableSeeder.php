<?php

class HandleTableSeeder extends Seeder {

    public function run()
    {
        DB::table('handles')->delete();

        Handle::create(array(
            'name' => 'tom',
            'user_id' => 1
        ));

        Handle::create(array(
            'name' => 'jane',
            'user_id' => 2
        ));

        Handle::create(array(
            'name' => 'bob',
            'user_id' => 3
        ));

    }

}