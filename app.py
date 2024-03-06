import sqlite3
from flask import Flask, send_from_directory, abort
import os
import glob

# Variables
# Remplacez ceci par le chemin de votre répertoire où les fichiers CSV sont stockés
CSV_FOLDER = '/home/py/scraper1/data'

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

# Get csv from id task
@app.route('/api/csv/<id>')
def download_csv(id):
    # Utilisez glob pour trouver des fichiers qui correspondent au modèle ID_*.csv
    pattern = os.path.join(CSV_FOLDER, f'{id}_*.csv')
    matching_files = glob.glob(pattern)
    
    # Vérifiez s'il y a au moins un fichier correspondant
    if not matching_files:
        # Si aucun fichier correspondant n'est trouvé, retournez une erreur 404
        abort(404)
    
    # Si des fichiers correspondants existent, prenez le premier (ou un autre critère spécifique si nécessaire)
    filename = os.path.basename(matching_files[0])
    
    # Retournez le fichier CSV
    return send_from_directory(CSV_FOLDER, filename, as_attachment=True)

if __name__ == '__main__':
    app.run(port=8089, debug=False)
