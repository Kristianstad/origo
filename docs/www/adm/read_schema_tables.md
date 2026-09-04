# Read schema tables-modul (read_schema_tables)

**Entry point:** `adm/read_schema_tables.php`

## Syfte
Systerfunktion till `read_db_schemas.php`, men ett steg djupare: där
`read_db_schemas.php` listar **scheman** i en extern databas, listar
denna modul **tabeller** i ett specifikt schema och registrerar dem i
konfigurationsdatabasens `tables`-tabell. Anropas troligen när en
administratör väljer ett schema i `manage.php` och systemet behöver
lista vilka tabeller som finns däri.

Inget eget svar skrivs ut (`pg_flush($dbh)` i slutet, men inget `echo`)
– ett "fire and forget"-anrop, i linje med `read_db_schemas.php`.

## Anropas med
`read_schema_tables.php?schema=<database_id>.<schema_name>`

| Parameter | Beskrivning |
|---|---|
| `schema` | Kombinerat `database_id.schema_name`, delas upp med `explode('.', ..., 2)` |

## Beror på
**Common-funktioner:**
- `dbh($connectionString)` – ansluter både till konfigurationsdatabasen
  och till den externa databasen (samma mönster som read_db_schemas)
- `all_from_table()`, `array_column_search()` – slår upp anslutningssträng
- `tableNamesFromSchema($dbh, $schema)` – listar tabellnamn i ett givet
  schema, dokumenterad i common.md

**Konstanter:** `constants/configSchema.php` → `$configSchema`

**Databas:** läser `<configSchema>.databases`, skriver till
`<configSchema>.tables` (kolumn `table_id`, format `db.schema.tabell`).

## Kända begränsningar / observationer
- ~~**⚠️ Strängbyggd SQL utan escaping vid INSERT**~~ Koden
  använder numera `pg_query_params()` med platshållare (`VALUES ($1)`)
  för `INSERT INTO {$configSchema}.tables(table_id)`, samma säkra
  mönster som `read_db_schemas.php`. Ingen SQL-injektionsrisk kvar här.
- ~~**`die()` vid SQL-fel** utan att stänga någon av databasanslutningarna~~
  Koden loggar felet (`error_log()`), stänger båda
  databasanslutningarna explicit (`pg_close($dbh)`/`pg_close($dbh_config)`)
  och avslutar med `http_response_code(500)` + `exit()` — motsvarande
  gäller även vid saknad databas (`404`) och saknad parameter (`400`).
- Doctype (`<!DOCTYPE html>`) skrivs ut överst trots att filen aldrig
  producerar något annat HTML-innehåll – sannolikt en kopieringsrest
  från en mall, ofarlig men vilseledande.
- Samma `unset($_GET)`-mönster som i `read_db_schemas.php` – konsekvent
  försiktighetsåtgärd för databasanslutningsmoduler.
