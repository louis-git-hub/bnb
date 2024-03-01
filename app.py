import sqlite3
from flask import Flask

app = Flask(__name__)

def init_db():
    with sqlite3.connect('refresh.db') as conn:
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
        conn.commit()

@app.route('/api/<id_bnb>/<int:days>')
def get_data(id_bnb, days):
    with sqlite3.connect('refresh.db') as conn:  # Crée une nouvelle connexion pour chaque requête
        cursor = conn.cursor()
        cursor.execute('INSERT INTO refresh (id_bnb, days, etat) VALUES (?, ?, ?)', (id_bnb, days, 0))
        conn.commit()  # Les modifications sont automatiquement sauvegardées
    return f"{id_bnb},{days}"

if __name__ == '__main__':
    app.run(debug=False)
