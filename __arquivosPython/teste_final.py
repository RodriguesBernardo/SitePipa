import requests
import json

# URLs CORRETAS - baseadas no routes/api.php
url_base = "http://localhost:8000/api"
url_test = f"{url_base}/test"
url_login = f"{url_base}/notebook/login"
url_comandos = f"{url_base}/notebook/pipa-notebook-01/comandos"

headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
}

print("=" * 60)
print("TESTE COMPLETO DA API NOTEBOOK")
print("=" * 60)

# 1. Teste da rota básica
print("\n1. 🧪 Testando rota básica...")
try:
    response = requests.get(url_test, headers=headers)
    print(f"   URL: {url_test}")
    print(f"   Status: {response.status_code}")
    print(f"   Resposta: {response.text}")
except Exception as e:
    print(f"   ❌ Erro: {e}")

# 2. Teste do login
print("\n2. 🔐 Testando login do notebook...")
dados_login = {
    "notebook_id": "pipa-notebook-01",
    "usuario": "teste.010101",
    "ip_address": "192.168.1.100",
    "fonte_deteccao": "teste_python",
    "hostname": "teste-pc"
}

try:
    response = requests.post(url_login, json=dados_login, headers=headers)
    print(f"   URL: {url_login}")
    print(f"   Status: {response.status_code}")
    
    if response.status_code == 200:
        print("   ✅ Login realizado com sucesso!")
        resultado = response.json()
        print(f"   Resposta: {json.dumps(resultado, indent=2)}")
    elif response.status_code == 201:
        print("   ✅ Notebook criado e logado com sucesso!")
        resultado = response.json()
        print(f"   Resposta: {json.dumps(resultado, indent=2)}")
    else:
        print(f"   ❌ Erro no login: {response.status_code}")
        print(f"   Resposta: {response.text}")
        
except Exception as e:
    print(f"   ❌ Erro: {e}")

# 3. Teste de comandos
print("\n3. 📋 Testando busca de comandos...")
try:
    response = requests.get(url_comandos, headers=headers)
    print(f"   URL: {url_comandos}")
    print(f"   Status: {response.status_code}")
    
    if response.status_code == 200:
        print("   ✅ Comandos recuperados com sucesso!")
        comandos = response.json()
        print(f"   Comandos: {json.dumps(comandos, indent=2)}")
    elif response.status_code == 404:
        print("   ℹ️  Notebook não encontrado (primeiro acesso?)")
        print(f"   Resposta: {response.text}")
    else:
        print(f"   ⚠️  Status inesperado: {response.status_code}")
        print(f"   Resposta: {response.text}")
        
except Exception as e:
    print(f"   ❌ Erro: {e}")

# 4. Teste adicional: heartbeat
print("\n4. 💓 Testando heartbeat...")
url_heartbeat = f"{url_base}/notebook/heartbeat"
dados_heartbeat = {
    "notebook_id": "pipa-notebook-01",
    "status": "online",
    "usuario": "teste.010101"
}

try:
    response = requests.post(url_heartbeat, json=dados_heartbeat, headers=headers)
    print(f"   URL: {url_heartbeat}")
    print(f"   Status: {response.status_code}")
    
    if response.status_code == 200:
        print("   ✅ Heartbeat enviado com sucesso!")
        resultado = response.json()
        print(f"   Resposta: {json.dumps(resultado, indent=2)}")
    else:
        print(f"   ❌ Erro no heartbeat: {response.status_code}")
        print(f"   Resposta: {response.text}")
        
except Exception as e:
    print(f"   ❌ Erro: {e}")

print("\n" + "=" * 60)
print("FIM DO TESTE")
print("=" * 60)