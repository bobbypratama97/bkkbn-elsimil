<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configs = [
            [
                'code' => 'role_child_5',
                'value' => '1',
                'name' => 'Petugas KB',
                'description' => '-'
            ],
            [
                'code' => 'role_child_5',
                'value' => '2',
                'name' => 'Petugas PKK',
                'description' => '-'
            ],
            [
                'code' => 'role_child_5',
                'value' => '3',
                'name' => 'Petugas Bidan',
                'description' => '-'
            ]
        ];
        
        DB::table('configs')->insert($configs);
    }
}
