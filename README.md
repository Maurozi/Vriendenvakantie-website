# Het Vriendenvakantie Archief - Technisch Ontwerpplan (Laravel Editie)

Dit document dient als de centrale README voor het "Vriendenvakantie Archief" project. Het beschrijft de volledige technische architectuur en workflow voor het bouwen van een robuuste, afgeschermde galerij-website met behulp van het Laravel (PHP) framework en AWS S3. 

## 1. Doel van het Project
Het veilig en kostenefficiënt hosten van 40 jaar aan foto's en video's (3-5 TB) van een hechte vriendengroep. De website is **volledig afgeschermd** van het publieke internet. Toegang wordt exclusief verleend via een gedeelde sleutel (voor gasten) of via persoonlijke beheerdersaccounts (voor de vriendengroep).

## 2. Gebruikte Tech Stack (VILT Stack & AWS)

### Frontend
* **Framework:** **Vue.js** gekoppeld via **Inertia.js**. Dit combineert de snelheid van een single-page application met de eenvoud van server-side routing, zonder dat er een losse API gebouwd hoeft te worden.

### Backend & Database (Laravel)
* **Backend:** **PHP met het Laravel Framework**. Fungeert als de absolute poortwachter en regelt alle logica, authenticatie en communicatie met de database en opslag.
* **Database:** **MySQL of PostgreSQL (SQL)**. Beheerd via Laravel's Eloquent ORM voor het vastleggen van strakke relaties tussen vakanties en media.
* **Beveiliging:** **Laravel Middleware & Policies** dwingen de autorisatie af voordat data de server verlaat.

### Opslag
* **Media Opslag:** **AWS S3 (Amazon Simple Storage Service)**. Gekoppeld via de ingebouwde Laravel Storage Facade voor de kostenefficiënte en veilige opslag van de 3-5 TB aan zware `.jpg`, `.webp` en `.mp4` bestanden.

## 3. Workflows & Beveiligingsmodel
De applicatie kent drie toegangsniveaus, beheerd door Laravel Middleware.

### A. De Onbekende Bezoeker (Geen Toegang)
* **Toegang:** Geen. Alle routes in Laravel zitten achter een authenticatie-muur.
* **Weergave:** De bezoeker ziet uitsluitend een landingspagina met één invoerveld: "Voer de geheime sleutel in" of een verborgen login-route voor admins.

### B. De Gast (Leestoegang via Sleutel)
Mensen buiten de directe vriendengroep met wie de link en de sleutel gedeeld wordt.
1. **Login Proces:** De gast vult de geheime sleutel in op de landingspagina.
2. **Achter de schermen:** Laravel controleert de sleutel. Bij succes wordt er een `is_guest` status weggeschreven in de PHP-sessie.
3. **Rechten:** Een speciale `GuestMiddleware` leest de sessie uit en geeft `read`-rechten op de media-routes. De gast kan de hele galerij bekijken, maar knoppen voor het toevoegen of verwijderen van data worden niet gerenderd.

### C. De Vriend (Admin Toegang)
De kerngroep die de galerij beheert.
1. **Login Proces:** De vriend logt in via een beheerdersportaal (bijv. gebouwd met Laravel Breeze) via een persoonlijk account of Google OAuth (via Laravel Socialite).
2. **Autorisatie:** Het Laravel User model koppelt de rol 'admin' aan deze specifieke accounts.
3. **Rechten:** Laravel Policies (bijv. `VacationPolicy`) verifiëren de admin-rol en geven volledige `read`, `create`, `update` en `delete` rechten. Het volledige beheerpaneel is nu toegankelijk.

## 4. Databasestructuur (SQL & Eloquent)
In plaats van documenten werken we met relationele tabellen in SQL. Laravel Eloquent haalt gerelateerde data efficiënt op.

**Tabel: `vacations`**
* `id` (Primary Key)
* `year` (Integer)
* `location` (String)
* `title` (String)
* `description` (Text)

**Tabel: `media`**
* `id` (Primary Key)
* `vacation_id` (Foreign Key -> references id on vacations)
* `type` (Enum: 'photo', 'video')
* `file_path` (String: Pad in de AWS S3 bucket)
* `timestamp` (DateTime)

*Gouden regel voor de frontend:* De backend maakt gebruik van Laravel's ingebouwde `$vacation->media()->paginate(50)` functionaliteit (infinite scroll in Vue) om te voorkomen dat er tienduizenden rijen tegelijk in het geheugen worden geladen.

## 5. Media Ingestie & Optimalisatie
Voor de initiële lading van 3-5 TB aan archiefmateriaal:
1. **Compressie:** Lokale verwerking (compressie naar WebP/MP4) is nog steeds sterk aanbevolen.
2. **Upload & Ingestie:** Uploaden kan handmatig via het Vue Admin Paneel. Voor de grote initiële lading kan een Laravel Artisan Command (een server-side PHP script) geschreven worden dat een lokale map uitleest en alles bulksgewijs naar de database en AWS S3 pusht.
3. **Thumbnails:** Bij het uploaden kan een Laravel package (zoals *Intervention Image*) automatisch kleine thumbnails genereren en opslaan in S3, wat essentieel is voor snelle laadtijden in de overzichtspagina's.