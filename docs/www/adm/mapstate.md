# Mapstate-modul (mapstate)

**Entry point:** `adm/mapstate.php`
**Funktionsfiler:** `adm/functions/mapstate/*.php`

## Syfte
Ett stateless JSON-API för att spara och återläsa "karttillstånd" (state) –
troligen kameraposition, synliga lager m.m. i en Origo-karta – identifierat
med ett genererat UUID. Används sannolikt för att generera delbara/bokmärkbara
länkar till en specifik kartvy. Rensar automatiskt bort gamla, oanvända
tillstånd vid varje anrop.

**OBS:** till skillnad från news-modulen finns **ingen inloggnings- eller
sessionskontroll** i denna fil. Endpointen är öppen för alla (CORS tillåter
`*`). Detta kan vara avsiktligt (frontend-kartan är publik och behöver kunna
spara/läsa state utan inloggning) men bör bekräftas – annars är det en
säkerhetslucka värd att åtgärda.

## Anropas med
Rent REST-liknande API, ingen `action`-parameter:

| Metod | Parametrar | Beskrivning | Svar |
|---|---|---|---|
| `POST` | JSON body (godtyckligt state-objekt) | Skapar ett nytt karttillstånd | `{"mapStateId": "<uuid>"}` |
| `GET`  | `?mapStateId=<uuid>` | Hämtar ett sparat karttillstånd | Rått JSON-innehåll (state) |
| `OPTIONS` | – | CORS-preflight | Tom 200 |
| annat | – | – | 405 `{"error": "Metod ej tillåten"}` |

Felsvar (400/404/500) returneras alltid som `{"error": "..."}`.

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `dbh()` – öppnar databasanslutning (enda common-funktionen som används här)

**Konstanter:**
- `constants/mapstateMaxUnused.php` → `$mapstateMaxUnused` (antal dagar innan
  ett oanvänt state städas bort, se `cleanupOldMapStates`)
- `constants/configSchema.php` → `$configSchema` (läses internt av
  `getMapStatesTable()`, med fallback till `'public'` om ej satt)

**Databas:** tabellen `<configSchema>.mapstates` med kolumner
`mapstate_id, state, created, lastuse, mapurl, preserve`.
(`preserve`-kolumnen förhindrar att en post städas bort av cleanup —
funktionen för att sätta `preserve` finns inte i denna modul, så den sätts
sannolikt från ett annat ställe i systemet, t.ex. manage-modulen.)

## Filer och funktioner

*Sorterad alfabetiskt efter filnamn.*

| Fil | Funktion | Beskrivning |
|---|---|---|
| `cleanupOldMapStates.php` | `cleanupOldMapStates($dbh, int $days): void` | Tar bort mapstates som är äldre än `$days` och oanvända, eller >30 dagar gamla och aldrig använda – om inte `preserve` är satt. Körs på **varje** request |
| `createMapState.php` | `createMapState($dbh): never` | Läser JSON från request body, genererar ett UUID v4, sparar i databasen, svarar med `mapStateId`. Avslutar alltid med `exit` |
| `getMapStatesTable.php` | `getMapStatesTable(): string` | Bygger (och cachar statiskt) det fullt kvalificerade, escapade tabellnamnet `schema.mapstates`. Delas av övriga funktioner i modulen |
| `retrieveMapState.php` | `retrieveMapState($dbh): never` | Validerar `mapStateId`, uppdaterar `lastuse`, hämtar och returnerar sparat state. Avslutar alltid med `exit` |
| `updateLastUse.php` | `updateLastUse($dbh, string $id): void` | Sätter `lastuse = NOW()` för ett givet id |
| `validateMapStateId.php` | `validateMapStateId(string $id): bool` | Validerar att id matchar UUID-formatet via regex |

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **⚠️ Ingen inloggningskontroll** — se OBS ovan under Syfte. Bör bekräftas
  om detta är avsiktligt.
- **⚠️ Öppen CORS-policy** (`Access-Control-Allow-Origin: *`) — tillåter
  anrop från vilken domän som helst. Rimligt om endpointen är avsedd att
  vara publik, men värt att notera som medvetet vägval snarare än
  standardinställning.
- **`$id` i `retrieveMapState.php`/`updateLastUse.php` valideras via regex
  men escapas inte** när den byggs in i SQL-strängen (`WHERE mapstate_id =
  '$id'`). Eftersom `validateMapStateId()` strikt begränsar formatet till
  hexadecimala tecken och bindestreck är detta i praktiken säkert idag,
  men är en annan säkerhetsstil än `createMapState.php` som konsekvent
  använder `pg_escape_literal()`. Bör harmoniseras vid refaktorering –
  antingen alltid `pg_escape_literal()`/parameteriserade frågor, eller en
  tydlig kommentar om varför regex-validering anses tillräcklig här.
- **`updateLastUse()` litar på att anroparen redan validerat `$id`** —
  funktionen har ingen egen validering. Fungerar idag eftersom enda
  anroparen (`retrieveMapState`) validerar innan anrop, men är sårbart om
  funktionen återanvänds någon annanstans utan samma försiktighet.
- **`mapstate.php` anropar `pg_close($dbh)` efter OPTIONS-svar** även om
  `$dbh` skulle vara `false` (databasanslutning misslyckad) — `dbh()`
  anropas *innan* OPTIONS-kontrollen, så ett `pg_close(false)`-anrop är
  tekniskt möjligt om anslutningen faller på just en OPTIONS-request.
  Litet ofarligt men värt att känna till.
- **UUID genereras i PHP med `mt_rand()`** snarare än en kryptografiskt
  säker källa (`random_bytes()`) eller databasens `gen_random_uuid()`.
  Sannolikt tillräckligt bra för detta syfte (inte säkerhetskritiskt),
  men avviker från best practice för UUID-generering.
- Ingen av filerna har `declare(strict_types=1)` överst, trots att
  parametrar och returvärden är typade — typkontroll är alltså inte strikt
  trots typdeklarationerna.
