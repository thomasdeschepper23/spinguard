# 📘 Complete Handleiding — SpinGuard Website

> **Van ZIP-bestand tot werkende website met klant-aanvragen.**
> Geen technische kennis nodig. Lees rustig door, één stap per keer.

---

## 📑 Inhoudsopgave

1. [Wat is dit pakket?](#1-wat-is-dit-pakket)
2. [Waarom je weg moet van Squarespace](#2-waarom-je-weg-moet-van-squarespace)
3. [Hosting kiezen — vergelijking](#3-hosting-kiezen--vergelijking)
4. [Stap 1 — Hosting bestellen (15 min)](#stap-1--hosting-bestellen-15-min)
5. [Stap 2 — Bestanden uploaden (10 min)](#stap-2--bestanden-uploaden-10-min)
6. [Stap 3 — Schrijfrechten instellen (5 min)](#stap-3--schrijfrechten-instellen-5-min)
7. [Stap 4 — Admin wachtwoord instellen (2 min)](#stap-4--admin-wachtwoord-instellen-2-min)
8. [Stap 5 — Domein verbinden (varieert)](#stap-5--domein-verbinden)
9. [Stap 6 — SSL aanzetten (5 min)](#stap-6--ssl-aanzetten-5-min)
10. [Stap 7 — E-mail testen voor leads (5 min)](#stap-7--e-mail-testen-voor-leads-5-min)
11. [Hoe leads binnenkomen](#-hoe-leads-binnenkomen)
12. [Het admin paneel — dagelijks gebruik](#-het-admin-paneel--dagelijks-gebruik)
13. [Lead management (inbox + CSV + auto-reply)](#-lead-management-inbox--csv--auto-reply)
14. [Volledige site-controle (theme, menu, onderhoud)](#-volledige-site-controle)
15. [Beveiliging & backup](#-beveiliging--backup)
16. [SEO — Vindbaar in Google](#-seo--vindbaar-in-google)
17. [Marketing & advertenties (Meta, Google Ads, GA4)](#-marketing--advertenties-meta-google-ads-ga4)
18. [Backups & onderhoud](#-backups--onderhoud)
15. [Troubleshooting](#-troubleshooting)
16. [Wat zit er allemaal in?](#-wat-zit-er-allemaal-in)
17. [Glossarium](#-glossarium)

---

## 1. Wat is dit pakket?

Een **complete website** voor SpinGuard die op je eigen hosting draait. Inclusief:

- ✅ **5 pagina's**: Home, Diensten, Werkwijze, Over ons, Contact
- ✅ **Mobiel-vriendelijk** ontwerp (paars / spider thema)
- ✅ **Admin paneel** waar je teksten, prijzen, foto's en reviews zelf aanpast
- ✅ **Contact-formulier** dat e-mails naar `info@spinguard.nl` stuurt
- ✅ **WhatsApp integratie** met klik-en-stuur knoppen
- ✅ **SEO-optimalisaties** (Google Schema, sitemap, Open Graph, etc.)
- ✅ **Geen maandelijkse abonnementen** voor de site zelf — alleen hosting (~€3/maand)

### Vergelijking met Squarespace

|                       | Squarespace                | Deze nieuwe site         |
|-----------------------|----------------------------|--------------------------|
| Maandelijks           | €15-30/maand               | €3-7/maand (hosting)     |
| Eigenaarschap site    | Bij Squarespace            | **Volledig van jullie**  |
| Custom design         | Beperkt                    | **Volledig op maat**     |
| SEO controle          | Beperkt                    | **Volledig**             |
| Foto's aanpassen      | Drag & drop                | Drag & drop (admin)      |
| Tekst aanpassen       | WYSIWYG editor             | Formulieren (admin)      |
| Snelheid              | Gemiddeld                  | **Zeer snel** (geen JS bloat) |
| Lock-in               | Hoog (kun je niet meenemen)| Geen (jullie zip)        |

> 💡 **Per saldo**: ongeveer €150-250/jaar besparing + betere site + volledige controle.

---

## 2. Waarom je weg moet van Squarespace

Squarespace is een **all-in-one platform** — geen "hosting" in de traditionele zin.
Je kunt er **geen externe code op uploaden**. Daarom moet je voor deze nieuwe site naar een **echte webhosting** verhuizen.

### Wat dit precies betekent
- Je **domein `spinguard.nl`** kun je behouden (alleen DNS aanpassen — zie stap 5)
- Je oude Squarespace-site verdwijnt zodra je de DNS verandert
- Je hoeft Squarespace niet meteen op te zeggen — wacht tot de nieuwe site werkt
- E-mail (info@spinguard.nl) verhuist mogelijk ook mee — afhankelijk van je setup

### Hoeveel werk?
**Eenmalig: ongeveer 1-2 uur** (voor iemand zonder technische kennis):
- 15 min hosting bestellen
- 10 min bestanden uploaden
- 5 min admin instellen
- 30 min DNS aanpassen + wachten tot dat werkt
- 30 min testen + content aanpassen

---

## 3. Hosting kiezen — vergelijking

Je hebt een hosting nodig die **PHP** ondersteunt (vrijwel alle Nederlandse hostings).

### Top 5 voor SpinGuard (Nederlandse hostings, prijzen ±2026)

| Provider | Vanaf/maand | PHP | SSL | Aanbevolen voor |
|---|---|---|---|---|
| ⭐ **TransIP** | €3,- | 8.x | Gratis | Aanbevolen — beste UI + NL support |
| **Hostnet** | €4,- | 8.x | Gratis | Solide, populair bij MKB |
| **MijnHostingPartner** | €2,50 | 8.x | Gratis | Goedkoop + Nederlandse service |
| **Versio** | €1,50 | 8.x | Gratis | Goedkoopst, basic UI |
| **Antagonist** | €4,- | 8.x | Gratis | Tech-savvy, goede performance |

### Mijn aanbeveling: **TransIP — Webhosting Pro** (~€3/m)

**Waarom?**
- 🇳🇱 Nederlandse support (telefoon + chat)
- 🛠 **DirectAdmin** als bestandsbeheer (deze handleiding gebruikt het)
- 🔒 1-klik gratis SSL via Let's Encrypt
- ⚡ Snelle Nederlandse servers
- 📧 E-mailaccounts inbegrepen (info@spinguard.nl etc.)
- 🆘 24/7 klantenservice

### Wat je NIET nodig hebt
- ❌ Dure "Business" of "Enterprise" pakketten
- ❌ Dedicated server / VPS
- ❌ Aparte e-mail-hosting (zit erbij)
- ❌ "Website builder" (we hebben de site al)

---

## Stap 1 — Hosting bestellen (15 min)

### Bij TransIP (aanbevolen voorbeeld)

1. Ga naar **[transip.nl/webhosting](https://www.transip.nl/webhosting)**
2. Kies **"Webhosting Pro"** of **"Webhosting Plus"** (beiden goed voor SpinGuard)
3. Klik **"Direct bestellen"**
4. Vul je gegevens in:
   - **Bedrijfsnaam**: SpinGuard (of jullie KvK-naam)
   - **Adres + facturatie**: jullie eigen adres
5. Bij "Domein": skip dit (jullie hebben al `spinguard.nl`)
6. Reken af (iDEAL of creditcard)
7. Wacht ±10 minuten op de welkomstmail

### Wat krijg je?
In de welkomstmail vind je:
- 🔐 **DirectAdmin login** (URL + gebruikersnaam + wachtwoord)
- 🔐 **FTP gegevens** (host, gebruikersnaam, wachtwoord)
- 🌐 **IP-adres van je hosting** (belangrijk voor DNS-stap)

> ⚠ **Bewaar deze mail goed!** In een wachtwoordmanager (1Password, Bitwarden) of geprint.

---

## Stap 2 — Bestanden uploaden (10 min)

Er zijn 2 manieren. Kies wat je makkelijker vindt.

### Optie A — Via DirectAdmin (in de browser, geen installatie)

1. Login bij DirectAdmin (URL uit welkomstmail, bijv. `https://srv01.transip.nl:2222`)
2. Klik **"File Manager"** of **"Bestandsbeheer"**
3. Open de map **`public_html`** (soms `httpdocs` of `www`)
4. Klik **"Upload"** → kies **`spinguard-website-upload.zip`**
5. Wacht tot upload klaar (paar seconden)
6. Klik op het ZIP-bestand → **"Extract"** of **"Uitpakken"**
7. Verwijder daarna de ZIP zelf

### Optie B — Via FileZilla (FTP, sneller bij grote bestanden)

1. Download **[FileZilla](https://filezilla-project.org/download.php?type=client)** (gratis)
2. Open FileZilla
3. Bovenin: vul FTP-gegevens uit welkomstmail in:
   - **Host**: bijv. `ftp.spinguard.nl` of een IP
   - **Gebruikersnaam**: uit mail
   - **Wachtwoord**: uit mail
   - **Poort**: 21
4. Klik **"Snel verbinden"**
5. **Rechts** zie je de hosting → ga naar `public_html`
6. **Links** zie je je computer → selecteer ALLE bestanden uit `Spinguard website` map
7. Sleep ze van links naar rechts
8. Wacht tot upload klaar (alle bestanden krijgen groen vinkje)

### ⚠ Wat NIET uploaden
- De `.zip` zelf hoeft niet als je via FileZilla werkt
- De map `_archief/` (als die nog bestaat — bevat oude design)
- `node_modules/` of `.git/` (komen niet voor in deze pakket)

### Resultaat
Na upload moet je in `public_html` zien:
```
public_html/
├── index.php
├── diensten.php
├── werkwijze.php
├── over-ons.php
├── contact.php
├── styles.css
├── .htaccess
├── admin/
├── api/
├── assets/
├── content/
├── inc/
├── js/
├── uploads/
├── HANDLEIDING.md  (deze file)
└── README.md
```

---

## Stap 3 — Schrijfrechten instellen (5 min)

Het admin paneel moet kunnen schrijven naar 3 plekken. Doe dit **één keer**.

### In DirectAdmin
1. Bestandsbeheer
2. Klik met rechtermuisknop op de map **`content/`** → **"Permissions"** (of **"Rechten"**)
3. Vul **`755`** in (of vink "Read/Write/Execute" voor Owner aan)
4. **"Apply to all subfolders and files"** aanvinken
5. Klik OK

Herhaal voor:
- 📁 `uploads/` → **755**
- 📁 `content/backups/` → **755** (wordt vanzelf aangemaakt)
- 📁 `content/leads/` → **755** (wordt vanzelf aangemaakt)
- 📄 `admin/config.php` → **644** (zodat wachtwoord opgeslagen kan worden)

### In FileZilla
1. Klik met rechtermuisknop op de map → **"Bestandsattributen"**
2. Vul **`755`** in voor mappen, **`644`** voor `config.php`
3. "Recursief naar onderliggende mappen" aanvinken
4. OK

> 💡 **Wat betekent dit?** 755 = lezen voor iedereen, schrijven alleen voor de eigenaar (= jullie). Standaard veilig.

---

## Stap 4 — Admin wachtwoord instellen (2 min)

1. Open je hosting-URL in de browser. Bijvoorbeeld:
   - Tijdelijk: `https://[hosting-domein]/admin/`
   - Of na DNS: `https://spinguard.nl/admin/`
2. Je krijgt automatisch een **setup-pagina** te zien
3. Verzin een **sterk wachtwoord** (minimaal 8 tekens, liefst 12+ met cijfer + symbool)
4. Bewaar het in een wachtwoordmanager
5. Klik "Wachtwoord instellen"
6. Je wordt automatisch ingelogd in het admin paneel

> ⚠ **Geen "wachtwoord vergeten"-functie.** Bij verlies: open `admin/config.php` via FTP, leeg de waarde tussen `''` bij `ADMIN_PASSWORD_HASH`. Daarna kun je opnieuw instellen.

---

## Stap 5 — Domein verbinden

Nu de site werkt, willen we hem op `spinguard.nl` draaien (in plaats van Squarespace).

### Eerst testen op een test-URL

Voordat je `spinguard.nl` overzet, kun je de site testen op een tijdelijke URL.
Bij TransIP krijg je bijv. `srv01.spinguard.nl` of `xxxx.transipanel.nl`.
Open die URL in de browser → check of alles werkt → ga dan pas door.

### Wat moet er gebeuren?
**De DNS van `spinguard.nl` moet wijzen naar je nieuwe hosting.**

Op dit moment wijst hij naar Squarespace's servers. We veranderen dat naar TransIP (of waar je nu host).

### Waar staat het domein? Twee scenarios:

#### Scenario A — Domein is bij Squarespace gekocht
1. Login Squarespace → Settings → Domains → Spinguard.nl
2. Klik **"Use a third-party DNS provider"** of **"Manage DNS"**
3. **Verwijder** de bestaande A-records die naar Squarespace wijzen
4. **Voeg toe** (vraag IP-adres bij je hosting):
   - Type: `A` | Naam: `@` | Waarde: `[IP van je hosting]`
   - Type: `A` | Naam: `www` | Waarde: `[IP van je hosting]`
5. Save

#### Scenario B — Domein is bij andere registrar (TransIP, Hostnet, etc.)
1. Login bij waar je het domein hebt gekocht
2. Ga naar DNS-instellingen voor `spinguard.nl`
3. Pas dezelfde A-records aan zoals hierboven

#### Scenario C — Domein verhuizen naar TransIP (overzichtelijker)
1. Bij TransIP: "Domein verhuizen" → vul `spinguard.nl` in
2. Vraag bij Squarespace (of huidige eigenaar) een **EPP-token** ("auth-code") aan
3. Plak die in TransIP, betaal ±€10 verhuiskosten
4. Wacht 1-7 dagen

> 🕐 **Wachttijd na DNS-aanpassing**: 15 minuten tot 24 uur (meestal binnen 1 uur). Tijdens deze periode zien sommige mensen nog de oude site, anderen al de nieuwe.

---

## Stap 6 — SSL aanzetten (5 min)

SSL = het groene slotje + `https://`. **Verplicht** voor SEO en vertrouwen.

### Bij TransIP / DirectAdmin
1. Login DirectAdmin
2. Zoek **"SSL Certificates"** of **"Let's Encrypt"**
3. Selecteer `spinguard.nl` + `www.spinguard.nl`
4. Klik **"Generate"** of **"Issue"**
5. Wacht 1-2 minuten

### Force HTTPS (alle bezoekers naar https://)
Open `.htaccess` (in de hoofdmap) via DirectAdmin bestandsbeheer:
- Zoek de regels:
  ```apache
  # RewriteCond %{HTTPS} off
  # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```
- Verwijder de `# ` aan het begin van die 2 regels
- Save

Test in browser: `http://spinguard.nl` zou automatisch naar `https://` moeten redirecten.

---

## Stap 7 — E-mail testen voor leads (5 min)

Om aanvragen via het contact-formulier te ontvangen, moet `mail()` werken.

### Test 1: Stuur een test-aanvraag
1. Open `https://spinguard.nl/contact.php`
2. Vul je eigen gegevens in:
   - Naam: TEST
   - Telefoon: 06...
   - Postcode: jouw postcode
   - Bericht: "Dit is een test"
3. Klik "Verstuur aanvraag"

### Test 2: Check je inbox
- Open `info@spinguard.nl` (of welk adres je in admin hebt ingesteld)
- **Check ook spam-folder!**
- Mail zou er binnen 1-5 minuten moeten zijn

### ✅ Werkt? Dan ben je klaar! 🎉

### ❌ Niet binnen 10 minuten? Twee opties:

#### Optie A — Wacht eerst (vaak alleen vertraging)
Mail kan vertraagd zijn op nieuwe hostings. Wacht 30 minuten. Probeer opnieuw.

#### Optie B — Check de admin "Aanvragen" backup
Login `/admin/leads.php` — daar staat altijd een **backup-log** van elke aanvraag, zelfs als de e-mail faalt. Geen aanvragen verloren.

#### Optie C — SMTP setup (voor garantie)
Als `mail()` niet werkt, kun je SMTP configureren via je hosting:
1. Maak een mailbox aan in je hosting (bijv. `noreply@spinguard.nl`)
2. Vraag je hostingprovider om hulp met **PHPMailer** + SMTP-instellingen
3. Of: gebruik externe service zoals **[Resend](https://resend.com/)** (gratis tot 3.000 mails/maand) — vereist code-aanpassing

---

## 📨 Hoe leads binnenkomen

Klanten kunnen op **3 manieren** contact opnemen:

### 1. Via WhatsApp (snelst, meest gebruikt)
- Klant klikt op één van de WhatsApp-knoppen op de site (header, hero, drijvende knop, etc.)
- WhatsApp Web/App opent met **vooringevuld bericht** ("Hallo SpinGuard, ik wil graag een vrijblijvende offerte aanvragen.")
- Klant past tekst aan, voegt foto's toe, drukt verzenden
- Jullie ontvangen direct een WhatsApp-bericht op `+31 24 234 0061`

### 2. Via het contactformulier
1. Klant vult naam, telefoon, postcode, type pand + optioneel bericht in
2. Klikt "Verstuur aanvraag"
3. **Twee dingen gebeuren tegelijk**:
   - 📧 E-mail naar `info@spinguard.nl` met alle details
   - 📂 Backup opgeslagen in `content/leads/[jaar-maand].txt`
4. Klant ziet "Bedankt voor uw aanvraag!" + WhatsApp-knop voor snelle follow-up

### 3. Via direct telefonisch
- Telefoonnummer is overal klikbaar (op mobiel: bel direct)
- Mailto-link op contact-pagina opent jullie eigen mailprogramma

### Wat krijg je per e-mail?
```
Onderwerp: [SpinGuard] Nieuwe offerte-aanvraag — Jan Jansen

Naam      : Jan Jansen
Telefoon  : 06 12 34 56 78
E-mail    : jan@example.com
Postcode  : 6500 AA
Type pand : Rijtjeshuis

Toelichting:
Veel spinnen op de gevel sinds afgelopen maand.

------------------------------------------
Verstuurd op 01-05-2026 14:23
IP: 84.123.45.67
```

### Tips voor snelle opvolging

#### Mobiele notificaties van mail
- Stel pushmeldingen aan in je e-mailapp voor `info@spinguard.nl`
- Of: zet je inbox op je telefoon (Gmail/Outlook app)

#### Forward naar WhatsApp Business
Met **Zapier** of **Make.com** (~€20/m gratis tier):
1. Trigger: "Nieuwe e-mail in info@spinguard.nl"
2. Action: "Stuur WhatsApp-bericht naar [eigen nummer]"
- Resultaat: zelfs als je inbox niet bekijkt, krijg je WhatsApp ping bij elke lead

#### Auto-reply
Stel in je e-mail (Outlook/Gmail) een auto-reply in:
> "Bedankt voor je aanvraag bij SpinGuard. We reageren binnen 24 uur. Voor snel contact: WhatsApp 024 234 0061."

---

## 🛠 Het admin paneel — dagelijks gebruik

### Inloggen
- Open `https://spinguard.nl/admin/`
- Gebruikersnaam: `admin`
- Wachtwoord: dat wat je in stap 4 hebt ingesteld

### Wat kun je aanpassen?

#### 🏢 **Bedrijfsgegevens**
Naam, telefoonnummer, e-mail, KvK, BTW, sociale media-URL's.
> ⚠ E-mail-adres bepaalt waar contactformulier-aanvragen heen gaan!

#### 🎨 **Hero (homepage bovenkant)**
De grote tekst, knop-teksten, vertrouwens-items.

#### 💰 **Prijzen / pakketten**
- Toevoegen / verwijderen / herordenen
- Prijs in € of "Op aanvraag"
- Eén pakket "uitgelicht" voor de paarse highlight
- Kenmerken (één per regel)

#### 🔄 **Werkwijze stappen**
De 5 stappen van het werkproces. Per stap: titel, korte tekst, lange tekst, bullet-points.

#### 📸 **Before/After foto's** (visuele picker!)
- Klik op een foto-vak → kies uit galerij of upload nieuwe
- Volledig drag & drop ondersteund

#### 💎 **Voordelen** (4 kaartjes)
Houd op 4 voor mooie indeling. ID `b1, b2, b3, b4` bepaalt de gradient-kleur.

#### ⭐ **Reviews** (testimonials)
Vervang fictieve door **echte reviews**. 3 echte reviews zijn krachtiger dan 10 verzonnen.

#### ❓ **FAQ**
Vragen + antwoorden. Helpen ook voor SEO (kunnen als rich snippet in Google verschijnen).

#### 👥 **Over ons**
Volledig aanpasbaar:
- **Hero**: eyebrow + titel + accent-tekst + intro
- **Ons verhaal**: eyebrow + heading + verhaal (alinea's)
- **Portretten**: labels op beide cards + "Sinds [jaar]" badge
- **Werkgebied sectie**: titel + beschrijving (kaart wordt automatisch gegenereerd uit SEO service-cities)
- **Onze waarden**: titels + 4 kaarten
- **CTA banner**: titel + tekst

#### 🔍 **SEO** (4 sub-secties)
- **Algemeen**: standaard titel/beschrijving
- **Per pagina**: eigen titel/beschrijving voor home, diensten, etc.
- **Lokaal**: stad, GPS, openingstijden, service-steden — voor "spinnenbestrijding [stad]" Google-zoekopdrachten
- **Reviews**: aantal + score → ⭐ in Google

### 📷 Foto's beheren
- Sidebar → "Foto's"
- Upload via drag & drop of klik
- "Kopieer pad" → plak in een tekstveld in de editor
- Of: gebruik de visuele picker bij Before/After

### 📨 Aanvragen bekijken
- Sidebar → "Aanvragen"
- Per maand gegroepeerd
- Backup-log voor het geval e-mail faalt

### 🔐 Wachtwoord wijzigen
- Sidebar → "Wachtwoord"
- Vul huidig + nieuw 2x in

### 💾 Auto-backups
Bij elke "Wijzigingen opslaan" wordt automatisch een backup gemaakt in `content/backups/`. De laatste 5 versies blijven bewaard.

### 🆘 Per ongeluk verkeerd opgeslagen?
1. Open via FTP de map `content/backups/`
2. Kies een eerdere versie (bestandsnaam met timestamp)
3. Hernoem naar `site.json` en verplaats naar `content/`
4. Klaar — terug naar oudere versie

---

## 📨 Lead management (inbox + CSV + auto-reply)

Alle aanvragen via het contactformulier komen binnen in een **lead-inbox** in admin (`/admin/leads.php`).

### Statussen
Elke lead heeft een status:
- 🔵 **Nieuw** — net binnen, nog niet opgevolgd
- 🟢 **Gecontacteerd** — al teruggebeld/gemaild
- 📁 **Gearchiveerd** — afgesloten (offerte aangenomen of afgewezen)

### Acties per lead
- ✓ **Gecontacteerd** — markeer als opgevolgd
- 📁 **Archiveren** — verberg uit hoofdlijst
- ↩ **Terugzetten** — naar "nieuw"
- 📝 **Notitie** — eigen aantekening (bv. "belt morgen 10u terug")
- **Verwijderen** — definitief weg
- 💬 **WhatsApp** — direct doorklikken naar gesprek met de klant

### Quick links per lead
Direct klikbaar:
- 📞 Telefoonnummer → bel-link
- ✉ E-mail → mailto link
- 💬 WhatsApp-knop → opent WhatsApp met dat nummer

### CSV export
Knop "**Export CSV**" rechtsboven. Download alle leads in Excel-compatibel formaat (UTF-8 BOM, datum, status, contact, bericht, UTM-bron).

### Lead doorsturen — meerdere ontvangers
Admin → Content bewerken → tab **Leads**:
- **Extra ontvangers**: komma-gescheiden e-mailadressen die ook een kopie krijgen (bv. `partner@example.com, monteur@example.com`)
- Eerste e-mail blijft naar `info@spinguard.nl` of welk adres je in Bedrijfsgegevens hebt ingesteld

### Webhook voor Slack/Discord/Make/Zapier
Tab **Leads** → **Webhook URL**. Bij elke nieuwe lead stuurt de site een POST met JSON-payload:
```json
{
  "text": "🕷️ Nieuwe SpinGuard aanvraag\n*Naam* · 06... · 6500AA",
  "lead": { "id": "...", "name": "...", "phone": "...", "email": "...", ... }
}
```

**Voorbeeld Slack-integratie:**
1. Maak Slack workspace + kanaal `#leads-spinguard`
2. Slack → Apps → "Incoming Webhooks" → kies kanaal → kopieer URL
3. Plak in admin
4. Volgende lead → ping in Slack ✓

### Auto-reply naar klant
Tab **Leads** → **Auto-reply**:
- ✓ Aan/uit
- Onderwerp aanpasbaar
- Bericht aanpasbaar met variabelen: `{naam}`, `{telefoon}`, `{postcode}`, `{type}`

Standaard bericht:
```
Beste {naam},

Bedankt voor uw aanvraag bij SpinGuard. Wij hebben uw bericht goed
ontvangen en nemen binnen 24 uur contact met u op.

Voor snel contact kunt u ons WhatsApp-bericht sturen op 024 234 0061.

Met vriendelijke groet,
Het SpinGuard team
```

### UTM-tracking (welke campagne leverde de lead)
Als je advertenties draait met UTM-parameters (zoals `?utm_source=facebook&utm_campaign=zomer`), worden die opgeslagen bij de lead. Je ziet daarna **per lead** uit welke campagne hij komt — handig voor ROI-meting.

---

## 🎛 Volledige site-controle

Via admin **Content bewerken** kun je nu **alles** aanpassen — geen developer meer nodig.

### Tab "Vormgeving" — Logo + kleuren
- **Logo upload**: klik op het logo-vak → upload nieuwe (modal-picker, drag & drop)
- **Primaire kleur** (paars): de hoofdkleur van knoppen, accenten, links — kies via color picker of typ HEX
- **Donker variant**: hover-states, donker accent
- **Licht variant**: voor subtiele details
- **Achtergrond donker**: hero-sectie achtergrond (was: dark navy)

### Tab "Menu" — Navigatie aanpassen
- Voeg menu-items toe/verwijder/herorder
- Per item: **Label** (zichtbaar) + **URL** + **Key** (unieke ID)
- Kun je gebruiken voor:
  - Externe links (bv. naar Google Maps)
  - Interne anchor-links (`/#diensten`)
  - Volledig nieuwe pagina's (vraag developer)

### Tab "Footer" — Aankondigingsbalk + footer + cookies
**Aankondigingsbalk** (boven aan elke pagina):
- ✓ Aan/uit
- Tekst (bv. "Zomeractie: 20% korting bij 3+ panden!")
- Optionele link met eigen tekst
- Aanpasbare achtergrond + tekstkleur

**Footer-teksten**:
- Tagline (eerste alinea)
- Extra tekst (kleinere onder-tagline)
- Copyright tekst (leeg = automatisch)

**Cookie banner**:
- Titel
- Bericht (volledige tekst)
- Knop-labels ("Akkoord met alle" / "Alleen noodzakelijk")

### Tab "Geavanceerd" — Onderhoud + Custom CSS

**🚧 Onderhoudsmodus** (zet site offline tijdens updates):
- Status: ON / OFF
- Titel + bericht (wat bezoekers zien)
- "Admin sessie omzeilt onderhoud?" — Ja = jij blijft de echte site zien terwijl bezoekers onderhoud zien

> 💡 Handig bij: prijzen overhalen, grote tekst-rewrites, foto-update batch.

**💻 Custom CSS** — eigen stijlen toevoegen:
```css
/* Voorbeeld: maak alle prijskaarten groter */
.price-card { padding: 40px; }

/* Voorbeeld: andere font */
body { font-family: "Georgia", serif; }
```

> ⚠ Alleen gebruiken als je weet wat CSS is. Verkeerde code kan layout breken.

---

## 🛡 Beveiliging & backup

`/admin/security.php` — Het beveiligings-dashboard.

### Health check (status van je site)
Direct overzicht of alles goed draait:
- ✓ PHP mail() functie beschikbaar
- ✓ Schrijfrechten op content/ en uploads/
- ✓ HTTPS actief
- ✓ Wachtwoord wijzigbaar
- ✓ Backup mogelijk (zip extensie)
- ✓ PHP 8.x (niet verouderd)

Rood = direct fixen. Oranje = aanbevolen actie. Groen = OK.

### 1-klik backup downloaden
Knop "**Download backup**" → ZIP met:
- `content/` (alle teksten, leads, auto-backups)
- `uploads/` (alle foto's)

> 💡 Doe dit eens per maand. Sla de ZIP op in Dropbox/Google Drive.

### Activity log (laatste 100 acties)
Zie precies:
- Wie heeft wanneer ingelogd
- Welke tab is opgeslagen wanneer
- Mislukte logins (met IP-adres)
- Lockouts
- Lead-acties

### Brute-force bescherming (automatisch)
- 5 mislukte logins binnen 15 min → IP-adres **15 minuten vergrendeld**
- Wordt gelogd in activity log
- Hacker kan niet eindeloos wachtwoorden raden

### Sessie-bescherming
- Sessie wordt automatisch beëindigd na **1 uur inactiviteit**
- Sessie wordt **gebonden aan IP + browser** (gestolen cookie werkt niet vanaf andere computer)
- Maximale sessieduur: 8 uur (forceer opnieuw inloggen)

### Wachtwoord-tips
- Minimaal 12 tekens (langer = beter)
- Gebruik **wachtwoordmanager** (1Password, Bitwarden, KeePass)
- Geef nooit door via e-mail of WhatsApp
- Verander 1x per jaar via admin → Wachtwoord

---

## 🆕 Extra pagina's & features

### 🛡 Privacy + Algemene voorwaarden (`/privacy.php`, `/voorwaarden.php`)
**Wat:** verplichte juridische pagina's. Templates ingevuld met realistische voorbeeld-tekst.

**Beheer:** Admin → tab **Juridisch** — beide pagina's volledig aanpasbaar (markdown editor: `# header`, `**vet**`, `- lijst`).

**Waarom belangrijk:** AVG/GDPR-verplichting. Beboet kunnen worden zonder. Footer-link al ingebouwd.

### 📄 Eigen pagina's bouwen (`/custom.php?p=KEY`)
**Wat:** maak zelf nieuwe pagina's zonder developer.

**Beheer:** Admin → tab **Eigen pagina's**:
- Klik **+ Pagina toevoegen**
- Vul **key** (URL-deel, bv. `spinnen-info`) → URL wordt `/custom.php?p=spinnen-info`
- Titel, eyebrow (klein label), intro, meta description (SEO)
- Inhoud in markdown (zelfde als juridisch)

**Voorbeelden van eigen pagina's:**
- `spinnen-info` — info over spider-soorten in NL
- `referenties` — uitgebreide klantcases
- `vacature` — wij zoeken een nieuwe collega
- `partners` — onze partners en leveranciers
- `pers` — persberichten / media

> 💡 **Tip:** voeg de URL toe aan je menu via Admin → tab **Menu**.

---

## 🚀 Mobile sticky bar

Onderaan-balk op mobiel met **WhatsApp + Bel-knoppen** — altijd zichtbaar, ongeacht waar bezoeker scrolt.

Aan/uit via Admin → tab **Vormgeving** → **Mobile sticky bar**.

> 💡 Mobiele conversie boost — vrijwel iedereen typt zijn vraag eerder in WhatsApp dan in een formulier.

---

## 🗺 Werkgebied-kaart aanpassen

De kaart van Nederland op de **Over ons**-pagina is volledig instelbaar via Admin → tab **Over ons** → kaart "Kaart van Nederland — instellingen".

**Wat je kunt aanpassen:**
- **Provincies tonen** — 12 checkboxes (Drenthe, Flevoland, … Zuid-Holland). Aangevinkt = paars ingekleurd, uitgevinkt = grijs/transparant. Handig als jullie maar een deel van NL bedienen.
- **Steden op de kaart** — 18 grote steden, elk met een checkbox (pin tonen ja/nee) + radio-knop "HQ" om de hoofdvestiging aan te wijzen.
- **Verbindingslijnen tonen** — toggle voor de stippellijn vanaf hoofdvestiging naar elke andere stad.

> 💡 Voorbeeld: bedienen jullie alleen het zuiden? Vink alle noordelijke provincies + steden uit. De kaart toont dan automatisch alleen jullie werkgebied.

---

## ⭐ Reviews — verschillende weergave-stijlen

In Admin → tab **Reviews** kun je 5 verschillende stijlen kiezen voor de klantervaringen-sectie op de homepage:

| Stijl | Wanneer gebruiken |
|-------|-------------------|
| **Raster (3 kolommen)** | Standaard, werkt met 3, 6 of 9 reviews |
| **Compact (2 kolommen)** | Voor 2-4 reviews, smaller layout |
| **Slider / carousel** | Voor 5+ reviews; swipebaar met pijltjes en dots |
| **Lijst (1 per rij)** | Voor uitgebreide reviews met veel tekst |
| **Trustpilot widget** | Toon de officiële Trustpilot-widget i.p.v. eigen reviews |

**Trustpilot integratie:** Wanneer je "Trustpilot widget" kiest verschijnt een extra kaartje waar je de **embed-code** plakt (van Trustpilot Business → Get widgets). De widget verschijnt direct op de site.

Eyebrow / heading / subtitel van de sectie zijn ook aanpasbaar.

---

## 📊 Vergelijkingstabel "DIY versus SpinGuard"

De vergelijkingstabel onderaan de **Diensten**-pagina is volledig instelbaar via Admin → tab **Prijzen** → kaart "Vergelijkingstabel".

- Tabel **aan/uit-zetten** met checkbox
- **Eyebrow / heading / introtekst** aanpassen
- **Kolomkoppen** wijzigen (standaard "DIY / supermarkt" vs "SpinGuard")
- **Rijen** toevoegen / verwijderen / herordenen — elke rij heeft drie statussen per kolom: ✓ Ja / ± Soms / — Nee

---

## 🖼 Foto's op portretten "Over ons"

De twee schuingedraaide foto-cards naast "Opgericht uit ergernis met halve oplossingen" zijn vanaf nu instelbaar:

Admin → tab **Over ons** → kaart **Portretten + Sinds badge**:
- **Foto bovenste card** — upload via foto-picker. Laat leeg = standaard spider-icoon.
- **Foto onderste card** — idem voor witte card. Leeg = building-icoon.
- Labels onder de foto's blijven aanpasbaar.

---

## 🛠 Admin uitbreidingen (extra power-tools)

### Bulk-acties op leads
In `/admin/leads.php`:
- ☑ Selecteer alle / per stuk
- Geselecteerd → balk verschijnt met:
  - ✓ Markeer alle gecontacteerd
  - 📁 Archiveer alle
  - 🗑 Verwijder alle

> 💡 Handig na een marketing-campagne om in 1 keer 50 oude leads op te ruimen.

### Image auto-resize bij upload
Foto's die je uploadt worden **automatisch geoptimaliseerd**:
- Max 1920px breed (groter wordt geresized)
- JPEG quality 85 (kleiner bestand, behoud kwaliteit)
- PNG/WebP transparency behouden

**Resultaat:** snellere site (Google ranking↑) zonder extra werk.

> ℹ Vereist PHP **GD-extensie** — vrijwel alle hostings hebben dit standaard. Werkt het niet? Foto's worden gewoon onverkleind opgeslagen (geen probleem, alleen iets groter).

---

## 🔍 SEO — Vindbaar in Google

De site heeft **alle SEO-basics** ingebouwd:
- Schema.org markup (LocalBusiness, FAQ, Service, HowTo, Breadcrumbs)
- Open Graph + Twitter Cards (mooie social media previews)
- Sitemap.xml + robots.txt
- Geo-meta tags voor lokale zoekopdrachten
- Canonical URL's
- Mobile-first responsive

Maar Google moet de site nog wel **vinden + indexeren**. Doe deze 3 dingen na launch:

### 1. Google Search Console (verplicht — 10 min)

Hierdoor weet Google dat de site bestaat + krijg je inzicht in zoekverkeer.

1. Ga naar **[search.google.com/search-console](https://search.google.com/search-console)**
2. Klik **"Eigendom toevoegen"** → kies **"URL-prefix"** → vul `https://spinguard.nl/` in
3. Verifieer eigendom (meestal via HTML-tag of DNS — kies HTML-tag, plak in admin → SEO algemeen)
4. Na verificatie: ga naar **"Sitemaps"** → vul `sitemap.xml` in → klik "Indienen"
5. Wacht 1-2 weken — Google indexeert geleidelijk

### 2. Google Business Profile (essentieel voor lokaal — 15 min)

Dit zorgt voor:
- ⭐ Gele sterren in Google Maps
- 📍 Pin op kaart bij "spinnenbestrijding bij mij in de buurt"
- 📞 1-klik bel-knop in zoekresultaten

1. Ga naar **[business.google.com](https://business.google.com)**
2. Voeg `SpinGuard` toe als bedrijf
3. Vul adres in (mag postbus / hoofdvestiging zijn)
4. Categorie: **"Pest control service"** of **"Ongediertebestrijding"**
5. Vul telefoon + website (`https://spinguard.nl`) in
6. Verificatie via post (kaart in 5-10 dagen) of telefoon
7. Na verificatie: voeg foto's, openingstijden, service-gebied toe
8. **Vraag actief om reviews** van tevreden klanten via WhatsApp-link

### 3. Geef Google 1 maand tijd

In de eerste maand:
- **Week 1**: Google ontdekt de site, indexeert eerste paginas
- **Week 2-3**: Eerste organische bezoekers (5-20 per dag)
- **Week 4+**: Reviews komen binnen, ranking groeit

### Tips voor extra zichtbaarheid
- 🔄 **Voeg regelmatig nieuwe FAQ's toe** (Google leest zo dat de site "leeft")
- 📸 **Upload echte foto's** van werk-projecten (before/after)
- ⭐ **Verzamel echte reviews** en zet ze in admin → Reviews tab
- 🏘 **Service-steden** in admin uitbreiden naar gemeentes waar je werkt

---

## 📊 Marketing & advertenties (Meta, Google Ads, GA4)

De website is **volledig voorbereid** voor advertentiecampagnes en analytics. Je hoeft geen code aan te passen — alle pixels en tracking-codes plak je in het admin paneel.

### Wat zit er ingebouwd?

| Platform | Wat | Auto-events |
|---|---|---|
| **Google Analytics 4** | Bezoekersgedrag tracken | PageView, generate_lead, whatsapp_click, phone_click |
| **Meta Pixel** | Facebook/Instagram retargeting | PageView, Lead, Contact |
| **Google Ads** | Conversion tracking voor advertenties | Conversion bij elk lead |
| **TikTok Pixel** | TikTok-advertenties tracken | PageView |
| **LinkedIn Insight Tag** | LinkedIn-campagnes | PageView |
| **Google Tag Manager** | Centraal beheer van alles | dataLayer events |
| **🍪 Cookie consent** | AVG/GDPR-compliant banner | Tracking laadt pas na "Akkoord" |

### Belangrijke principes

✅ **AVG/GDPR-compliant** — tracking laadt **alleen** als bezoeker op "Akkoord" klikt
✅ **Conversion events** vuren automatisch af (na succesvol formulier, bij WhatsApp-klik, etc.)
✅ **Geen developer nodig** — alles via admin paneel
✅ **Cookie banner** is ingebouwd en kan optioneel worden uitgezet

---

### Setup 1 — Google Analytics 4 (verplicht eerste, gratis, 10 min)

GA4 vertelt je: hoeveel bezoekers, waar komen ze vandaan, welke pagina's zijn populair, hoe lang blijven ze.

#### Stappen
1. Ga naar **[analytics.google.com](https://analytics.google.com)** → log in met Google-account
2. Klik **"Account aanmaken"** → bedrijfsnaam: SpinGuard → akkoord
3. **Property aanmaken**: SpinGuard → Tijdzone Nederland → EUR
4. **Bedrijfsdetails** invullen → Doel kiezen "Generate leads"
5. **Data stream** → Web → URL: `https://spinguard.nl` → naam: SpinGuard Website
6. Je krijgt een **Measurement ID** te zien: **`G-XXXXXXXXXX`** (kopieer)
7. Open jullie admin: `https://spinguard.nl/admin/edit.php#tab-marketing`
8. Plak het ID bij **"Google Analytics 4 — Measurement ID"**
9. Klik **"Wijzigingen opslaan"**

#### Testen of het werkt
1. Open jullie site in een **incognito-venster**
2. Klik op het cookie banner: **"Akkoord met alle"**
3. Wacht 30 seconden
4. Ga in GA4 naar **Reports → Realtime**
5. Je zou jezelf moeten zien als 1 actieve gebruiker

---

### Setup 2 — Meta (Facebook/Instagram) Pixel (15 min)

Voor:
- Retargeting (mensen die jullie site bezochten weer bereiken via FB/IG ads)
- Conversie-meting van Facebook-campagnes
- "Lookalike audiences" maken op basis van leads

#### Stappen
1. Ga naar **[business.facebook.com](https://business.facebook.com)** → maak een Business account aan (of log in)
2. **Events Manager** → **"Connect Data Sources"** → **Web** → **Meta Pixel**
3. Naam: SpinGuard Pixel → URL: `https://spinguard.nl`
4. Kies **"Install code manually"** → kopieer het 15-cijferige **Pixel ID** (zoals `1234567890123456`)
5. Open admin → Marketing tab → plak bij **"Meta Pixel — Pixel ID"**
6. Save

#### Wat wordt automatisch getracked?
- **PageView** op elke pagina
- **Lead** event na succesvol contactformulier
- **Contact** event bij WhatsApp / telefoon / e-mail klik

#### Testen
1. Installeer Chrome extensie **"Meta Pixel Helper"**
2. Open jullie site in nieuw incognito → "Akkoord" met cookies
3. Pixel Helper-icoontje moet "1 pixel found" tonen
4. Klik op WhatsApp-knop → moet "Contact event" laten zien
5. In Events Manager → Test Events tab — zie events live binnenkomen

---

### Setup 3 — Google Ads conversion tracking (20 min)

Voor wie advertenties draait via Google Ads. Hiermee weet je welke advertentie / zoekwoord de meeste leads oplevert.

#### Voorvereiste
Je hebt een Google Ads-account nodig. Anders eerst aanmaken via **[ads.google.com](https://ads.google.com)**.

#### Stappen
1. In Google Ads → **Tools & Settings** → **Conversions**
2. **+ New conversion action** → **Website**
3. URL: `https://spinguard.nl` → klik "Scan"
4. **Add conversion action manually**:
   - Goal: **Lead**
   - Conversion name: **SpinGuard — Contact form lead**
   - Value: **€50** (gemiddelde waarde van een lead — pas aan)
   - Count: **One**
   - Click-through window: 30 dagen
5. Save → "Set up the tag" → **"Install the tag yourself"**
6. Je krijgt 2 codes te zien:
   - **Conversion ID** (zoals `AW-123456789`)
   - **Conversion label** (zoals `abcDEFghi123`)
7. Open admin → Marketing tab → plak beide bij **"Google Ads"**
8. Save

#### Testen
1. Wacht 24 uur na setup (Google heeft tijd nodig)
2. Vul je eigen contactformulier in op de site
3. Volgende dag: in Google Ads → Conversions → check of je je test-lead ziet

---

### Setup 4 — Google Tag Manager (geavanceerd, vervangt al het bovenstaande)

Voor wie meer dan 2 platforms gebruikt of zelf custom events wil definiëren. **Niet verplicht** — gebruik dit alleen als je veel marketing-tools hebt.

#### Voordeel
- 1 ID in jullie site
- Beheer GA4, Meta Pixel, Google Ads, Hotjar, etc. via tagmanager.google.com
- Kan in/uitschakelen zonder code-wijzigingen
- A/B testen, custom events, scroll tracking

#### Stappen
1. **[tagmanager.google.com](https://tagmanager.google.com)** → New Account
2. Account: SpinGuard → Container: spinguard.nl → Web
3. Kopieer de **GTM Container ID** (`GTM-XXXXXXX`)
4. Plak in admin → Marketing tab → "GTM Container ID"
5. Save

#### Tags configureren in GTM
In tagmanager.google.com voor jullie container:

**Tag 1 — GA4:**
- Type: Google Analytics: GA4 Configuration
- Measurement ID: G-XXXXXXXXXX
- Trigger: All Pages

**Tag 2 — Meta Pixel:**
- Type: Custom HTML
- Plak Meta Pixel base code
- Trigger: All Pages

**Tag 3 — Lead conversion (GA4):**
- Type: Google Analytics: GA4 Event
- Event Name: generate_lead
- Trigger: Custom Event → Event name: `lead_submitted` (de site stuurt dit auto bij sent=1)

**Tag 4 — Google Ads conversion:**
- Type: Google Ads Conversion Tracking
- Conversion ID + Label uit Google Ads
- Trigger: Custom Event → `lead_submitted`

#### Wat de site automatisch in dataLayer pusht
| Event naam | Wanneer |
|---|---|
| `lead_submitted` | Na succesvol contactformulier |
| `whatsapp_click` | Bij klik op WhatsApp-knop |
| `phone_click` | Bij klik op telefoonnummer |
| `email_click` | Bij klik op e-mailadres |
| `form_submit_attempt` | Bij verzenden formulier (voor redirect) |

---

### 🍪 Cookie consent banner

De site toont automatisch een cookie-banner aan elke nieuwe bezoeker:

> 🍪 **Cookies & privacy**
> Wij gebruiken essentiële cookies voor de werking van de site. Met uw toestemming gebruiken wij ook analyse- en marketing-cookies.
>
> [Alleen noodzakelijk] [Akkoord met alle]

#### Hoe het werkt
- **"Akkoord met alle"** → tracking-pixels laden direct + cookie 365 dagen bewaard
- **"Alleen noodzakelijk"** → geen tracking, banner verdwijnt
- Bezoeker moet pas opnieuw kiezen na 1 jaar

#### Optionele uitbreiding: gedetailleerde categorieën
De ingebouwde banner is bewust simpel ("aan/uit"). Wil je categorieën (analytics aan, marketing uit, etc.)?
1. Maak account bij **[CookieYes](https://www.cookieyes.com)** (gratis tier) of **[Cookiebot](https://www.cookiebot.com)**
2. Kopieer hun script
3. Plak in admin → Marketing → **"Custom code in &lt;head&gt;"**
4. In admin → Marketing → "Cookie banner": **uitzetten** (anders heb je 2 banners)

---

### 💡 Pro tips voor effectievere advertenties

#### 1. UTM-tags voor je advertentie-links
Als je een Facebook-ad maakt naar `https://spinguard.nl`, gebruik dan:
```
https://spinguard.nl/?utm_source=facebook&utm_medium=ad&utm_campaign=spinnen-zomer
```
Dit verschijnt in GA4 zodat je weet welke campagne de leads opleverde.

#### 2. Custom audiences in Meta
Na 2-4 weken Meta Pixel data:
- Maak een **Custom Audience** van iedereen die `/contact.php` bezocht maar GEEN lead werd
- Run retargeting-ads naar die audience met 10% korting

#### 3. Optimaliseer voor "Lead" event in Meta
- In Meta Ads Manager → Campagne doel: **Conversions**
- Conversion event: **Lead**
- Meta zal jouw ads tonen aan mensen die het meest waarschijnlijk een lead worden

#### 4. Google Ads — bid op specifieke termen
Begin smal: **"spinnenbestrijding [stad]"** (lokaal) >> "spinnenbestrijding" (te duur)
Voor SpinGuard sterk: `spinnen weghalen Nijmegen`, `gevel reinigen spinnen`, `professionele spinbestrijding`

#### 5. Track ROI per kanaal
Met de juiste setup zie je in GA4:
- Bezoekers per kanaal (organic, ads, social)
- Conversies per kanaal
- Kost per lead per kanaal (als je in Google Ads doelen koppelt)

---

## 💾 Backups & onderhoud

### Wat de site automatisch doet
- ✅ Auto-backup van content bij elke save (laatste 5)
- ✅ Lead-log per maand in `content/leads/`
- ✅ Cache headers via `.htaccess` voor snelheid

### Wat jij maandelijks moet doen
- ✅ **Test het contactformulier** (verstuur een test, check inbox)
- ✅ **Check Google Search Console** voor errors
- ✅ **Update reviews** met nieuwe klant-feedback
- ✅ **Backup downloaden** (zie hieronder)

### Volledige backup maken (5 min, eens per kwartaal)
Via FTP/DirectAdmin → download deze 2 mappen naar je computer:
- 📁 `content/` (alle teksten + leads + backups)
- 📁 `uploads/` (alle foto's)

Bewaar in een Dropbox / Google Drive map. Klaar.

### Bij hosting-overstap (zelden)
1. Download alle bestanden van oude hosting
2. Upload naar nieuwe hosting
3. Schrijfrechten opnieuw instellen (stap 3)
4. DNS aanpassen (stap 5)
5. SSL opnieuw genereren (stap 6)
6. Klaar

---

## 🆘 Troubleshooting

### "Mijn site is helemaal wit / geeft 500 error"
- **Oorzaak**: meestal `.htaccess` issue
- **Fix**: hernoem `.htaccess` tijdelijk naar `.htaccess.bak`. Werkt? Dan is het Apache-config. Vraag hosting-support.

### "E-mails komen niet aan"
1. Check spam-folder
2. Login `/admin/leads.php` — staan ze daar wel? → Mail-config issue, niet jullie probleem als bedrijf (gewoon admin volgen)
3. Vraag hosting-support: "Werkt PHP `mail()` op mijn pakket?"
4. Als alternatief: SMTP via PHPMailer (vraag developer)

### "Ik kan niet inloggen op admin"
- **Verkeerd wachtwoord**: open via FTP `admin/config.php`, leeg de waarde tussen `''` bij `ADMIN_PASSWORD_HASH`. Daarna `/admin/` opnieuw bezoeken om nieuw wachtwoord in te stellen.
- **Setup pagina blijft komen**: file `admin/config.php` is niet schrijfbaar. Stel chmod 644 in.

### "Foto's uploaden faalt"
- Map `uploads/` heeft geen schrijfrechten — chmod 755 instellen.

### "Wijzigingen niet zichtbaar"
- Browser cache — druk **Ctrl+Shift+R** (Windows) of **Cmd+Shift+R** (Mac)
- Of: open in incognito-venster om te checken

### "Site is plotseling offline"
- Check of je hosting-rekening betaald is
- Check via uptime-monitor (bv. **uptimerobot.com** — gratis)
- Als geen technisch probleem: bel je hostingprovider

### "Domein wijst nog naar Squarespace na 24u"
- DNS-cache → check via [dnschecker.org](https://dnschecker.org)
- Vul `spinguard.nl` in → kijk of het nieuwe IP wereldwijd zichtbaar is
- Geduld — het proces kan tot 48u duren

### "404 op /diensten in plaats van /diensten.php"
- mod_rewrite niet aan op je hosting
- Werkt wel met `.php` extensie
- Vraag hosting om mod_rewrite te activeren (vrijwel altijd standaard)

### "Mobiel ziet er raar uit"
- Browser cache — Ctrl+Shift+R
- Of: in DevTools (F12) → device toolbar (Ctrl+Shift+M) → kies iPhone/Android

### Algemene noodknop
Bel je hostingprovider:
- **TransIP**: 020 - 235 8585
- **Hostnet**: 088 - 446 8638
- **MijnHostingPartner**: 088 - 050 1010

Zij helpen vrijwel altijd binnen 5 minuten met basisproblemen.

---

## 📂 Wat zit er allemaal in?

```
spinguard/
├── 📄 index.php               Homepage
├── 📄 diensten.php            Diensten & prijzen
├── 📄 werkwijze.php           Hoe wij werken
├── 📄 over-ons.php            Over ons
├── 📄 contact.php             Contactformulier
├── 📄 styles.css              Vormgeving
├── 📄 sitemap.php             Voor Google (genereert sitemap.xml)
├── 📄 robots.txt              Voor zoekmachines
├── 📄 .htaccess               Apache-config (security, snelheid, redirects)
├── 📄 README.md               Snelle start
├── 📄 HANDLEIDING.md          Deze file
│
├── 📁 admin/                  Admin paneel
│   ├── index.php              Login
│   ├── setup.php              Eerste keer wachtwoord instellen
│   ├── dashboard.php          Hoofdscherm
│   ├── edit.php               Content bewerken
│   ├── photos.php             Foto's uploaden
│   ├── leads.php              Aanvragen-log
│   ├── wachtwoord.php         Wachtwoord wijzigen
│   ├── config.php             Auth config (NIET aanpassen)
│   ├── api-list-photos.php    JSON: lijst foto's
│   ├── api-upload-photo.php   JSON: upload foto
│   ├── save.php               Verwerkt content-aanpassingen
│   ├── logout.php
│   ├── css/admin.css          Admin vormgeving
│   └── js/admin.js            Admin interactiviteit
│
├── 📁 api/
│   └── contact.php            Verwerkt contactformulier (e-mail)
│
├── 📁 content/                Hier zit ALLE content
│   ├── site.json              Hoofdbestand met alle teksten/prijzen/etc.
│   ├── .htaccess              Beveiligt content tegen directe toegang
│   ├── leads/                 Backup van aanvragen (per maand)
│   └── backups/               Auto-backups van site.json
│
├── 📁 inc/                    Gedeelde PHP-helpers
│   ├── bootstrap.php          Init + helpers (icons, schemas)
│   ├── header.php             HTML <head> + nav (per pagina geladen)
│   ├── footer.php             Footer + scripts
│   └── contact_form.php       Herbruikbaar contactformulier
│
├── 📁 js/main.js              Frontend interactiviteit
├── 📁 assets/
│   └── spinguard-logo.png     Logo
└── 📁 uploads/                Hier komen geüploade foto's
```

---

## 📖 Glossarium

| Term | Wat is het? |
|---|---|
| **Hosting** | De server waar je site op draait |
| **Domein** | Je adres op het web (`spinguard.nl`) |
| **DNS** | Telefoonboek van het internet — wijst domein naar hosting |
| **A-record** | Type DNS-regel die een domein aan een IP-adres koppelt |
| **SSL** | Beveiliging (groene slotje + `https://`) |
| **PHP** | Programmeertaal waarin de site is gebouwd |
| **FTP** | Manier om bestanden te uploaden naar hosting |
| **Apache** | De software die je website serveert (standaard) |
| **DirectAdmin / cPanel** | Bedieningspaneel van je hosting |
| **`.htaccess`** | Configuratiebestand voor Apache |
| **Schema.org** | "Etiket" voor Google zodat hij snapt wat info betekent |
| **Sitemap** | Lijst van alle pagina's voor zoekmachines |
| **CSRF token** | Beveiliging tegen valse formulier-submissies |
| **JSON** | Bestandsformaat voor data (zoals `site.json`) |
| **Repository** | Geen — deze site is niet in Git, gewoon files |
| **CMS** | Content Management System — hier: het admin paneel |

---

## 🤝 Hulp nodig?

- **Technische issues** met de site zelf → vraag de developer die de site heeft opgeleverd
- **Hostingproblemen** (offline, e-mail, DNS) → bel je hostingprovider
- **Aanvragen via formulier komen niet aan** → check `/admin/leads.php` als backup, anders hosting-support
- **Wachtwoord vergeten** → zie [Troubleshooting](#-troubleshooting)
- **Backup terugzetten** → `content/backups/` — zie hieronder

---

## ✅ Eindcheck — werkt alles?

Doorloop deze lijst. Vink af. Klaar als alles ✓ is.

- [ ] Hosting besteld en welkomstmail ontvangen
- [ ] Bestanden geüpload naar `public_html/`
- [ ] Schrijfrechten ingesteld op `content/`, `uploads/`, `admin/config.php`
- [ ] Admin wachtwoord ingesteld (en bewaard in wachtwoordmanager!)
- [ ] Site werkt op test-URL (zonder DNS-aanpassing)
- [ ] DNS aangepast naar nieuwe hosting
- [ ] Spinguard.nl wijst naar nieuwe site (test in browser)
- [ ] SSL geactiveerd (groene slotje zichtbaar)
- [ ] HTTPS geforceerd (uncomment regels in `.htaccess`)
- [ ] Contact-formulier getest → e-mail aangekomen
- [ ] WhatsApp-knop getest
- [ ] Echte bedrijfsgegevens ingevuld in admin (KvK, BTW, etc.)
- [ ] Echte foto's geüpload (busje, team, voor/na werk)
- [ ] Echte reviews toegevoegd
- [ ] Lokale SEO ingevuld (stad, GPS, service-steden)
- [ ] Site toegevoegd aan Google Search Console
- [ ] Sitemap ingediend bij Google
- [ ] Google Business Profile aangemaakt + verificatie aangevraagd
- [ ] Squarespace-abonnement opgezegd (na 1 week — als alles werkt)

🎉 **Klaar! Veel succes met de leads.** 🕷️
