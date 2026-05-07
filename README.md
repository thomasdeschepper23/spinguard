# 🕷️ SpinGuard Website

Een complete, mobielvriendelijke website voor SpinGuard — met admin paneel, contactformulier en SEO-optimalisaties.

---

## 🚀 Snel beginnen

> **👉 Lees [HANDLEIDING.md](HANDLEIDING.md) voor de volledige stap-voor-stap gids.**
> Daar staat alles in: van hosting kiezen, ZIP uploaden, domein verbinden, SSL aanzetten, tot leads ontvangen.

### TL;DR — in 4 stappen
1. **Hosting bestellen** met PHP 8 (TransIP/Hostnet/etc., ±€3/maand)
2. **ZIP uploaden** naar `public_html/` via FTP of bestandsbeheer
3. **DNS aanpassen** zodat `spinguard.nl` naar nieuwe hosting wijst
4. **Open `/admin/`** → wachtwoord instellen → klaar

Tijd: ~1-2 uur.

---

## 🎯 Wat zit erin?

- ✅ 5 paginas: Home, Diensten, Werkwijze, Over ons, Contact
- ✅ Admin paneel om alles aan te passen (geen code)
- ✅ Contact-formulier → e-mail naar `info@spinguard.nl`
- ✅ WhatsApp-knoppen overal (klik → gesprek)
- ✅ SEO geoptimaliseerd (Schema.org, Open Graph, sitemap)
- ✅ Lead-backup in `content/leads/` (voor het geval mail faalt)
- ✅ Auto-backups van content
- ✅ Mobiel-vriendelijk
- ✅ Drag & drop foto-upload
- ✅ Visuele image picker voor before/after

---

## 📋 Belangrijke documenten

| Bestand | Inhoud |
|---|---|
| **[HANDLEIDING.md](HANDLEIDING.md)** | ⭐ **Hoofd-document.** Complete A-Z gids van zip-bestand tot werkende site met leads. |
| README.md | Deze file (snel overzicht) |

---

## 🔧 Vereisten van de hosting

- **PHP 7.4+** (alle moderne hostings hebben dit)
- **Apache** met `mod_rewrite` (standaard)
- **Schrijfrechten** op `content/` en `uploads/` (chmod 755)
- **PHP `mail()`** geactiveerd voor contact-formulier (standaard)

> 🚫 **Werkt NIET op**: Squarespace, Wix, Webflow (die staan geen externe code toe).

---

## 🆘 Snelle hulp

- 🐛 **Iets werkt niet?** → [HANDLEIDING.md → Troubleshooting](HANDLEIDING.md#-troubleshooting)
- 📧 **Geen e-mails?** → Check `/admin/leads.php` voor backup-log
- 🔑 **Wachtwoord vergeten?** → Open `admin/config.php` via FTP, leeg de hash, ga naar `/admin/`
- 🌐 **Site offline?** → Bel je hostingprovider

---

Gemaakt met ❤️ voor SpinGuard. Veel succes met de leads! 🕷️
