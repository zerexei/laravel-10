<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\User::truncate();
        \App\Models\Room::truncate();
        \App\Models\RoomUser::truncate();
        \App\Models\Message::truncate();
        \App\Models\Team::truncate();
        DB::table('team_user')->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = \App\Models\User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'current_team_id' => 1,
        ]);
        $admin->teams()->attach([1]);

        $users = \App\Models\User::factory(10)->create([
            'current_team_id' => 1,
        ]);

        $users->each(fn ($user) => $user->teams()->attach([1]));

        $rooms = \App\Models\Room::factory(10)->create();

        $users->add($admin);

        $rooms->each(fn ($room) => $room->participants()->attach(
            $users->shuffle()->slice(0, rand(3, 5))->pluck('id')
        ));

        \App\Models\Message::factory(50)->create();
    }
}
