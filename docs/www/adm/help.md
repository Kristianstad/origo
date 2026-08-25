# Hjälp-modul (help)

**Entry point:** `adm/help.php`
**Stilmall:** `adm/styles/help.css`

## Syfte
Visar en hjälptext i en popup/iframe (samma `postMessage`-mönster som
info- och multiselect-modulerna). Utan parameter visas en generisk lista
med länkar till Origo-dokumentation och en JSON-validerare. Med en
`id`-parameter visas istället en specifik hjälptext hämtad från databasen
(troligen kontextuell hjälp kopplad till ett visst formulärfält i
`manage.php`).

## Anropas med
`help.php` eller `help.php?id=<help_id>`

| Parameter | Beskrivning |
|---|---|
| `id` | (valfri) Id för en specifik hjälptext i `helps`-tabellen. Om utelämnad visas generella hjälplänkar |

## Beror på
**Common-funktioner:** `dbh()`, `all_from_table()`, `array_column_search()`
(endast när `id` anges – annars inkluderas inte ens `functions/common/`)

**Konstanter:** `constants/configSchema.php` → `$configSchema`

**Databas:** `<configSchema>.helps` (kolumner inkl. `help_id`, `abstract`)

**Filsystem:** länkar till (men läser inte) `../Origo_admin_tutorial_swedish.pdf`
på toppnivå.

## Kända begränsningar / observationer
- **`$help['abstract']` skrivs ut utan `htmlspecialchars()`** – troligen
  avsiktligt eftersom hjälptexter förväntas innehålla HTML-formatering
  (skriven av administratörer), men värt att notera som samma mönster
  som `news.php`s XSS-observation.
- Inkluderar `functions/common` villkorat inuti `if (isset($_GET['id']))`
  – ett ovanligt men logiskt mönster (undviker databasanslutning helt
  när den inte behövs).
