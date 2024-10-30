=== Clonable - Oversæt Woocommerce / WordPress-websted. Flersproget på 5 minutter ===
Contributors: clonable
Tags: oversættelser, oversæt, flersproget, clonable, seo
Requires at least: 5.0
6.6.2
Requires PHP: 7.2
Stable tag: 2.2.6
License: GPL v2 or later

Oversæt og vedligehold problemfrit dine flersprogede hjemmesider. Fremskynd og forenkl din internationalisering med Clonable.

== Description ==
Online internationalisering uden besvær:  Fremskynd og forenkl dine oversættelsesprocesser. Din flersprogede hjemmeside opdateres automatisk.

= Udfordringen =
Det kan være både dyrt og tidskrævende at oversætte et WordPress/Woocommerce-site. Ud over den indledende oversættelsesindsats er løbende vedligeholdelse afgørende for at sikre, at den oversatte version forbliver aktuel med nyt indhold. Derfor bliver oversættelser ofte overset eller forsømt, når de først er lavet, på trods af det store potentiale på udenlandske markeder.

= Vores løsning =
Vi introducerer Clonable, en banebrydende løsning til ejere af WordPress-hjemmesider. Clonable gør det muligt ubesværet at oprette "kloner" af dine WordPress-hjemmesider og sikre, at de konsekvent er synkroniseret med den originale version. Alle ændringer, der foretages på den oprindelige side, afspejles øjeblikkeligt i klonen, hvilket reducerer vedligeholdelsesomkostningerne betydeligt. Med Clonable kan du generere en website-klon på bare 5 minutter, hvilket drastisk reducerer time-to-market.

= Nøglefunktioner =
1. SEO-forbedring: Vores plugin tilføjer sømløst sprogtags til head-sektionen på alle dine sider. Dette sikrer, at dine kloner og det originale site er korrekt forbundet for at forbedre SEO-performance. Denne funktionalitet omfatter både oversatte og ikke-oversatte kloner og understøtter også kloner af undermapper.
2. Ubesværet integration af undermapper: Clonable giver dig mulighed for ubesværet at integrere en klon i en undermappe på din hjemmeside, hvilket eliminerer behovet for komplekse tekniske konfigurationer i WordPress.
3. Brugervenlig sprogomskifter: Clonable tilbyder også en intuitiv sprogomskifter, så brugerne nemt kan navigere mellem de forskellige sprog, der er tilgængelige på din hjemmeside.
4. Understøttelse af Woocommerce for nemmere konverteringssporing på forskellige sprog.

== Installation ==
Du skal bruge en [Clonable-konto] (https://app.clonable.net/register) for at kunne bruge pluginet. Når du har forbundet pluginet til din Clonable-konto, synkroniseres indstillingerne automatisk med din WordPress-installation.
automatisk synkronisere med din WordPress-installation.

Hvis indstillingerne ikke er synkroniseret korrekt, kan du gøre det manuelt ved at trykke på knappen 'Synkroniser med Clonable' på siden med de generelle indstillinger i pluginet.

== Changelog ==
v2.2.6
Bug fix: timeouts

v2.2.5
Improve circuit breaker logic

v2.2.4
Retter fejl med forkert gemmeplacering i sprogkoder.

v2.2.3
Rettet et problem i sprogkoderne i klonindstillingerne

v2.2.2
Indlejret videoer i plugin'et.
Tilføjet sprogindstillinger til sprog-tags

v2.2.1
Forbedringer af circuit breaker følsomhed.

v2.2.0
Tilføjet understøttelse af WordPress 6.6 og hævet minimumsversionen for PHP.
Tilføjet circuit breaker til undermappekommunikation med Clonable.
Tilføjet indstillingsvindue til aktivering og deaktivering af specifikke tjenester.

v2.1.16
Bedre standardværdier for klonbare indstillinger.
Tilføjet mulighed for at få lokaliteten for klonerne ved hjælp af WordPress' get_locale()-funktion.
Bugfix: Deaktiveret proxy-loop for websteder med flere undermapper.

v2.1.15
Forbedringer af ydeevnen for API-kommunikation

v2.1.14
Bugfix: Fix af debounce-algoritme
Bumped testet op til version

v2.1.13
Bugfix: Løste problem med norsk flag, som ikke kunne vælges i sprogomskifteren.

v2.1.12
Bug fix

v2.1.11
Understøttelse af site_url og home_url med en undermappe.

v2.1.10
Performance-forbedringer til admin-grænsefladen.

v2.1.9
Bugfix: Rettet mismatch i 'sync with Clonable'-knappen for domæner, der bruger www. Tilføjet et notifikationssystem for bedre indsigt i baggrundsopgaver. Forbedret stabilitet for interne hooks.

v2.1.8
Bugfix: Læs brugerdata korrekt, når indholdstypen er multipart/form-data

v2.1.7
Bugfix: Rettet omdirigeringsadfærd for undermappekloner.

v2.1.6
Oversættelsesforbedringer for undermappekloner.

v2.1.5
Bugfixes og ydeevneforbedringer.

v2.1.4
Bugfix: håndterer HTTP-metoder forskelligt og retter ugyldig header for indholdslængde.

v2.1.3
Bugfix: edge case med Mollie-betalingsudbyderen og domænebaserede kloner, der forårsager indirekte omdirigeringer

v2.1.2
Bedre understøttelse af fejlsporing ved konfigurationsfejl i undermapper.
Rettet formateringsfejl.

v2.1.1
Bug fix

v2.1.0
Flere fejlrettelser
Forbedret understøttelse af WooCommerce:
- Tilføjet produktudelukkelser for WooCommerce-produkter.
- Forbedret konverteringssporing for undermappe-kloner.
- Præcisering af eksisterende Analytics/WooCommerce-moduler.

v2.0.7
Fejlrettelse i indstillingsskærme for sprogkoder i kombination med nogle performance-plug-ins

v2.0.6
Fejlrettelse i opsætning
Opdateret til WordPress 6.4-kompatibilitetsniveau

v2.0.5
Forbedringer af ydeevnen.

v2.0.4
Rettet en fejl for input på den oprindelige side, hvor input ikke blev renset korrekt.

v2.0.3
Rettet en fejl, hvor nogle funktionsnavne kunne være i konflikt med andre plugins.

v2.0.2
Rettet flere fejl i oversættelserne af sprog-tags.

v2.0.1
Rettet en fejl, hvor api-nøglefejl ikke blev håndteret korrekt under forbindelsen til webstedet.
Tilføjet en knap til at afbryde forbindelsen til Clonable-pluginet.

v2.0.0
Ny hovedversion af pluginet.
- Tilføjet indstillinger for sprogomskifter.
- Tilføjet forbindelse til kontrolpanelet.
- Forbedret brugervenlighed af indstillingerne for sprogtag.
- Tilføjet registreringsproces for nye brugere.
- Tilføjet automatisk undermappekonfiguration til undermappekloner.

v1.3.0
Tilføjet mulighed for at slå url-oversættelse fra for sprogtags.

v1.2.4:
Rettet en fejl, hvor et tomt domænefelt ville blive set som ugyldigt input.

v1.2.3:
Fikset, at besøgende ikke vendte tilbage til indkøbskurvsiden, når betalingen blev annulleret, mens de brugte Mollie.

v1.2.2:
Fix crash på nogle installationer, når man forsøger at opdage, om woocommerce er installeret eller ej.

v1.2.1:
Tilføj kompatibilitet med Mollie Payment Gateway.

v1.2.0:
Tilføj integration med Woocommerce for nemmere konverteringssporing på klonede sider.

v1.1.2:
Forbedret cache-hitrate og finjusteret backoff-algoritme.

v1.1.1:
Retter et nedbrud, når indstillingerne gemmes

v1.1.0:
Brug oversatte versioner af url'er i sprogtags

v1.0.2:
Retter kompatibilitet med Wordpress 6.0
Retter forkerte sprogtags i nogle tilfælde

v1.0.1:
Retter kompatibilitet med Wordpress < 5.9

v1.0.0:
Første udgivelse