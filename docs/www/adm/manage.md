# Manage-modul (manage)

**Entry point:** `adm/manage.php` (32K, den enskilt största och mest
centrala filen i systemet)
**Funktionsfiler:** `adm/functions/manage/*.php` (60+ filer, **ännu
inte granskade** – dokumenteras i en separat genomgång)
**JS-filer:** `adm/js-functions/manage/*.js` (ej granskade ännu)
**Stilmall:** `adm/styles/manage.css`

**⚠️ STATUS: endast entry point-filen dokumenterad hittills.** Detta är
den viktigaste modulen i hela systemet – all administration av kartor,
lager, källor, grupper m.m. går genom denna fil. Fullständig
dokumentation kräver genomgång av samtliga filer i
`functions/manage/`, vilket görs i efterföljande omgångar.

## Syfte
Den centrala administrationssidan: en generisk CRUD-motor (skapa, läsa,
uppdatera, radera) för alla konfigurationsobjekt i systemet – kartor,
grupper, lager, källor, tjänster, databaser, scheman, tabeller,
kontroller, plugins, sökmodeller/-tabeller, m.fl. Istället för en separat
sida per objekttyp, hanterar en enda, parametriserad kodväg samtliga
typer, genom att typen (`$type`) och vilken tabell den motsvarar
(`$typeTableName`) räknas ut dynamiskt utifrån vilken knapp som
klickades och vilket fält som postades.

Presenterar en kaskaderande vy: välj en karta → se dess grupper/lager/
kontroller → välj en grupp → se dess undergrupper/lager → välj ett
lager → se och redigera lagrets fullständiga formulär. Motsvarande
kaskad finns för databas → schema → tabell.

Skriver ändringar direkt till konfigurationsdatabasen, och markerar
(via `markMapsChanged()`) vilka publicerade kartor som blivit
inaktuella och behöver köras genom `writeConfig.php` igen för att
ändringen ska synas för besökare.

## Anropas med
`manage.php?view=<vy>` (GET för vy) + POST med formulärdata

| Parameter | Beskrivning |
|---|---|
| `view` (GET) | Styr vilken uppsättning kolumner/urvalsformulär som visas överst (t.ex. "Origo"-vyn för kartkonfiguration kontra en annan vy för databaskonfiguration – exakt vilka vyer som finns kräver `viewKeywordCategorized`/`views.php`-konstanten) |
| `<typ>Id` | Väljer ett specifikt objekt av given typ (t.ex. `mapId`, `layerId`, `groupId`) |
| `groupId` / `groupIds` | Håller reda på hela kedjan av nästlade grupper från rot till vald undergrupp |
| `<typ>IdNew` | Nytt id vid skapande av ett objekt |
| `<typ>IdDel` | Id att radera |
| `update<Fält>` | Ett formulärfälts nya värde vid uppdatering (t.ex. `updateTitle`, `updateAbstract`) |
| `<knapp med kommando>` | Formulärknappens `name`/`value` avslöjar `$type` och `$command` (`copy`/`create`/`delete`/`update`/`operation`), tolkas av `postButton()` |
| `to<Typ>Id` / `from<Typ>Id` | Vid `operation`-kommandot: lägg till/ta bort ett barn-objekt från en förälder (map/group) |

## Beror på (bekräftat hittills)
**Common-funktioner** (`adm/functions/common/`):
- `dbh()`, `configTables($dbh)`
- `pkColumnOfTable()`, `array_column_search()`, `pgArrayToPhp()`,
  `assoc_array_values()`, `findAllParents()`, `tablesFromQgsXml()`

**Manage-funktioner** (namn bekräftade via användning, fullständig
beskrivning väntar):
`idPosts`, `sizePosts`, `categoryPosts`, `focusTable`, `viewKeywordCategorized`,
`categories`, `postButton`, `typeTableName`, `isIdUniqueInTable`,
`insertIdSql`, `deleteIdSql`, `updatePosts`, `validateUpdate`,
`array_column_search` (delad), `makeFullTarget`, `sqlForUpdate`,
`makeBasicTarget`, `sqlForOperation`, `usedInMaps`, `markMapsChanged`,
`printViewSwitcher`, `printHeadForms`, `typeHelps`, `printMapForm`,
`printChildSelect`, `printDatabaseForm`, `printSchemaForm`,
`printGroupForm`, `makeTargetFull`, `targetType`, `targetConfigParam`,
`setTargetConfigParam`, `printLayerForm`, `printSourceForm`,
`printTableForm`, `printSearchtableForm`, samt (via `eval`)
`print<Typ>Form` för varje övrig typ (t.ex. `printControlForm`,
`printPluginForm`, m.fl.)

**Filsystem:** läser QGIS-projektfiler (`.qgs`) direkt från disk vid
uppdatering av layer/source, samma mönster som i `info.php` och
`writeTablesForAllLayers.php`.

**Databas:** läser och skriver till praktiskt taget samtliga
konfigurationstabeller (via `configTables()` och dynamiskt genererad SQL).

## Kända begränsningar / observationer (preliminära, baserat på entry point)

- **⚠️ Genomgående användning av `eval()`** för dynamiska
  funktionsanrop, på minst fyra ställen:
```php
  eval("\${$table}Categories=categories(\$config, \$catParam);");
  eval("\$categories=\${$categorized}Categories;");
  eval("\${$typeTableName}Categories=categories(\$configTables[\$typeTableName], \$typeTablePkColumn);");
  eval('print' . ucfirst($childType) . 'Form($childFullTarget, ...);');
```
  Detta är ett vanligt men riskabelt mönster: om `$childType` (eller
  `$table`/`$typeTableName`) någonsin kan påverkas av extern input utan
  fullständig validering mot en känd lista av giltiga typer, öppnar det
  för kodinjektion. I det vi sett hittills verkar `$childType` härledas
  från `targetType()` baserat på databasstruktur (inte direkt från
  `$_POST`), vilket sannolikt begränsar risken – men detta **bör
  verifieras** när `targetType.php` och `typeTableName.php` granskas.
  Oavsett säkerhetsrisk är `eval()` här även en **läsbarhets- och
  verktygsstödsutmaning**: varken IDE:er, statisk analys (PHPStan) eller
  "hitta användningar av funktion X" fungerar över en `eval()`-gräns,
  vilket gör det svårare för en ny utvecklare (eller AI) att förstå
  vilka `print*Form`-funktioner som faktiskt anropas utan att läsa hela
  filen. **Detta är den högst prioriterade kandidaten för förenkling i
  hela manage-modulen** – ett explicit `match`/`switch`-uttryck eller en
  uppslagstabell (`$formRenderers = ['layer' => 'printLayerForm', ...]`)
  skulle ge exakt samma funktionalitet utan `eval()`, och samtidigt göra
  koden sökbar och verktygsstödd.
- **Extremt hög cyklomatisk komplexitet i en enda fil.** 705 rader med
  djupt nästlade villkor, och minst fem distinkta "typer av objekt som
  kan vara valda" (map/database/schema/group-kedja/övrigt) hanteras i
  sekvens i samma fil, med tydlig `unset()`-städning mellan varje sektion
  för att undvika att variabler läcker mellan grenarna. Det här mönstret
  (en enda lång fil med väldokumenterade kommentarer som beskriver varje
  steg) är faktiskt **ovanligt väl kommenterat** jämfört med resten av
  kodbasen – varje sektion har en förklarande kommentar om vad den gör
  och varför. Det gör filen begriplig trots sin storlek, men den skulle
  sannolikt vinna på att brytas upp i namngivna funktioner (en per FAS
  ovan) även om inga rader ändras i sak – ren extraktion utan
  beteendeändring, vilket är en lågriskrefaktorering.
- **`unset($_POST, $_GET)` överst** – samma försiktighetsmönster vi sett
  i read_db_schemas/read_schema_tables, konsekvent tillämpat.
- **`array_filter($_POST, ...)` behåller `"0"` som värde men filtrerar
  bort tomma strängar** – ett medvetet, korrekt hanterat specialfall
  (PHP:s `empty("0")` är sant, vilket annars skulle förlora legitima
  "0"-värden i formulär, t.ex. en opacitet eller skala satt till 0).
  Bra exempel på uppmärksamhet mot en klassisk PHP-fallgrop.
- **Delete-skyddet (`findAllParents` + `assoc_array_values`) återanvänder
  exakt samma mönster som `info.php`s "Används av"-funktion** – bra
  konsekvens, och bekräftar att `findAllParents`/`assoc_array_values` är
  kärnfunktioner värda extra uppmärksamhet vid eventuell framtida
  ändring, eftersom de skyddar mot dataförlust på minst två ställen.
- **QGIS-autoifyllnadslogiken vid update (rad ~150–170 i del 1) dupliceras
  konceptuellt med `writeTablesForAllLayers.php` och delar av `info.php`**
  (alla tre läser `.qgs`-filer för att extrahera information). Om denna
  logik någonsin behöver ändras (t.ex. ny QGIS-version med annat
  XML-format) måste tre olika ställen uppdateras. Kandidat för att
  bryta ut till en delad common-funktion, t.ex. `qgisProjectMetadata($service,
  $sourceId)`, vid framtida förenkling.
- **Global användning av variabler som `$viewDepthGlobal`,
  `$formChangedGlobal`** (namngivna med `Global`-suffix, till skillnad
  från writeConfig-modulens råa `GLOBAL`-nyckelord utan
  namnkonvention) – en medveten, mer läsbar konvention för globala
  variabler jämfört med writeConfig. Värt att notera som en god
  praxis-skillnad mellan de två stora modulerna.
- **Inkonsekvent felhantering vid databasfel:** vid `pg_query()`-fel
  byggs ett JS `alert()` med rått `pg_last_error()`-innehåll
  (escapat för JS-strängen, men inte HTML-escapat) som visas direkt för
  administratören. Detta exponerar interna databasfelmeddelanden
  (kan innehålla tabell-/kolumnnamn, SQL-fragment) till den inloggade
  administratören – rimligt för en intern adminpanel med betrodda
  användare, men värt att notera som en skillnad mot vad man skulle
  acceptera i en publik felhantering.
- Ingen `strict_types` eller parametertypning (gäller hela filen,
  konsekvent med övriga äldre delar av kodbasen).
