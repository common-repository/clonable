=== Clonable - Oversett Woocommerce / WordPress-nettsted. Flerspråklig på 5 minutter ===
Contributors: clonable
Tags: oversettelser, oversette, flerspråklig, clonable
Requires at least: 5.0
6.6.2
Requires PHP: 7.2
Stable tag: 2.2.6
License: GPL v2 or later

Sømløs oversettelse og vedlikehold av flerspråklige nettsteder. Gjør internasjonaliseringen raskere og enklere med Clonable.

== Beskrivelse ==
Internasjonalisering på nett uten problemer:  Gjør oversettelsesprosessene raskere og enklere. Det flerspråklige nettstedet oppdateres automatisk.

== Utfordringen ==
Å oversette et WordPress/Woocommerce-nettsted kan være både kostbart og tidkrevende. I tillegg til den første oversettelsen er det viktig med løpende vedlikehold for å sikre at den oversatte versjonen er oppdatert med nytt innhold. Derfor blir oversettelser ofte oversett eller forsømt etter at de er laget, til tross for det store potensialet i utenlandske markeder.

= Vår løsning =
Vi presenterer Clonable, en banebrytende løsning for eiere av WordPress-nettsteder. Clonable gjør det enkelt å lage "kloner" av WordPress-nettstedene dine, slik at de alltid er synkronisert med originalversjonen. Alle endringer som gjøres på det opprinnelige nettstedet, gjenspeiles umiddelbart i klonen, noe som reduserer vedlikeholdskostnadene betydelig. Med Clonable kan du generere en klon av nettstedet på bare 5 minutter, noe som reduserer time-to-market drastisk.

= Viktige funksjoner =
1. SEO-forbedring: Vår plugin legger sømløst til språkkoder i head-delen av alle sidene dine. Dette sikrer at klonene dine og det opprinnelige nettstedet er riktig koblet sammen for å forbedre SEO-ytelsen. Denne funksjonaliteten omfatter både oversatte og ikke-oversatte kloner og støtter også kloner av undermapper.
2. Enkel integrering av undermapper: Med Clonable kan du enkelt innlemme en klone i en undermappe på nettstedet ditt, slik at du slipper kompliserte tekniske konfigurasjoner i WordPress.
3. Brukervennlig språkveksler: Clonable tilbyr også en intuitiv språkveksler, slik at brukerne enkelt kan navigere mellom de ulike språkene som er tilgjengelige på nettstedet ditt.
4. Støtte for Woocommerce for enklere konverteringssporing på ulike språk.

== Installation ==
Du trenger en [Clonable-konto] (https://app.clonable.net/register) for å kunne bruke plugin-modulen. Når du har koblet plugin-modulen til Clonable-kontoen din, vil innstillingene automatisk synkroniseres med
automatisk synkroniseres med WordPress-installasjonen din.

Hvis innstillingene ikke er synkronisert korrekt, kan du gjøre dette manuelt ved å trykke på knappen "Synkroniser med Clonable" på siden for generelle innstillinger i plugin-modulen.

== Changelog ==
v2.2.6
Bug fix: timeouts

v2.2.5
Improve circuit breaker logic

v2.2.4
Fikser feil lagringsplassering i språkkoder.

v2.2.3
Løste et problem med språkkodene i kloninnstillingene

v2.2.2
Innebygde videoer i plugin-modulen.
Lagt til språkalternativer til språkkodene

v2.2.1
Forbedret følsomhet for circuit breaker.

v2.2.0
Lagt til støtte for WordPress 6.6 og økt minimumsversjonen for PHP.
Lagt til circuit breaker for undermappekommunikasjon med Clonable.
Lagt til innstillingsvindu for å aktivere og deaktivere spesifikke tjenester.

v2.1.16
Bedre standardverdier for klonbare alternativer.
Lagt til mulighet for å få lokaliteten til klonene ved hjelp av WordPress get_locale()-funksjonen.
Feilretting: Deaktivert proxy-loop for nettsteder med flere undermapper.

v2.1.15
Forbedret ytelse for API-kommunikasjon

v2.1.14
Feilretting: Retting av debounce-algoritme
Bumped testet opp til versjon

v2.1.13
Feilretting: Løste et problem med norsk flagg som ikke kunne velges for språkveksleren.

v2.1.12
Bug fix

v2.1.11
Støtte for site_url og home_url med underkatalog.

v2.1.10
Ytelsesforbedringer i administrasjonsgrensesnittet.

v2.1.9
Feilretting: Fikset feil i "synkroniser med Clonable"-knappen for domener som bruker www. Lagt til et varslingssystem for bedre innsikt i bakgrunnsoppgaver. Forbedret stabilitet for interne kroker.

v2.1.8
Feilretting: Leser brukerdata riktig når innholdstypen er multipart/form-data

v2.1.7
Feilretting: Fikset omdirigeringsadferd for undermappekloner.

v2.1.6
Forbedringer i oversettelsen av undermappekloner.

v2.1.5
Bugfixes og ytelsesforbedringer.

v2.1.4
Feilretting: håndterer HTTP-metoder annerledes og fikset ugyldig header for innholdslengde.

v2.1.3
Feilrettelse: Edge case med Mollie-betalingsleverandøren og domenebaserte kloner som forårsaker indirekte viderekoblinger

v2.1.2
Bedre støtte for feilsøking ved konfigurasjonsfeil i undermapper.
Rettet formateringsfeil.

v2.1.1
Bug fix

v2.1.0
Flere feilrettinger
Forbedret støtte for WooCommerce:
- Lagt til produktekskluderinger for WooCommerce-produkter.
- Forbedret konverteringssporing for undermappekloner.
- Avklart eksisterende Analytics/WooCommerce-moduler.

v2.0.7
Feilretting i skjermbildene for innstilling av språkkoder i kombinasjon med noen ytelses-plugin-moduler.

v2.0.6
Feilretting i oppsettet
Oppdatert til kompatibilitetsnivå for WordPress 6.4

v2.0.5
Ytelsesforbedringer.

v2.0.4
Fikset en feil for inndataene på det opprinnelige nettstedet, der inndataene ikke ble renset korrekt.

v2.0.3
Rettet en feil der noen funksjonsnavn kunne komme i konflikt med andre plugins.

v2.0.2
Fikset flere feil i oversettelsene av språktagger.

v2.0.1
Rettet en feil der feil i api-nøkler ikke ble håndtert korrekt under tilkoblingen til nettstedet.
Lagt til en knapp for å koble fra Clonable-plugin-modulen.

v2.0.0
Ny hovedversjon av plugin-modulen.
- Lagt til innstillinger for språkveksler.
- Lagt til tilkobling til kontrollpanelet.
- Forbedret brukervennligheten til språkinnstillingene.
- Lagt til registreringsprosess for nye brukere.
- Lagt til automatisk undermappekonfigurasjon for undermappekloner.

v1.3.0
Lagt til mulighet for å slå av url-oversettelse for språkkoder.

v1.2.4:
Fikset en feil der et tomt domenefelt ble sett på som ugyldig input.

v1.2.3:
Fikset at besøkende ikke kom tilbake til handlekurv-siden når betalingen ble kansellert ved bruk av Mollie.

v1.2.2:
Fikset krasj på noen installasjoner når du prøver å finne ut om woocommerce er installert eller ikke.

v1.2.1:
Legg til kompatibilitet med Mollie Payment Gateway.

v1.2.0:
Legg til integrasjon med Woocommerce for enklere konverteringssporing på klonede nettsteder.

v1.1.2:
Forbedret treffrate for cache og justert backoff-algoritme.

v1.1.1:
Fikset en krasj ved lagring av innstillinger

v1.1.0:
Bruk oversatte versjoner av nettadresser i språkkoder.

v1.0.2:
Fikser kompatibilitet med Wordpress 6.0
Fikser feil språkkoder i noen tilfeller

v1.0.1:
Fikser kompatibilitet med Wordpress < 5.9

v1.0.0:
Første utgivelse