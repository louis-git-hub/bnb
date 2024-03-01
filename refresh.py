import sqlite3
import subprocess
import time


# Chemin vers le script secondaire que vous souhaitez exécuter
script_path = 'airbnb.py'

# Connexion à la base de données
conn = sqlite3.connect('refresh.db')
cursor = conn.cursor()

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
    cursor.execute('SELECT id_bnb, days FROM refresh WHERE etat = 0')
    rows = cursor.fetchall()
    print(rows)

    for id_bnb, days in rows:
        # Construisez la commande comme avant
        command = ["python", "airbnb.py", "--id", str(id_bnb), "--days", str(days)]
        
        # Exécutez la commande avec subprocess.run
        result = subprocess.run(command)

        if result.returncode == 0:
            cursor.execute('UPDATE refresh SET etat = 1 WHERE id_bnb = ?', (id_bnb,))
        else:
            cursor.execute('UPDATE refresh SET etat = 2 WHERE id_bnb = ?', (id_bnb,))
        conn.commit()


    time.sleep(20)
conn.commit()
cursor.close()
conn.close()