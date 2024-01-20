from playwright.sync_api import sync_playwright

# URL de la page et sélecteur de l'élément à trouver
url = 'https://www.airbnb.fr/rooms/838191105746965572?adults=1&category_tag=Tag%3A677&enable_m3_private_room=true&photo_id=1598525445&source_impression_id=p3_1705693329_vxfmtJvFyHo%2FSCKO&previous_page_section_name=1000&federated_search_id=ea2f69b4-8233-49bc-8ae6-523a46573ffb&guests=1&check_in=2024-01-20#availability-calendar'  # Remplacez par l'URL réelle de votre page
testid_selector = 'div[data-testid="availability-calendar-date-range"]'

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False , slow_mo=00)
    page = browser.new_page()
    page.goto(url)
    if page.is_visible(testid_selector):
        # Récupérez et imprimez le contenu textuel de l'élément
        content = page.text_content(testid_selector)
        print(content)
    else:
        print("Élément non trouvé.")

    browser.close()
