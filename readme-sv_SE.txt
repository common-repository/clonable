=== Clonable - Översätt Woocommerce / WordPress-webbplats. Flerspråkig på 5 minuter. ===
Contributors: clonable
Tags: översättningar, översätta, flerspråkig, clonable, seo
Requires at least: 5.0
6.6.2
Requires PHP: 7.2
Stable tag: 2.2.6
License: GPL v2 or later

Översätt och underhåll dina flerspråkiga webbplatser på ett smidigt sätt. Snabba upp och förenkla din internationalisering med Clonable.

== Description ==
Internationalisering online utan krångel:  Snabba upp och förenkla dina översättningsprocesser. Din flerspråkiga webbplats uppdateras automatiskt.

= Utmaningen =
Att översätta en WordPress/Woocommerce-webbplats kan vara både kostsamt och tidskrävande. Utöver det inledande översättningsarbetet krävs löpande underhåll för att se till att den översatta versionen är uppdaterad med nytt innehåll. Därför förbises eller försummas ofta översättningar efter att de har skapats, trots den stora potentialen på utländska marknader.

= Vår lösning =
Vi presenterar Clonable, en banbrytande lösning för WordPress-webbplatsägare. Clonable gör det möjligt att enkelt skapa "kloner" av dina WordPress-webbplatser, vilket säkerställer att de är konsekvent synkroniserade med originalversionen. Alla ändringar som görs på den ursprungliga webbplatsen återspeglas omedelbart i klonen, vilket avsevärt minskar underhållskostnaderna. Med Clonable kan du skapa en klonad webbplats på bara 5 minuter, vilket drastiskt minskar time-to-market.

= Viktiga funktioner =
1. SEO-förbättring: Vårt plugin lägger sömlöst till språktaggar i huvudsektionen på alla dina sidor. Detta säkerställer att dina kloner och den ursprungliga webbplatsen är korrekt länkade för förbättrad SEO-prestanda. Denna funktionalitet sträcker sig till både översatta och icke-översatta kloner och stöder även kloner av undermappar.
2. Enkel integration av undermappar: Clonable ger dig möjlighet att enkelt integrera en klon i en undermapp på din webbplats, vilket eliminerar behovet av komplexa tekniska konfigurationer inom WordPress.
3. Användarvänlig språkväxlare: Clonable erbjuder också en intuitiv språkväxlare som gör det möjligt för användare att enkelt navigera mellan de olika språk som finns tillgängliga på din webbplats.
4. Stöd för Woocommerce för enklare konverteringsspårning på olika språk.

== Installation ==
Du behöver ett [Clonable-konto](https://app.clonable.net/register) för att kunna använda pluginet. När du har anslutit pluginet till ditt Clonable-konto kommer inställningarna automatiskt att
automatiskt synkroniseras med din WordPress-installation.

Om inställningarna inte har synkroniserats korrekt kan du göra detta manuellt genom att trycka på knappen "Synkronisera med Clonable" på sidan för allmänna inställningar i insticksprogrammet.

== Changelog ==
v2.2.6
Bug fix: timeouts

v2.2.5
Improve circuit breaker logic

v2.2.4
Åtgärdat fel med felaktig lagringsplats i språktaggar.

v2.2.3
Åtgärdat ett problem i språktaggarna för kloninställningarna

v2.2.2
Inbäddade videor i plugin-programmet.
Lagt till alternativ för endast språk till språktaggarna

v2.2.1
Förbättrad känslighet hos kretsbrytare.

v2.2.0
Lagt till stöd för WordPress 6.6 och höjt lägsta version för PHP.
Lagt till brytare för undermappskommunikation med Clonable.
Inställningsfönster för aktivering och inaktivering av specifika tjänster har lagts till.

v2.1.16
Bättre standardvärden för Clonable-alternativ.
Lagt till alternativ för att hämta klonernas språk med hjälp av WordPress get_locale()-funktionen.
Buggfix: Inaktiverad proxyslinga för webbplatser med flera undermappar.

v2.1.15
Prestandaförbättringar för API-kommunikation

v2.1.14
Buggfix: Debounce-algoritm fixad
Bumped testad upp till version

v2.1.13
Buggfix: Löste problem med norsk flagga som inte kunde väljas för språkväxlaren.

v2.1.12
Bug fix

v2.1.11
Stöd för site_url och home_url med en underkatalog.

v2.1.10
Prestandaförbättringar för admin-gränssnittet.

v2.1.9
Bugfix: Fixad felmatchning i knappen "Synkronisera med Clonable" för domäner som använder www. Lade till ett meddelandesystem för bättre insikter i bakgrundsuppgifter. Förbättrad stabilitet för interna krokar.

v2.1.8
Bugfix: Läs användardata korrekt när innehållstypen är multipart/form-data

v2.1.7
Bugfix: Fast omdirigeringsbeteende för kloner av undermappar.

v2.1.6
Förbättringar av översättningen för kloner av undermappar.

v2.1.5
Bugfixes och prestandaförbättringar.

v2.1.4
Bugfix: hanterar HTTP-metoder på olika sätt och fixade ogiltig innehållslängdsrubrik.

v2.1.3
Buggfix: Edge Case med betalningsleverantören Mollie och domänbaserade kloner som orsakar indirekta omdirigeringar

v2.1.2
Bättre stöd för felsökning vid konfigurationsfel i undermappar.
Fixat formateringsfel.

v2.1.1
Bug fix

v2.1.0
Flera buggfixar
Förbättrat stöd för WooCommerce:
- Lagt till produktundantag för WooCommerce-produkter.
- Förbättrad konverteringsspårning för kloner av undermappar.
- Förtydligande av befintliga Analytics/WooCommerce-moduler.

v2.0.7
Buggfix i inställningsskärmar för språktaggar i kombination med vissa prestandaplugins

v2.0.6
Buggfix i installationen
Uppdaterad till WordPress 6.4-kompatibilitetsnivå

v2.0.5
Prestandaförbättringar.

v2.0.4
Fixade en bugg för inmatningen på den ursprungliga webbplatsen, där inmatningen inte sanerades korrekt.

v2.0.3
Åtgärdat ett fel där vissa funktionsnamn kunde vara i konflikt med andra plugins.

v2.0.2
Flera buggar i översättningarna av språktaggar åtgärdade

v2.0.1
Åtgärdat ett fel där api-nycklar inte hanterades korrekt under anslutningen till webbplatsen.
Lade till en knapp för att koppla bort Clonable-pluginet.

v2.0.0
Ny huvudversion av pluginet.
- Lagt till inställningar för språkväxlare.
- Lagt till anslutning till kontrollpanelen.
- Förbättrad användbarhet för inställningarna för språktaggar.
- Lagt till registreringsprocess för nya användare.
- Lagt till automatisk undermappskonfiguration för undermappskloner.

v1.3.0
Lagt till alternativ för att stänga av url-översättning för språktaggar.

v1.2.4:
Fixade ett fel där ett tomt domänfält skulle ses som ogiltig inmatning.

v1.2.3:
Fixade besökare som inte återvände till kundvagnssidan när betalningen avbröts medan de använde Mollie.

v1.2.2:
Fixa krasch på vissa installationer när man försöker upptäcka om woocommerce är installerat eller inte.

v1.2.1:
Lägg till kompatibilitet med Mollie Payment Gateway.

v1.2.0:
Lägg till integration med Woocommerce för enklare konverteringsspårning på klonade webbplatser.

v1.1.2:
Förbättrad cache-träfffrekvens och finjusterad backoff-algoritm.

v1.1.1:
Fixa en krasch när du sparar inställningarna

v1.1.0:
Använd översatta versioner av webbadresser i språktaggar

v1.0.2:
Fixa kompatibilitet med Wordpress 6.0
Fixar felaktiga språktaggar i vissa fall

v1.0.1:
Fixa kompatibilitet med Wordpress < 5.9

v1.0.0:
Första utgåvan