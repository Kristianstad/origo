# Read DB schemas-modul (read_db_schemas)

**Entry point:** `adm/read_db_schemas.php`
**Funktionsfiler:** `adm/functions/read_db_schemas/*.php`
**Anropas troligen från:** en knapp i manage-modulen (se
`functions/manage/printReadDbSchemasButton.php`, ej dokumenterad ännu)

## Syfte
Ansluter till en extern, i förväg registrerad databas (identifierad via
`database_id`, med anslutningssträng lagrad i konfigurationsdatabasens
`databases`-tabell) och listar dess Postgres-scheman. Varje hittat schema
registreras i konfigurationsdatabasens `schemas`-tabell (om det inte redan
finns) i formatet `<database_id>.<schema_name>`. Detta gör de externa
schemana valbara i övriga delar av adminpanelen (t.ex. vid konfiguration
av databaskällor för kartlager).

Ger inget svar till klienten vid lyckat resultat (svaret är
utkommenterat i koden) – detta är alltså troligen ett "fire and forget"-
anrop, inte en sida menad att visas för en användare.

## Anropas med
`read_db_schemas.php?database=<database_id>`

| Parameter | Beskrivning |
|---|---|
| `database` | Id för en tidigare registrerad databas (`databases`-tabellens `database_id`) |

**Svar vid fel:**
- 400 om `database`-parametern saknas
- 404 om `database_id` inte finns i `databases`-tabellen
- 500 vid databasfel

**Svar vid lyckat resultat:** inget innehåll (200, tom body) – se ovan.

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `dbh($connectionString = null)` – **OBS: `dbh()` kan ta emot en
  anslutningssträng som argument** för att ansluta till en *annan*
  databas än standardkonfigurationen. Denna modul är första stället vi
  sett detta – tidigare moduler har bara anropat `dbh()` utan argument
  (ansluter till standard-/konfigurationsdatabasen). Uppdaterat i
  `common.md`.
- `all_from_table($dbh, $schema, $table)` – hämtar alla rader ur
  `databases`-tabellen
- `array_column_search($value, $column, $rows)` – slår upp raden för
  angivet `database_id`

**Konstanter:**
- `constants/configSchema.php` → `$configSchema`

**Databas:** läser `<configSchema>.databases` (kolumner inkl.
`database_id`, `connectionstring`), skriver till
`<configSchema>.schemas` (kolumn `schema_id`). Ansluter dessutom
till en **extern databas** vars scheman listas via
`information_schema.schemata`.

## Filer och funktioner

| Fil | Funktion | Beskrivning |
|---|---|---|
| `schemaNamesFromDb.php` | `schemaNamesFromDb(&$dbh): array` | Listar namn på alla scheman i en databas, exkluderar `information_schema` och `pg_%`-scheman (Postgres interna scheman) |

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **✅ Bra exempel: parameteriserad SQL.** Denna fil använder
  `pg_query_params()` för INSERT-satsen istället för strängbyggd SQL –
  till skillnad från t.ex. `readDelete.php` i news-modulen. Värt att
  lyfta fram som förebild när äldre kod (som `readDelete.php`)
  refaktoreras för att åtgärda SQL injection-risker.
- **⚠️ Känslig data i klartext i databasen:** `databases`-tabellens
  `connectionstring`-kolumn innehåller sannolikt databasuppgifter
  (host, användarnamn, lösenord) i klartext, hämtade och använda direkt
  utan synlig kryptering. Detta är i sig kanske en medveten, accepterad
  designavvägning (jämför cookie-kryptering i authorization-modulen som
  visar att man är medveten om känslig data), men värt att bekräfta – är
  `databases`-tabellen extra skyddad (t.ex. bara läsbar av
  databasanvändaren appen kör som, ingen bredare åtkomst)?
- **`unset($_GET)` direkt efter att `database`-parametern lästs ut** är
  ett ovanligt men defensivt mönster (troligen för att förhindra att
  någon längre ner i koden råkar läsa fler `$_GET`-värden än avsett).
  Fungerar men är inte ett mönster vi sett i andra moduler – värt att
  notera om det är en medveten säkerhetspraxis som borde spridas till
  fler moduler, eller en enstaka försiktighetsåtgärd för just denna
  känsliga operation (extern databasanslutning).
- **`schemaNamesFromDb()` använder `die()` vid SQL-fel** istället för
  att kasta ett exception eller returnera ett felvärde. Detta avslutar
  scriptet abrupt **utan att stänga `$dbh_config`** (konfigurationsdatabas-
  anslutningen öppnad i `read_db_schemas.php`), vilket är en mindre
  resursläcka vid fel (ofarligt eftersom PHP städar upp anslutningar vid
  scriptets slut ändå, men inkonsekvent med den i övrigt noggranna
  felhanteringen i `read_db_schemas.php`, som konsekvent stänger båda
  anslutningarna vid andra felvägar).
- Ingen `strict_types` eller parametertypning.
