<?php

use App\Models\Players;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return integer
     */
    public function run()
    {
        $names = [
            'Quon Tama',
            '88nia',
            'Amatsuka Seu',
            'Yumemi',
            'TenoMage',
            'Higure',
            'Uten Hiyori',
            'Leona Shishigami',
        ];

        $max = count($names);

        $me = new Players;
        $me->name = "Cyan";
        $me->description = "ME";
        $me->email = "qazs0205@gmail.com";
        $me->trip_id = 1;
        $me->user_id = 1;
        $me->save();

        for( $index=0; $index<$max; $index++ ) {
            $player = new Players;

            $player->name = $names[$index];
            $player->description = str_random(10);
            $player->email = ($index+1)."@123.com";
            $player->trip_id = 1;

            $player->save();
        }

        return $max;
    }
}
