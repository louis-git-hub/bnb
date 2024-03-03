import sqlite3
import subprocess
import time
import requests
from datetime import datetime

# Chemin vers le script secondaire que vous souhaitez exécuter
script_path = 'airbnb.py'
webhook_url = 'http://localhost:5000/webhook/'

# Connexion à la base de données
conn = sqlite3.connect('refresh.db')
cursor = conn.cursor()

# def get_current_datetime():
#     # Récupérer la date et l'heure actuelles
#     current_datetime = datetime.now()
#     # Formater la date et l'heure actuelles pour inclure les secondes
#     formatted_datetime = current_datetime.strftime('%Y-%m-%d %H:%M:%S')
#     return formatted_datetime

# Création de la table si elle n'existe pas (pas de nouvelle insertion ici pour éviter les doublons)
cursor.execute('''
    CREATE TABLE IF NOT EXISTS refresh (
        id INTEGER PRIMARY KEY,
        id_bnb TEXT,
        duree INTEGER,
        etat INTEGER,
        date_d DATETIME,
        date_f DATETIME,
        days INTEGER
    )
''')
# Commencez la boucle infinie pour exécuter le script toutes les 30 secondes
while True:
    # Sélection des lignes où etat = 0
    cursor.execute('SELECT id_bnb, days, id FROM refresh WHERE etat = 0')
    rows = cursor.fetchall()
    print(rows)

    for id_bnb, days, id in rows:
        # Construisez la commande comme avant
        command = ["python", "airbnb.py", "--idr", str(id), "--id", str(id_bnb), "--days", str(days)]
        webhook_url = 'http://sonarbnb.com/scrap/{}'.format(id)
        webhook_url_error = 'http://sonarbnb.com/scrap/error/{}'.format(id)
        print(webhook_url)


        date_d = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute('UPDATE refresh SET date_d = ? WHERE id = ?', (date_d, id))
        cursor.execute('UPDATE refresh SET etat = 1 WHERE id = ?', (id,))
        conn.commit()
        result = subprocess.run(command)

        if result.returncode == 0:
            date_f = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            cursor.execute('UPDATE refresh SET etat = 2 WHERE id = ?', (id,))
            cursor.execute('UPDATE refresh SET date_f = ? WHERE id = ?', (date_f, id))
            response = requests.get(webhook_url)
        else:
            date_f = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            cursor.execute('UPDATE refresh SET etat = 3 WHERE id = ?', (id,))
            cursor.execute('UPDATE refresh SET date_f = ? WHERE id = ?', (date_f, id))
            response = requests.get(webhook_url_error)
        conn.commit()


    time.sleep(10)
conn.commit()
cursor.close()
conn.close()
