# Write tables for all layers-modul (writeTablesForAllLayers)

**Entry point:** `adm/writeTablesForAllLayers.php`

## Syfte
Ett **underhålls-/batchscript** snarare än en vanlig sida: går igenom
samtliga QGIS-baserade lager i systemet som saknar en ifylld
`tables`-kolumn, läser motsvarande QGIS-projektfil (`.qgs`) från disk,
och fyller i vilka databastabeller lagret använder (via
`tablesFromQgsXml()`). Troligen körs detta manuellt eller periodiskt
efter att nya lager lagts till, för att slippa manuellt ange vilka
tabeller varje QGIS-lager bygger på – informationen härleds istället
direkt ur QGIS-projektfilen.

Ingen synlig koppling till `functions/manage/`, men resultatet
(`layers.tables`) används sannolikt av manage-modulen (t.ex. vid
visning av "vilka tabeller påverkas av detta lager", jämför `info.php`s
"Används av"-funktion).

## Anropas med
`writeTablesForAllLayers.php` (inga parametrar) – körs för samtliga
lager i systemet i ett svep.

**Svar:** inget synligt HTML-innehåll (endast `<!DOCTYPE html>` skrivs
ut, sedan tyst bearbetning).

## Beror på
**Common-funktioner:**
- `dbh()`, `all_from_table()`, `array_column_search()`, `pkColumnOfTable()`
- `tablesFromQgsXml($qgsXml, $layerName)` – **ny, ej tidigare
  dokumenterad common-funktion**, tolkar en QGIS-projektfils XML för att
  hitta vilka databastabeller ett givet lager bygger på

**Konstanter:** `constants/configSchema.php` → `$configSchema`

**Databas:** läser `layers`, `sources`, `services`; skriver
`layers.tables` (Postgres-array).

**Filsystem:** läser QGIS-projektfiler direkt från disk,
`/services/<service>/<sourceName>.qgs` – samma mönster som i
`info.php` för `source`-typer.

## Kända begränsningar / observationer
- **⚠️ Strängbyggd SQL vid UPDATE:** `$layerId` klistras in direkt i
  SQL-strängen. `$layerId` kommer från databasen (inte direkt
  användarinput i denna körning), så risken är låg i praktiken, men
  avviker från `pg_query_params()`-mönstret i nyare kod.
- **`die()` vid SQL-fel**, konsekvent med tidigare observerade
  underhållsscript.
- Script utan parametrar som körs över **alla** lager i systemet –
  potentiellt tungt/långsamt om systemet har många lager. Ingen
  batchning eller framstegsindikator syns. Värt att känna till om
  scriptet någonsin timear ut vid körning via webbserver (ingen
  `set_time_limit()` justering syns).
- Namnmönstret `writeTablesForAllLayers.php` (ingen `functions/`-mapp,
  logik direkt i entry point-filen) skiljer sig från övriga moduler –
  rimligt för ett litet, sällan använt underhållsscript.
