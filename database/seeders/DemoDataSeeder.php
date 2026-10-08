<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Like;
use App\Models\Location;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo data is only available in local and testing environments.');
        }

        $image = 'pets/demo/dog.jpg';
        $disk = Storage::disk('public');
        if (! $disk->exists($image)) {
            $source = file_get_contents(public_path('assets/images/gallery/petspat-banner-orange-dog.jpg'));
            if ($source === false || ! $disk->put($image, $source)) {
                throw new RuntimeException('Unable to prepare the demo dog photo.');
            }
        }

        Model::withoutEvents(function () use ($image): void {
            DB::transaction(function () use ($image): void {
                $password = Hash::make('PetmingleDemo!2026');
                $names = ['Milo', 'Luna', 'Simba', 'Nala', 'Rocky', 'Bella', 'Oscar', 'Ruby', 'Toby', 'Cleo', 'Leo', 'Chloe'];
                $cities = [
                    'marrakech' => ['Marrakech', 31.6295, -7.9811],
                    'casablanca' => ['Casablanca', 33.5731, -7.5898],
                    'agadir' => ['Agadir', 30.4278, -9.5981],
                ];

                foreach ($cities as $slug => [$label, $latitude, $longitude]) {
                    $users = [];
                    $pets = [];
                    for ($index = 0; $index < 12; $index++) {
                        $email = $index === 0 ? "demo.{$slug}@petmingle.test" : "demo.{$slug}.{$index}@petmingle.test";
                        // Deleted demo records remain deleted; existing accounts are never overwritten.
                        $user = User::withTrashed()->firstOrCreate(['email' => $email], [
                            'name' => $index === 0 ? "Demo {$label}" : "Voisin {$label} {$index}",
                            'password' => $password,
                            'is_admin' => false,
                        ]);
                        if ($user->trashed()) {
                            continue;
                        }

                        $cat = $index % 3 === 2;
                        $species = Species::firstOrCreate(['name' => $cat ? 'Cats' : 'Dogs'], ['description' => $cat ? 'Cats' : 'Dogs']);
                        $race = Race::firstOrCreate([
                            'species_id' => $species->getKey(),
                            'name' => $cat ? 'Domestic Shorthair' : 'Golden Retriever',
                        ]);
                        $pet = Pet::withTrashed()->firstOrCreate(['user_id' => $user->getKey()], [
                            'species_id' => $species->getKey(),
                            'race_id' => $race->getKey(),
                            'name' => $names[$index],
                            'age' => 1 + $index % 7,
                            'sexe' => $index % 2,
                            'color' => $cat ? 'Grey' : 'Golden',
                            'images' => $cat ? [] : [$image],
                            'about' => "Profil de démonstration à {$label}. Partant pour une promenade et de nouvelles rencontres !",
                        ]);
                        if ($pet->trashed()) {
                            continue;
                        }

                        Location::withTrashed()->firstOrCreate(['user_id' => $user->getKey()], [
                            'latitude' => $latitude + ($index % 4) * 0.006,
                            'longitude' => $longitude + intdiv($index, 4) * 0.007,
                            'label' => "{$label}, Maroc",
                        ]);
                        $users[$index] = $user;
                        $pets[$index] = $pet;
                    }

                    if (! isset($users[0], $pets[0])) {
                        continue;
                    }
                    foreach ([1, 3, 4] as $index) {
                        if (! isset($users[$index], $pets[$index])) {
                            continue;
                        }
                        foreach ([[0, $index], [$index, 0]] as [$from, $to]) {
                            Like::withTrashed()->firstOrCreate(['from' => $pets[$from]->getKey(), 'to' => $pets[$to]->getKey()]);
                            MatchTable::withTrashed()->firstOrCreate(['from' => $pets[$from]->getKey(), 'to' => $pets[$to]->getKey()]);
                        }
                        $conversation = Conversation::withTrashed()->firstOrCreate([
                            'first_user_id' => $users[0]->getKey(),
                            'seconde_user_id' => $users[$index]->getKey(),
                        ]);
                        if ($conversation->trashed()) {
                            continue;
                        }
                        foreach (['Bonjour ! Une promenade ce week-end ?', 'Avec plaisir ! On se retrouve au parc ?', 'Parfait, à bientôt !'] as $position => $content) {
                            $sender = $position === 1 ? 0 : $index;
                            $receiver = $position === 1 ? $index : 0;
                            Message::withTrashed()->firstOrCreate([
                                'conversation_id' => $conversation->getKey(),
                                'sender_id' => $users[$sender]->getKey(),
                                'receiver_id' => $users[$receiver]->getKey(),
                                'content' => $content,
                            ], ['is_seen' => $position !== 2]);
                        }
                    }
                    foreach ([[5, 0], [6, 0], [0, 7]] as [$from, $to]) {
                        if (isset($pets[$from], $pets[$to])) {
                            Like::withTrashed()->firstOrCreate(['from' => $pets[$from]->getKey(), 'to' => $pets[$to]->getKey()]);
                        }
                    }
                }
            });
        });
    }
}
