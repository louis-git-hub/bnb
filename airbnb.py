import csv
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright
import argparse
import re  # Assurez-vous d'importer le module re pour extract_number_from_string

# Initialisation des arguments du script
parser = argparse.ArgumentParser(description='Traite les variables DAYS et ID.')
parser.add_argument('--days', type=int, default=7, help='Nombre de jours')
parser.add_argument('--id', type=str, required=True, help='Identifiant unique')
parser.add_argument('--idr', type=str, required=True, help='Identifiant unique')
args = parser.parse_args()

IDR = args.idr
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
filename_csv = f"{IDR}_{ID}_{DAYS}_{today_str}.csv"

# Ouverture du fichier CSV pour écriture
with open(filename_csv, mode='w', newline='', encoding='utf-8') as file:
    writer = csv.writer(file)
    # En-têtes du CSV
    writer.writerow(['Prix', 'Date_prix', 'Durée Minimale'])

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False, slow_mo=2000)
        page = browser.new_page()
        try:
            page.goto(URL, wait_until='networkidle')
            if page.is_visible(close_button_selector):
                page.click(close_button_selector)
            for i in range(DAYS):
                page.click(fermer)
                target_date = datetime.now() + timedelta(days=i)
                target_date_str = target_date.strftime("%d/%m/%Y")
                if page.is_visible(f'[data-testid="calendar-day-{target_date_str}"]') and \
                page.is_enabled(f'[data-testid="calendar-day-{target_date_str}"]'):
                    page.click(f'[data-testid="calendar-day-{target_date_str}"]')
                    page.wait_for_timeout(2000)
                else:
                    writer.writerow(["Bloqué", target_date_str, 0])
                    continue
                nombre = page.inner_text(testid_selector)
                b_nombre = extract_number_from_string(nombre) if nombre else 0
                a_nombre = b_nombre if b_nombre > 0 else 1
                tomorrow_date = target_date + timedelta(days=a_nombre)
                tomorrow_date_str = tomorrow_date.strftime("%d/%m/%Y")
                if page.is_visible(f'[data-testid="calendar-day-{tomorrow_date_str}"]') and \
                page.is_enabled(f'[data-testid="calendar-day-{tomorrow_date_str}"]'):
                    page.click(f'[data-testid="calendar-day-{tomorrow_date_str}"]')
                else:
                    writer.writerow(["Bloqué", target_date_str, 0])
                    continue
                section_element = page.query_selector(ppp)
                if section_element:
                    price_element = section_element.query_selector(tot)
                    if price_element:
                        price_text = extract_number_from_string(price_element.inner_text())
                        if b_nombre > 0:
                            price_sql = price_text / b_nombre
                        else:
                            price_sql = price_text
                        writer.writerow([price_sql, target_date_str, b_nombre])
                    else:
                        writer.writerow(["Bloqué", target_date_str, 0])
                        continue
                else:
                    writer.writerow(["Bloqué", target_date_str, 0])
                    continue
        finally:
            browser.close()
