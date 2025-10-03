<?php

namespace Database\Seeders;

use App\Models\Notebook;
use Illuminate\Database\Seeder;

class NotebookSeeder extends Seeder
{
    public function run()
    {
        $notebooks = [
            [
                'identificador' => 'pipa-notebook-01',
                'usuario_atual' => 'aluno.010101',
                'status' => 'online',
                'ip_address' => '192.168.1.101',
                'hostname' => 'LAB01-PC01',
                'ultimo_login' => now()->subMinutes(15),
                'ultimo_heartbeat' => now()->subSeconds(30),
            ],
            [
                'identificador' => 'pipa-notebook-02',
                'usuario_atual' => 'aluno.020202',
                'status' => 'online',
                'ip_address' => '192.168.1.102',
                'hostname' => 'LAB01-PC02',
                'ultimo_login' => now()->subMinutes(20),
                'ultimo_heartbeat' => now()->subSeconds(45),
            ],
            [
                'identificador' => 'pipa-notebook-03',
                'usuario_atual' => 'prof.matematica',
                'status' => 'online',
                'ip_address' => '192.168.1.103',
                'hostname' => 'SALA01-PC01',
                'ultimo_login' => now()->subMinutes(5),
                'ultimo_heartbeat' => now()->subSeconds(10),
            ],
            [
                'identificador' => 'pipa-notebook-04',
                'usuario_atual' => 'aluno.030303',
                'status' => 'offline',
                'ip_address' => '192.168.1.104',
                'hostname' => 'LAB02-PC01',
                'ultimo_login' => now()->subHours(2),
                'ultimo_heartbeat' => now()->subHours(1),
            ],
            [
                'identificador' => 'pipa-notebook-05',
                'usuario_atual' => 'aluno.040404',
                'status' => 'online',
                'ip_address' => '192.168.1.105',
                'hostname' => 'LAB02-PC02',
                'ultimo_login' => now()->subMinutes(35),
                'ultimo_heartbeat' => now()->subMinutes(2),
            ],
            [
                'identificador' => 'pipa-notebook-06',
                'usuario_atual' => null,
                'status' => 'offline',
                'ip_address' => '192.168.1.106',
                'hostname' => 'LAB03-PC01',
                'ultimo_login' => now()->subDays(1),
                'ultimo_heartbeat' => now()->subDays(1),
            ],
            [
                'identificador' => 'pipa-notebook-07',
                'usuario_atual' => 'admin.escola',
                'status' => 'online',
                'ip_address' => '192.168.1.107',
                'hostname' => 'ADMIN-PC01',
                'ultimo_login' => now()->subMinutes(10),
                'ultimo_heartbeat' => now()->subSeconds(20),
            ],
            [
                'identificador' => 'pipa-notebook-08',
                'usuario_atual' => 'aluno.050505',
                'status' => 'online',
                'ip_address' => '192.168.1.108',
                'hostname' => 'LAB03-PC02',
                'ultimo_login' => now()->subMinutes(25),
                'ultimo_heartbeat' => now()->subSeconds(55),
            ],
            [
                'identificador' => 'pipa-notebook-09',
                'usuario_atual' => 'aluno.060606',
                'status' => 'offline',
                'ip_address' => '192.168.1.109',
                'hostname' => 'BIBLIO-PC01',
                'ultimo_login' => now()->subHours(3),
                'ultimo_heartbeat' => now()->subHours(2),
            ],
            [
                'identificador' => 'pipa-notebook-10',
                'usuario_atual' => 'prof.portugues',
                'status' => 'online',
                'ip_address' => '192.168.1.110',
                'hostname' => 'SALA02-PC01',
                'ultimo_login' => now()->subMinutes(8),
                'ultimo_heartbeat' => now()->subSeconds(25),
            ],
            [
                'identificador' => 'pipa-notebook-11',
                'usuario_atual' => 'aluno.070707',
                'status' => 'online',
                'ip_address' => '192.168.1.111',
                'hostname' => 'LAB04-PC01',
                'ultimo_login' => now()->subMinutes(40),
                'ultimo_heartbeat' => now()->subMinutes(1),
            ],
            [
                'identificador' => 'pipa-notebook-12',
                'usuario_atual' => null,
                'status' => 'offline',
                'ip_address' => '192.168.1.112',
                'hostname' => 'LAB04-PC02',
                'ultimo_login' => now()->subDays(2),
                'ultimo_heartbeat' => now()->subDays(2),
            ],
            [
                'identificador' => 'pipa-notebook-13',
                'usuario_atual' => 'aluno.080808',
                'status' => 'online',
                'ip_address' => '192.168.1.113',
                'hostname' => 'LAB05-PC01',
                'ultimo_login' => now()->subMinutes(18),
                'ultimo_heartbeat' => now()->subSeconds(40),
            ],
            [
                'identificador' => 'pipa-notebook-14',
                'usuario_atual' => 'coord.pedagogico',
                'status' => 'online',
                'ip_address' => '192.168.1.114',
                'hostname' => 'COORD-PC01',
                'ultimo_login' => now()->subMinutes(3),
                'ultimo_heartbeat' => now()->subSeconds(15),
            ],
            [
                'identificador' => 'pipa-notebook-15',
                'usuario_atual' => 'aluno.090909',
                'status' => 'offline',
                'ip_address' => '192.168.1.115',
                'hostname' => 'LAB05-PC02',
                'ultimo_login' => now()->subHours(5),
                'ultimo_heartbeat' => now()->subHours(4),
            ],
            [
                'identificador' => 'pipa-notebook-16',
                'usuario_atual' => 'prof.ciencias',
                'status' => 'online',
                'ip_address' => '192.168.1.116',
                'hostname' => 'SALA03-PC01',
                'ultimo_login' => now()->subMinutes(12),
                'ultimo_heartbeat' => now()->subSeconds(35),
            ]
        ];

        foreach ($notebooks as $notebookData) {
            Notebook::create($notebookData);
        }

        $this->command->info('16 notebooks criados com sucesso!');
    }
}