# Updated-modul (updated)

**Entry point:** `adm/updated.php`
**Funktionsfiler:** `adm/functions/updated/*.php`
**Stilmall:** `adm/styles/updated.css`

## Syfte
Tar reda på **när en eller flera databastabeller senast ändrades**,
genom att fråga Postgres commit-tidsstämpel (`pg_xact_commit_timestamp`)
för varje tabells senaste rad. Returnerar det senaste datumet (endast
datumdel, `YYYY-MM-DD`) bland de angivna tabellerna. Används troligen av
Origo-kartan eller adminpanelen för att visa "senast uppdaterad"-datum
för en karta eller ett lager.

**Förutsättning:** kräver att `track_commit_timestamp` är påslaget i
Postgres-konfigurationen, annars returnerar
`pg_xact_commit_timestamp()` alltid NULL.

## Anropas med
`updated.php?table=<schema.tabell1>,<schema.tabell2>,...`

| Parameter | Beskrivning |
|---|---|
| `table` | Kommaseparerad lista med fullt kvalificerade tabellnamn (`schema.tabell`) att kolla senaste ändring för |

**Svar:** ett enkelt textdatum, t.ex. `2025-06-12` (de första 10 tecknen
av tidsstämpeln för den senast ändrade tabellen).

## Beror på
**Common-funktioner:** `dbh($connectionString)`

**Konstanter:** `constants/dbhConnectionStringForUpdated.php` →
`$dbhConnectionStringForUpdated` – **egen, dedikerad anslutningssträng**
för denna modul, skild från standardanslutningen. Möjlig anledning:
kanske en läsreplika, eller en anslutning med särskilda rättigheter för
`pg_xact_commit_timestamp()`. Värt att bekräfta.

## Filer och funktioner

| Fil | Funktion | Beskrivning |
|---|---|---|
| `updated_from_table2.php` | `updated_from_table2($dbh, $tableWithSchema): array\|null` | Kör `pg_xact_commit_timestamp`-frågan för en tabell, returnerar `[tidsstämpel, xmin]` för senast ändrade rad |

## Kända begränsningar / observationer
- **⚠️ SQL injection-risk:** `$tableWithSchema` klistras in direkt i SQL
  utan escaping (`"SELECT ... FROM $tableWithSchema ..."`), och kommer
  från `$_GET['table']` (kommaseparerad, ej validerad mot en whitelist av
  tillåtna tabellnamn). Eftersom detta är ett tabellnamn (inte ett värde)
  kan det inte parameteriseras med `pg_query_params()` på vanligt sätt –
  rekommenderad åtgärd är att validera mot en känd lista tillåtna
  scheman/tabeller, eller använda `pg_escape_identifier()` per
  del (schema och tabellnamn separat).
- **Funktionsnamnet `updated_from_table2`** antyder att det finns/fanns
  en `updated_from_table.php` (utan `2`) – vi har inte sett den filen
  men den kan finnas i `functions/common/` eller vara borttagen.
  *(Not: en `updated_from_table.php` finns faktiskt listad i
  `functions/common/` i filträdet – värt att jämföra de två när vi ser
  den filen, för att förstå varför två varianter finns.)*
- **`die()` vid SQL-fel**, samma mönster som tidigare noterat i andra
  moduler.
