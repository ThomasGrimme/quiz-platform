Installatie
Vereisten
Git

Docker & Docker Compose (aanbevolen)

OF: PHP 8.0+ + MySQL 8.0+

Stap 1: Repository klonen
Open je terminal en run:

Bash
git clone https://github.com/ThomasGrimme/quiz-platform.git
cd quiz-platform/Mijn-project
Stap 2: Starten met Docker (Aanbevolen)
Als je Docker hebt geïnstalleerd, hoef je alleen dit te runnen:

Bash
docker-compose up --build
Stap 3: Starten zonder Docker (Handmatig)
Als je geen Docker gebruikt is niet erg maar word wel geadviseerd, anders volg dan deze stappen:

Maak een MySQL database aan genaamd kayeet.

Pas je database credentials aan in app/php/bootstrap.php.

Start de PHP server met het volgende commando:
0
Bash
php -S localhost:8000 -t app/php/
4. Open [http://localhost:8080](http://localhost:8080) in je browser.

---

### Inloggegevens testaccount
Je kunt inloggen met de volgende gegevens:
- **Email:** `test@test.com`
- **Wachtwoord:** `Test1234!.`
- natuurlijk ook eigen account te maken
- inlogbaar en opslag van progressie
