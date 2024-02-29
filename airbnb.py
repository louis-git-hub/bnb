import re
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright
import argparse
import csv  # Importez le module csv

# Configuration initiale
parser = argparse.ArgumentParser(description='Traite les variables DAYS et ID.')
parser.add_argument('--days', type=int, default=30, help='Nombre de jours')
parser.add_argument('--id', type=str, help='Identifiant unique')
args = parser.parse_args()

DAYS = args.days
ID = args.id
BASE = "https://www.airbnb.fr/rooms/"
URL = BASE + ID
close_button_selector = '[aria-label="Fermer"]'
testid_selector = 'div[data-testid="availability-calendar-date-range"]'
fermer = 'button:has-text("Effacer les dates")'
ppp = 'div[data-section-id="BOOK_IT_SIDEBAR"] section'
tot = 'div:has-text("Total")'

# Fonction pour extraire les nombres d'une chaîne
def extract_number_from_string(text):
    numbers = re.findall(r'\d+', text)
    return int(numbers[0]) if numbers else 0

# Préparation du nom du fichier CSV
today_str = datetime.now().strftime("%Y-%m-%d")
filename_csv = f"{ID}-{DAYS}-{today_str}.csv"

# Création et ouverture du fichier CSV
with open(filename_csv, mode='w', newline='') as file:
    writer = csv.writer(file)
    # En-têtes du CSV
    writer.writerow(['Date du Prix', 'Prix', 'Durée Minimale'])
    
    # Logique de Playwright pour parcourir le site et collecter les données
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False, slow_mo=2000)
        page = browser.new_page()
        page.goto(URL, wait_until='networkidle')
        if page.is_visible(close_button_selector):
            page.click(close_button_selector)
        for i in range(DAYS):
            page.click(fermer)
            target_date = datetime.now() + timedelta(days=i)
            target_date_str = target_date.strftime("%d/%m/%Y")
            if not page.is_visible(f'[data-testid="calendar-day-{target_date_str}"]') or \
               not page.is_enabled(f'[data-testid="calendar-day-{target_date_str}"]'):
                writer.writerow([target_date_str, "Bloqué", 0])
                continue  # Continue avec le prochain jour si la date est bloquée
            
            page.click(f'[data-testid="calendar-day-{target_date_str}"]')
            page.wait_for_timeout(2000)  # Attente pour que le calendrier se mette à jour

            nombre = page.inner_text(testid_selector)
            b_nombre = extract_number_from_string(nombre) if nombre else 0

            tomorrow_date = target_date + timedelta(days=b_nombre or 1)
            tomorrow_date_str = tomorrow_date.strftime("%d/%m/%Y")
            if not page.is_visible(f'[data-testid="calendar-day-{tomorrow_date_str}"]') or \
               not page.is_enabled(f'[data-testid="calendar-day-{tomorrow_date_str}"]'):
                writer.writerow([target_date_str, "Bloqué", b_nombre])
                continue

            page.click(f'[data-testid="calendar-day-{tomorrow_date_str}"]')
            page.wait_for_timeout(2000)  # Attente pour que la sélection de date se complète

            price = "Bloqué"  # Valeur par défaut si le prix n'est pas trouvé
            section_element = page.query_selector(ppp)
            if section_element:
                price_element = section_element.query_selector(tot)
                if price_element:
                    price_text = price_element.inner_text()
                    price = extract_number_from_string(price_text)
            
            writer.writerow([target_date_str, price, b_nombre])
        
        browser.close()
