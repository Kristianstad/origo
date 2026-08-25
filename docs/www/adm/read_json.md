# Read JSON-modul (read_json) — AVSTÄNGD, LÅG PRIORITET

**Entry point:** `adm/read_json.php.off` (avaktiverad; byt filändelse
till `.php` för att återaktivera)
**Funktionsfiler:** `adm/functions/read_json/*.php`

**⚠️ STATUS: avstängd i produktion, låg underhållsprioritet.**
Filändelsen `.php.off` gör att webbservern inte kör filen. Enligt beslut
ska den inte återaktiveras utan eftertanke – den kan skriva över data
och orsaka oreda. Används i praktiken bara vid nyuppsättning av systemet
från grunden. Koden är enligt uppgift "rätt slarvig" – dokumentationen
nedan är fullständig, men observationslistan är ovanligt lång eftersom
kvalitetsnivån skiljer sig tydligt från resten av kodbasen.

## Syfte
Motsatsen till `writeConfig.php`: importerar en komplett Origo-
kartkonfiguration (JSON, t.ex. hämtad via `writeConfig.php?getJson=y`)
och skriver in den som nya poster i databasen – lager, grupper, källor,
tjänster, kontroller, sidfötter, proj4defs, tilegrids och stilar. Ett
unikt `importId` (genererat med `uniqid()`) läggs som suffix på alla
skapade id:n, för att undvika namnkrockar med befintliga poster i
databasen.

Visar först ett formulär där administratören kan kryssa i/ur vilka
datatyper som ska importeras, med en tydlig varningsdialog om riskerna
(`confirm()` vid submit). Efter lyckad import visas en länk vidare till
`manage.php` för fortsatt konfiguration.

**Bekräftad koppling till writeConfig:** koden i lager-importen
innehåller en regex som aktivt tar bort den "Administrera"-knapp som
`addLayersToJson.php` (writeConfig-modulen) bäddar in i varje lagers
`abstract`-fält vid export. Detta bekräftar att workflödet är tänkt att
kunna gå export (writeConfig) → redigera JSON externt → import
(read_json) → export igen, utan att adminknappen ackumuleras i
beskrivningstexten för varje varv.

## Anropas med
`read_json.php` (GET, visar formulär) eller POST med:

| Parameter | Beskrivning |
|---|---|
| `json` | Hela Origo-konfigurationen som JSON-text |
| `importid` | Unikt suffix för alla genererade id:n (förifyllt med `uniqid()`, kan redigeras) |
| `mapid` | Namn på den nya kartan (förifyllt `map#<importid>`) |
| `layers`, `groups`, `map`, `controls`, `footers`, `proj4defs`, `sources`, `tilegrids`, `styles`, `services` | Var och en `yes`/ej satt – kryssrutor som styr vilka datatyper som importeras |

## Beror på
**Common-funktioner:** `dbh()`, `configTables($dbh)`

**Databas:** skriver till `map_configs.layers`, `.groups`, `.maps`,
`.controls`, `.footers`, `.proj4defs`, `.tilegrids`, `.sources`,
`.services` (hårdkodat schema `map_configs`, se flaggning).

## Filer och funktioner

| Fil | Funktion | Beskrivning |
|---|---|---|
| `recursiveGroups.php` | `recursiveGroups($groupsArr): array` | Skriver rekursivt in grupphierarkin i `map_configs.groups`, kopplar samman under-/övergrupper och lager. Se detaljerad beskrivning i tidigare utkast |
| `renamedup.php` | `renamedup($name, $count=0): string` | Genererar ett unikt lagernamn vid namnkrock inom importen (`namn#1`, `namn#2`, ...) |

## Kända begränsningar / observationer

**Säkerhet:**
- **⚠️ Genomgående SQL injection-risk.** Nästan samtliga `INSERT`-satser
  i huvudfilen byggs med strängkonkatenering av värden direkt från den
  inskickade JSON-datan (`$layer['title']`, `$def['projection']`,
  `$jsonFooter['url']`, m.fl.) utan escaping. Några få fält använder
  `pg_escape_literal()` (t.ex. `abstract`, `attributes`,
  `style_config`), men detta är inkonsekvent – de flesta fält
  (`title`, `format`, `type`, `url`, kontrollnamn via `pg_escape_string`
  på ett ställe men inte konsekvent) är oskyddade. Eftersom denna modul
  är avstängd och lågprioriterad är den akuta risken låg, men **om
  modulen någonsin återaktiveras eller återanvänds som mall för ny
  kod, bör den skrivas om med `pg_query_params()` genomgående** innan
  den tas i bruk igen.
- **Ingen validering av inskickad JSON-struktur** innan den används –
  om ett förväntat fält saknas (t.ex. `$json_arr['layers']` inte är en
  array) ger koden PHP-varningar eller oväntat beteende snarare än ett
  tydligt felmeddelande till administratören.
- **Formulärets varningstext är den enda skyddsmekanismen** mot
  oavsiktlig körning – ingen bekräftelse på serversidan, ingen
  "dry run"-möjlighet att se vad som skulle importeras innan det
  faktiskt skrivs till databasen.

**Skör parsning:**
- **⚠️ Regex-parsning av rå JSON-text** (inte den avkodade
  strukturen) förekommer på flera ställen för att extrahera
  `"source"`-blocket och `"resolutions"`-listor:
```php
  preg_match('/(.*)("source": *{.*)/s', $json, $matches);
  ...
  preg_match('/"resolutions": *\[([^\]]*)\]/', $jsonPreSource, $matches);
```
  Detta är sårbart för formatförändringar i hur JSON:en är formaterad
  (extra whitespace, annan nyckelordning, nästlade objekt som råkar
  innehålla ordet "source" eller "resolutions" på oväntat sätt). All
  denna information (`source`, `resolutions`, `tileGrid`) finns redan
  tillgänglig i den avkodade `$json_arr`-strukturen (`$json_arr['source']`,
  `$json_arr['tileGridOptions']['resolutions']`, etc.) – regex-vägen
  verkar ha valts specifikt för att bevara den *ursprungliga* ordningen
  eller formateringen av upplösningslistan (troligen för att undvika
  flyttalsavrundning eller ordningsskillnader vid `json_decode`→
  `json_encode`-rundtrippen), men är ändå en betydande skörhetsrisk.
  Om modulen någonsin ska underhållas vidare är detta den mest
  akuta tekniska skulden att adressera.
- **`$result` läses efter loopar som villkorligt kan ha hoppat över att
  sätta den** (t.ex. sista raden `if ($result) { echo "Import
  lyckades!"; ... }` – om t.ex. `$_POST['map']` inte är `'yes'`, sätts
  `$result` aldrig i den sista blocket, och den använda variabeln kan
  då innehålla resultatet från en tidigare, orelaterad loop-iteration
  istället för att spegla om *hela* importen lyckades. Missvisande
  slutmeddelande möjligt.

**Övrigt:**
- **Hårdkodat schemanamn `map_configs`** genomgående (samma mönster
  som redan flaggat i `multiselect.php` och `recursiveGroups.php`),
  istället för `$configSchema`.
- **`die()` vid nästan varje SQL-fel**, utan att stänga
  databasanslutningen eller ge administratören möjlighet att se vad
  som redan hunnit importeras innan felet. En import som misslyckas
  halvvägs kan lämna databasen i ett inkonsekvent, delvis importerat
  tillstånd (t.ex. källor och tjänster skapade, men inte den
  motsvarande kartan) – inget transaktionshanterande (`BEGIN`/`COMMIT`/
  `ROLLBACK`) syns i koden.
- **`var_export($bool, true)` används för att generera SQL-litteraler**
  (`'true'`/`'false'` som text) – fungerar för Postgres boolean-kolumner
  som accepterar textrepresentation, men är en ovanlig teknik jämfört
  med att använda `pgBoolToText()` (writeConfig-modulen) eller en
  motsvarande omvänd funktion.
- **Mycket lång, monolitisk kod utan uppdelning i mindre funktioner** i
  själva `read_json.php.off` (endast två små hjälpfunktioner är
  utbrutna: `recursiveGroups`, `renamedup` – resten, inklusive hela
  lager-importlogiken med dess djupa villkorsträd för stilar/ikoner,
  ligger direkt i entry point-filen). Om modulen någonsin blir aktuell
  igen vore en uppdelning liknande `writeConfig`/`addLayersToJson`-
  mönstret (en fil per logisk del: `importSources`, `importLayers`,
  `importGroups`, `importMap`, etc.) den mest värdefulla första
  refaktoreringsinsatsen.
- **Komplex, delvis duplicerad villkorslogik för stilar/ikoner** i
  lager-importen (fyra kapslade if/else-grenar som avgör
  `$layerStyleConfig`/`$layerIcon`/`$layerExtendedIcon`/`$layerStyleFilter`)
  är i praktiken den omvända operationen av motsvarande logik i
  `addLayersToJson.php` (writeConfig) – de två borde spegla varandra
  exakt (vad som skrivs ut vid export måste kunna läsas tillbaka vid
  import), men eftersom det är fristående, separat skriven kod finns
  risk att de tystnat glidit isär över tid. Om read_json någonsin
  återaktiveras rekommenderas en explicit jämförelse mot
  `addLayersToJson.php`s logik för att säkerställa att rundtrippen
  fortfarande fungerar korrekt.
- Ingen `strict_types` eller parametertypning.

## Sammanfattande rekommendation
Eftersom modulen är avstängd, sällan behövd, och innehåller flera lager
av teknisk skuld (SQL injection-risk, skör regex-parsning, avsaknad av
transaktionshantering), **rekommenderas den inte som utgångspunkt för
vidareutveckling i sitt nuvarande skick**. Om ett behov av
JSON-import uppstår igen i framtiden är en fullständig omskrivning
(snarare än stegvis lagning) sannolikt mer kostnadseffektiv än att
försöka härda den befintliga koden bit för bit, givet hur tätt
sammanflätade problemen är.
