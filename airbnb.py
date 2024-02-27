import re
import sqlite3
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright

DAYS = 30
BASE = "https://www.airbnb.fr/rooms/"
ID = "849164886478265494"
URL = BASE + ID
close_button_selector = '[aria-label="Fermer"]'
testid_selector = 'div[data-testid="availability-calendar-date-range"]'
fermer = 'button:has-text("Effacer les dates")'
ppp = 'div[data-section-id="BOOK_IT_SIDEBAR"] section'
tot = 'div:has-text("Total")'
price_sql = 0

def extract_number_from_string(text):
    numbers = re.findall(r'\d+', text)
    return int(numbers[0]) if numbers else 0

conn = sqlite3.connect(f'{ID}.db')
cursor = conn.cursor()

cursor.execute('''
    CREATE TABLE IF NOT EXISTS prices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        date_relevage DATE,
        date_prix DATE,
        prix INTEGER
    )
''')
conn.commit()

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

        if page.is_visible(f'[data-testid="calendar-day-{target_date_str}"]') and \
           page.is_enabled(f'[data-testid="calendar-day-{target_date_str}"]'):
            page.click(f'[data-testid="calendar-day-{target_date_str}"]')
            page.wait_for_timeout(2000)
        else:
            cursor.execute('INSERT INTO prices (date_relevage, date_prix, prix) VALUES (?, ?, ?)',
                         (datetime.now().strftime("%d/%m/%Y"), target_date_str, "Bloqué"))
            conn.commit()
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
            cursor.execute('INSERT INTO prices (date_relevage, date_prix, prix) VALUES (?, ?, ?)',
                         (datetime.now().strftime("%d/%m/%Y"), target_date_str, "Bloqué"))
            conn.commit()
            continue

        section_element = page.query_selector(ppp)
        if section_element:
            price_element = section_element.query_selector(tot)
            if price_element:
                price_text = extract_number_from_string(price_element.inner_text())
            else:
                cursor.execute('INSERT INTO prices (date_relevage, date_prix, prix) VALUES (?, ?, ?)',
                (datetime.now().strftime("%d/%m/%Y"), target_date_str, "Bloqué"))
        else:
            continue:

        if b_nombre > 0:
            price_sql = price_text / b_nombre
            cursor.execute('INSERT INTO prices (date_relevage, date_prix, prix) VALUES (?, ?, ?)',
                                (datetime.now().strftime("%d/%m/%Y"), target_date_str, price_sql))
            conn.commit()
        else:
            cursor.execute('INSERT INTO prices (date_relevage, date_prix, prix) VALUES (?, ?, ?)',
                                (datetime.now().strftime("%d/%m/%Y"), target_date_str, price_text))
            conn.commit()

    browser.close()

conn.close()
