<?php

namespace Database\Seeders;

use App\Models\GameTag;
use App\Models\HelpContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Desenvolvimento',
            'email' => 'dev@dev',
            'password' => Hash::make('p1paw3b'),
            'is_admin' => true,
        ]);

        // Criar tags de jogos
        $tags = [
            'Educativo',
            'Quebra-cabeça',
            'Aventura',
            'Estratégia',
            'Simulação',
            'RPG',
            '1 Jogador',
            '2 Jogadores',
            '3 Jogadores',
            '4 Jogadores',
            '5 Jogadores',
            '5 Jogadores ou mais',
        ];

        foreach ($tags as $tag) {
            GameTag::create([
                'name' => $tag,
                'slug' => \Illuminate\Support\Str::slug($tag),
            ]);
        }
    }
}