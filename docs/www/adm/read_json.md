# Read JSON-modul (read_json) — AVSTÄNGD

**Entry point:** `adm/read_json.php.off` (avaktiverad, byt filändelse
till `.php` för att återaktivera)
**Funktionsfiler:** `adm/functions/read_json/*.php`

**⚠️ STATUS: avstängd i produktion.** Filändelsen `.php.off` gör att
webbservern inte kör filen som PHP. Enligt beslut ska den **inte**
återaktiveras utan eftertanke – den kan skriva över data och orsaka
oreda. Används idag i praktiken bara vid nyuppsättning av systemet
("scratch"-installation). Full dokumentation av huvudfilen
(`read_json.php.off`) är uppskjuten tills den eventuellt behövs; nedan
dokumenteras bara de två hjälpfunktioner vi sett, utifrån vad de avslöjar
om modulens syfte.

## Vad vi kan sluta oss till om syftet
Baserat på funktionsnamnen och innehållet: modulen tar sannolikt in en
JSON-fil (troligen en Origo-kartkonfiguration att importera) och skriver
in dess grupper och lager i databasen (`map_configs.groups`), inklusive
hantering av namnkonflikter mellan lager (`renamedup`) och rekursiv
uppbyggnad av grupphierarkier (`recursiveGroups`). Detta antyder en
**import-funktion**: att ta en exporterad/extern Origo-kartkonfiguration
och lägga in den i systemets egen databas – motsatsen till
`writeConfig`-modulen (som troligen genererar Origo-konfiguration
*från* databasen).

## Filer och funktioner (hittills sedda)

| Fil | Funktion | Beskrivning |
|---|---|---|
| `recursiveGroups.php` | `recursiveGroups($groupsArr): array` | Går rekursivt igenom en trädstruktur av grupper (från importerad JSON), infogar varje grupp i `map_configs.groups` med en genererad unik id (`namn#$importId`), och länkar samman under-/övergrupper samt lager. Använder `GLOBAL $dbh, $groups, $importId, $groupsLayers` |
| `renamedup.php` | `renamedup($name, $count=0): string` | Genererar ett unikt lagernamn vid namnkrock, genom att rekursivt lägga till en räknare (`namn#1`, `namn#2`, ...) tills ett ledigt namn hittas. Använder `GLOBAL $uniqueLayers` |

## Kända begränsningar / observationer (preliminära, baserat på delvis underlag)
- **Hårdkodat schemanamn `map_configs`** i `recursiveGroups.php`, samma
  mönster (och samma flaggning) som i `multiselect.php` – inte
  `$configSchema` från konstanterna.
- **Tung användning av `GLOBAL`** i båda funktionerna – gör dem svåra
  att förstå och testa isolerat utan att se hela `read_json.php.off`.
  Kandidat för att skriva om med explicita parametrar/returvärden vid
  framtida arbete, **om och när** modulen någonsin blir aktuell att
  underhålla igen.
- Eftersom modulen är avstängd och sällan behövd, **rekommenderas låg
  prioritet** för vidare dokumentation/refaktorering av den – markerat
  tydligt här så att framtida utvecklare (mänsklig eller AI) inte
  råkar lägga tid på den utan anledning, men ändå hittar den om behovet
  uppstår.
