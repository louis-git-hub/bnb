import re
import sqlite3
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright
import random
import time

NUMBER_OF_DAYS = 36  # Nombre de jours à partir d'aujourd'hui pour récupérer les prix

BASE = "https://www.airbnb.fr/rooms/"
ID = "44188454"
URL = BASE + ID
temps_attente = random.uniform(1, 3)
testid_selector = 'div[data-testid="availability-calendar-date-range"]'
close_button_selector = '[aria-label="Fermer"]'
number = 0

def extract_number(text):
    numbers = re.findall(r'\d+', text)
    return int(numbers[0]) if numbers else 0

conn = sqlite3.connect(ID + '.db')
cursor = conn.cursor()

cursor.execute('''
    CREATE TABLE IF NOT EXISTS prices (
        date DATE,
        price INTEGER
    )
''')
conn.commit()

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False,)
    page = browser.new_page()
    try:
        page.goto(URL, wait_until='networkidle')

        if page.is_visible(close_button_selector):
            page.click(close_button_selector)

        for i in range(NUMBER_OF_DAYS):
            target_date = datetime.now() + timedelta(days=i)
            target_date_str = target_date.strftime("%d/%m/%Y")
            tomorrow_date = target_date + timedelta(days=1 + number)
            tomorrow_date_str = tomorrow_date.strftime("%d/%m/%Y")

            if page.is_visible(f'[data-testid="calendar-day-{target_date_str}"]') and \
               page.is_enabled(f'[data-testid="calendar-day-{target_date_str}"]'):
                page.click(f'[data-testid="calendar-day-{target_date_str}"]')
                time.sleep(temps_attente)

                if page.is_visible(testid_selector):
                    content = page.text_content(testid_selector)
                    time.sleep(temps_attente)
                    number = extract_number(content)
                else:
                    number = 0

                if page.is_visible(f'[data-testid="calendar-day-{tomorrow_date_str}"]') and \
                   page.is_enabled(f'[data-testid="calendar-day-{tomorrow_date_str}"]'):
                    page.click(f'[data-testid="calendar-day-{tomorrow_date_str}"]')

                    time.sleep(temps_attente)
                else:
                    print(f"bloqué sur la date {tomorrow_date_str}")
                    cursor.execute('INSERT INTO prices (date, price) VALUES (?, ?)', (target_date_str, "Bloqué"))
                    conn.commit()
                    continue

                section_selector = 'div[data-section-id="BOOK_IT_SIDEBAR"] section'
                section_element = page.wait_for_selector(section_selector, timeout=5000)
            else:
                print(f"bloqué sur la date {target_date_str}")
                cursor.execute('INSERT INTO prices (date, price) VALUES (?, ?)', (target_date_str, "Bloqué"))
                conn.commit()
                continue

            section_selector = 'div[data-section-id="BOOK_IT_SIDEBAR"] section'
            section_element = page.wait_for_selector(section_selector, timeout=5000)

            if section_element:
                price_element = section_element.query_selector('div:has-text("Total")')
                if price_element:
                    price_text = price_element.text_content()
                    price_number = extract_number(price_text)
                    print(price_text)

                    try:
                        result = price_number / number
                    except ZeroDivisionError:
                        result = price_number  # Ou une autre valeur ou action de votre choix

                    # Insérer les données dans la base de données
                    cursor.execute('INSERT INTO prices (date, price) VALUES (?, ?)', (target_date_str, result))
                    conn.commit()
                else:
                    print(f"Prix non trouvé pour {target_date_str}.")

    except Exception as e:
        print(f"Une erreur est survenue : {e}")
    finally:
        browser.close()

conn.close()