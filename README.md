# Het Vriendenvakantie Archief - Technisch Ontwerpplan
Dit document dient als de centrale README voor het "Vriendenvakantie Archief" project. Het beschrijft de volledige technische architectuur en workflow voor het bouwen van een robuuste, afgeschermde en serverless galerij-website met behulp van Google Cloud en Firebase.
## 1. Doel van het Project
Het veilig en kostenefficiënt hosten van 40 jaar aan foto's en video's (3-5 TB) van een hechte vriendengroep. De website is **volledig afgeschermd** van het publieke internet. Toegang wordt exclusief verleend via een gedeelde sleutel (voor gasten) of via persoonlijke Google-accounts (voor beheerders binnen de vriendengroep).
## 2. Gebruikte Tech Stack (Serverless)
### Frontend
 * **Framework:** React / Next.js of Vue.js
 * **Hosting:** **Firebase Hosting** (gratis, snel via CDN, en direct gekoppeld aan de backend).
### Database & Opslag (Firebase)
 * **Database:** **Firestore (NoSQL)** voor het opslaan van de metadata (jaartallen, locaties, bestand-URL's en tags). Werkt op basis van snelle documenten en subcollecties.
 * **Opslag:** **Google Cloud Storage (Firebase Storage)** voor het veilig hosten van de daadwerkelijke .jpg, .webp en zware .mp4 videobestanden.
 * **Beveiliging:** **Firebase Security Rules** dwingen de autorisatie direct op database-niveau af, wat een aparte backend overbodig maakt.
### Authenticatie (Firebase Auth)
 * **Email & Wachtwoord Login:** Gebruikt voor het 'Gedeelde Gast-Account' (de geheime toegangssleutel).
 * **Google Login:** Gebruikt voor de beheerdersrechten (de originele vriendengroep).
## 3. Workflows & Beveiligingsmodel
De applicatie kent drie toegangsniveaus, waarbij de hoogste niveaus overschrijven wat de lagere mogen.
### A. De Onbekende Bezoeker (Geen Toegang)
 * **Toegang:** Geen. Het publieke internet kan de database niet benaderen.
 * **Weergave:** De bezoeker ziet uitsluitend een landingspagina met één invoerveld: "Voer de geheime sleutel in".
### B. De Gast (Leestoegang via Sleutel)
Mensen buiten de directe vriendengroep met wie de link en de sleutel gedeeld wordt.
 1. **Login Proces:** De gast vult de geheime sleutel in op de landingspagina.
 2. **Achter de schermen:** De frontend logt op de achtergrond in bij Firebase Auth via een onzichtbaar algemeen account (bijv. gast@vriendenvakantie.nl) met de ingevulde sleutel als wachtwoord.
 3. **Rechten:** De gast krijgt een geldige Firebase sessie. De Security Rules geven nu read-rechten vrij. De gast kan de hele galerij bekijken, maar de admin-functies blijven verborgen.
### C. De Vriend (Admin Toegang via Google)
De kerngroep die de galerij beheert.
 1. **Login Proces:** De vriend klikt op de verborgen "Admin Login" knop en logt in met zijn persoonlijke Google-account (Gmail).
 2. **Autorisatie:** De Firebase Security Rules controleren of het e-mailadres in de *hardcoded* whitelist staat (bijv. in een isVriend() functie in de regels).
 3. **Rechten:** De vriend heeft nu read, create, update en delete rechten op de gehele database. In de frontend (het ingebouwde Admin Paneel) verschijnen de knoppen om nieuwe vakanties aan te maken en media te uploaden/verwijderen.
## 4. Databasestructuur (Firestore)
Om kosten te minimaliseren en laadtijden te optimaliseren, wordt er gebruik gemaakt van subcollecties.
```json
// Collectie: vacations
{
  "1998_italie": {
    "year": 1998,
    "location": "Toscane, Italië",
    "title": "Zomervakantie 1998",
    // Subcollectie: media (Pas ingeladen wanneer gebruiker de vakantie opent)
    "media": [
      { "type": "photo", "url": "...", "timestamp": "..." },
      { "type": "video", "url": "...", "timestamp": "..." }
    ]
  }
}

```
*Gouden regel voor de frontend:* De UI maakt gebruik van **paginatie** (infinite scroll) om te voorkomen dat duizenden documenten tegelijkertijd worden opgehaald, wat database-reads bespaart.
## 5. Media Ingestie & Optimalisatie
Voor de initiële lading van 3-5 TB aan archiefmateriaal:
 1. Lokale verwerking (compressie naar WebP/MP4) is sterk aanbevolen om opslagkosten te verlagen.
 2. Initiele upload kan via een Python-script (firebase-admin SDK) of handmatig via het nieuw te bouwen Admin Paneel door de vriendengroep.
 3. Firebase Extensions (bijv. *Resize Images*) kunnen worden ingezet om automatisch kleine thumbnails te genereren voor snellere laadtijden van het overzicht.
