# Multiselect-modul (multiselect)

**Entry point:** `adm/multiselect.php`
**JS-filer:** `adm/js-functions/multiselect/*.js`
**Stilmall:** `adm/styles/multiselect.css`

## Syfte
Generisk komponent för att välja flera poster ur en databastabell via en
`<select multiple>`, tänkt att öppnas i en iframe/popup från ett annat
formulär (troligen `manage.php`). Håller reda på urvalsordning (senast
tillagd sist) och skickar tillbaka resultatet som en kommaseparerad sträng
till förälderfönstret via `postMessage`, för visning i ett textarea-fält
där.

All PHP-logik ligger i entry point-filen – ingen egen funktionsmapp i
`functions/`. Klientbeteendet (ordningshantering, toggle-klick,
förval, skicka-tillbaka) sköts av separata JS-filer.

## Anropas med
`multiselect.php?table=<textareaId>::<tabellnamn>:<aktuella värden>`

| Del | Beskrivning |
|---|---|
| `<textareaId>` | Id på textarea-elementet i förälderfönstret som ska ta emot urvalet |
| `<tabellnamn>` | Namnet på tabellen (i `map_configs`-schemat) att välja poster ur, t.ex. `layers`, `proj4defs` |
| `<aktuella värden>` | Kommaseparerad lista med redan valda värden (förifyller urvalet) |

Ingen JSON/REST – ren HTML-sida avsedd för iframe.

**Specialfall:** om `<tabellnamn>` är `proj4defs` används kolumnen `code`
som id-kolumn istället för det generella mönstret `<singularis>_id`.

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `dbh()` – databasanslutning
- `all_from_table($dbh, $schema, $table)` – hämtar alla rader från angiven
  tabell. **OBS:** schemat är här hårdkodat till `'map_configs'` istället
  för att läsas från `constants/configSchema.php` som i info-modulen – se
  flaggning nedan.
- `toSwedish($string)` – översätter tabellnamn till svensk rubrik

**JS-funktioner** (`adm/js-functions/multiselect/`, laddas inline via
`includeDirectory()` i en `<script>`-tagg – se separat avsnitt nedan)

**Databas:** läser alla rader från valfri tabell i `map_configs`-schemat,
namnet kommer direkt från `$_GET['table']`.

## JS-filer och funktioner

| Fil | Funktion | Beskrivning |
|---|---|---|
| `update.js` | `update(menu)` | Körs vid ändring i select-listan. Räknar ut vad som lagts till/tagits bort, håller ordning på urvalsordningen i `data-sorted-values`, uppdaterar textarean |
| `makeSelectToggleOnly.js` | `makeSelectToggleOnly(selectId)` | Fångar `mousedown` i capture-fas för att göra vanligt klick till "toggla ett alternativ" istället för webbläsarens standard shift/ctrl-rangebeteende |
| `selectOptionsByValues.js` | `selectOptionsByValues(selectId, optionValues)` | Förvalsmarkerar options baserat på en kommaseparerad sträng, körs vid sidladdning |
| `getCurrentSelection.js` | `getCurrentSelection()` | Läser och trimmar värdet från textarean `#selection` |
| `sendSelectionAndClose.js` | `sendSelectionAndClose(targetId)` | Läser aktuellt urval och skickar det + en `close`-signal till förälderfönstret via `postMessage` |
| `closeTopFrame.js` | `closeTopFrame()` | Skickar bara en `close`-signal (utan värde) till förälderfönstret |

**Beroendekedja mellan JS-filerna:** `makeSelectToggleOnly` anropar
`update` (global funktion, måste finnas laddad). `sendSelectionAndClose`
anropar `getCurrentSelection`. Eftersom alla filer i mappen laddas
tillsammans inline i samma `<script>`-block spelar filordningen inom
`includeDirectory()` ingen praktisk roll här, men vore det viktigt att
veta om filerna någonsin laddas separat.

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **⚠️ Hårdkodat schemanamn `'map_configs'`** i anropet till
  `all_from_table()`, till skillnad från info-modulen som använder
  `$configSchema` från `constants/configSchema.php`. Om schemat någonsin
  ändras (t.ex. olika miljöer, multi-tenant) kommer denna modul sluta
  fungera medan andra fortsätter fungera korrekt. Bör harmoniseras –
  sannolikt ska `require("./constants/configSchema.php")` läggas till
  och `'map_configs'` bytas mot `$configSchema`.
- **⚠️ `$table` (och därmed tabellnamnet i SQL-frågan, inuti
  `all_from_table`) kommer direkt från `$_GET['table']` utan whitelist
  eller validering mot en känd uppsättning tillåtna tabeller.** Vi har
  inte sett `all_from_table()`s implementation än, men om tabellnamnet
  klistras in direkt i SQL utan validering är detta en SQL
  injection-risk (eller åtminstone risk att exponera godtycklig
  tabelldata). Bör verifieras när vi dokumenterar `all_from_table.php`.
- **Bra exempel i övrigt:** all utskrift av användarstyrd data
  (`$textareaId`, `$currentValue`, `$header`, options-värden) går genom
  `htmlspecialchars()` innan det skrivs till HTML – till skillnad från
  flera tidigare moduler. Denna fil är alltså en förebild för
  XSS-skydd, värt att lyfta fram som referens vid förenkling av äldre
  filer.
- **Parsning av `$_GET['table']` är svårläst:** `explode('::', ..., 2)`
  följt av `explode(':', ..., 2)` för att packa upp tre värden ur en
  enda sträng med två olika separatorer (`::` och `:`) är en ovanlig och
  lätt förvirrande kodningsform. Kandidat för förenkling, t.ex. tre
  separata query-parametrar (`?textareaId=...&table=...&values=...`)
  istället för hopkodad sträng – skulle även göra `?`-anropet mer
  läsbart och mindre felbenäget vid framtida ändringar.
- **Inline `onclick`-JS med hopslagna strängar** i "Töm"-knappen
  (flera DOM-anrop i en enda `onClick`-attributsträng) är svårläst och
  bör brytas ut till en egen namngiven JS-funktion (t.ex. `clearSelection()`)
  i samma stil som övriga knappar.
- Kommentarer i `makeSelectToggleOnly.js` innehåller en attribuering
  till "GROK" (`// GROK: behövs för att undvika...`) — tyder på att viss
  kod genererats/felsökts med hjälp av ett annat AI-verktyg. Ofarligt,
  men värt att känna till som kontext.
