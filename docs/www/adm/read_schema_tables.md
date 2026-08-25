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
- `tableNamesFromSchema($dbh, $schema)` – **ny, ej tidigare dokumenterad
  common-funktion**, listar tabellnamn i ett givet schema

**Konstanter:** `constants/configSchema.php` → `$configSchema`

**Databas:** läser `<configSchema>.databases`, skriver till
`<configSchema>.tables` (kolumn `table_id`, format `db.schema.tabell`).

## Kända begränsningar / observationer
- **⚠️ Strängbyggd SQL utan escaping vid INSERT:**
  `"INSERT INTO $configSchema.tables(table_id) VALUES ('$database.$schema.$tableName') ..."`
  – till skillnad från sin systermodul `read_db_schemas.php`, som
  använder `pg_query_params()`. Här klistras `$database` (från
  `$_GET['schema']`) och `$tableName` (från databasen, troligen säkert)
  direkt in i strängen. `$database` kommer indirekt från användarinput
  och bör escapas eller parameteriseras – samma åtgärd som redan gjorts
  i `read_db_schemas.php` bör spegla hit.
- **`die()` vid SQL-fel** utan att stänga någon av databasanslutningarna
  – samma mönster som `schemaNamesFromDb()`, se den notisen i
  `read_db_schemas.md`.
- Doctype (`<!DOCTYPE html>`) skrivs ut överst trots att filen aldrig
  producerar något annat HTML-innehåll – sannolikt en kopieringsrest
  från en mall, ofarlig men vilseledande.
- Samma `unset($_GET)`-mönster som i `read_db_schemas.php` – konsekvent
  försiktighetsåtgärd för databasanslutningsmoduler.
