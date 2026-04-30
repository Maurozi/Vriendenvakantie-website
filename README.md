# Het Vriendenvakantie Archief

Dit document dient als de centrale README voor het project. Het beschrijft de volledige technische architectuur en workflow voor het bouwen van een robuuste, schaalbare en professionele vakantie-galerij website met behulp van AWS (Amazon Web Services).

## 1. Gebruikte AWS Services

### Frontend & Hosting

 * **AWS Amplify:** Wordt gebruikt voor het automatisch bouwen en hosten van de frontend applicatie (React, Vue of Next.js) rechtstreeks vanuit de GitHub repository. Het biedt CI/CD en SSL-certificaten out-of-the-box.

 * **Amazon Route 53:** Voor het beheer van de domeinnaam en de DNS-routering naar de Amplify-omgeving.

### Gebruikersbeheer & Beveiliging

 * **Amazon Cognito:** Regelt de volledige authenticatie-flow (aanmelden, inloggen, wachtwoordherstel). Dit zorgt ervoor dat de 3-5 TB aan privéfoto's en video's alleen toegankelijk zijn voor geautoriseerde familieleden.

### Opslag & Distributie

 * **Amazon S3 (Simple Storage Service):** De primaire opslag voor alle media.

   * Er wordt gebruik gemaakt van de **S3 Standard-IA (Infrequent Access)** opslagklasse voor een optimale balans tussen kosten en toegangssnelheid voor dit archief.

 * **Amazon CloudFront (CDN):** Een wereldwijd Content Delivery Network dat de media bestanden (vooral de zware video's) met hoge snelheid en lage latentie streamt. Dit voorkomt buffering en verlaagt de belasting op de S3 bucket.

### Backend & API

 * **Amazon API Gateway:** Fungeert als de beveiligde 'voordeur' voor de frontend om te communiceren met de backend.

 * **AWS Lambda:** Serverless Python functies die de logica uitvoeren (zoals het valideren van tokens en het ophalen van metadata) zonder dat er servers 24/7 aan hoeven te staan.

 * **Amazon DynamoDB:** Een snelle NoSQL-database voor het opslaan van de metadata per bestand (S3-paden, jaartallen, locaties, tags en bestandstypes).

## 2. Workflows

### A. Media Ingestie & Upload (Python Script)

 1. **Lokale Indexering:** Een Python script doorloopt de lokale mappenstructuur op de computer.
 2. **Directe Upload:** Het script uploadt de originele bestanden via de Boto3 library rechtstreeks naar de S3 bucket.
 3. **Metadata Registratie:** Na een succesvolle upload schrijft het script automatisch de relevante metadata (bestandsnaam, jaar, locatie) naar de DynamoDB tabel.

### B. Authenticatie Workflow

 1. De gebruiker bezoekt de website (Amplify).
 2. De frontend verwijst de gebruiker naar de door Cognito gehoste login-interface.
 3. Na inloggen ontvangt de browser een beveiligde JWT-token die wordt gebruikt voor alle API-verzoeken.
### C. Weergave van de Galerij
 1. **Verzoek:** De frontend vraagt aan de API Gateway om een lijst met media voor een specifiek jaar.
 2. **Verwerking:** Een Lambda functie controleert de rechten en haalt de metadata op uit DynamoDB.
 3. **URL Generatie:** De Lambda functie geeft de CloudFront URL's terug aan de applicatie.
 4. **Streaming:** De browser laadt de media direct via CloudFront voor een optimale gebruikerservaring.

## 3. Veiligheid en Optimalisatie

 * **IAM Roles:** Strikt beheer van rechten binnen AWS via het 'Least Privilege' principe.

 * **S3 Lifecycle Policies:** Automatische regels die bestanden na verloop van tijd naar nog goedkopere opslaglagen (zoals S3 Glacier) kunnen verplaatsen als ze zelden worden bekeken.

 * **CloudFront Caching:** Minimaliseert data-transfer kosten en verbetert de laadtijden voor herhaalde bezoeken.
