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

## Datastrukturen "target"

Ett återkommande begrepp genom hela manage-modulen. Ett **target** är en
liten, enhetlig datastruktur som representerar "ett objekt av en viss
typ", i två varianter:

- **Basic target:** `[$type => $id]`, t.ex. `['layer' => 'vagar#1']`
  – bara typen och dess id, inget annat. Skapas av `makeBasicTarget()`.
- **Full target:** `[$type => $config]`, t.ex.
  `['layer' => ['layer_id' => 'vagar#1', 'title' => 'Vägar', ...]]`
  – typen och hela dess konfigurationsrad från databasen. Skapas av
  `makeFullTarget()` (om du redan har `$config`) eller `makeTargetFull()`
  (om du bara har ett basic target och en databaskoppling/configTables,
  och behöver slå upp konfigurationen åt dig).

Skillnaden mellan en "full" och "basic" target känns igen på om värdet
är en array (`is_array(current($target))`) – vilket är precis vad
`isFullTarget()` kontrollerar.

Detta enhetliga format gör att funktioner som `sqlForUpdate()`,
`sqlForOperation()`, `printChildSelect()` m.fl. kan ta emot "vilket
objekt som helst" utan att bry sig om exakt vilken av de många typerna
(map/layer/group/source/...) det handlar om – de läser bara ut typen
via `array_key_first()`/`key()`-mönster och agerar generiskt. Detta är
kärnan i hur manage-modulen kan hantera alla entitetstyper med samma
kodväg istället för att skriva om samma logik för varje typ.

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

## Filer och funktioner (target-hantering och POST-tolkning)

| Fil | Funktion | Beskrivning |
|---|---|---|
| `appendUpdatedColumnsToSql.php` | `appendUpdatedColumnsToSql($dbColumns, $sql): string` | Bygger vidare på en befintlig SQL-sträng med `kolumn = värde`-par (kommaseparerade), för användning i en `UPDATE`-sats. Tomma värden (inkl. `'{}'`/`'{{}}'`, tomma Postgres-arrayer) blir `NULL`; övriga escapas med `pg_escape_literal()` |
| `categories.php` | `categories($config, $catParam): array` | Bygger en nyckelordskategorisering: går igenom en hel tabellkonfiguration och grupperar rad-id:n (`$catParam`, primärnyckelkolumnen) efter deras `keywords`-fält. Lägger alltid till en `"Alla"`-kategori med samtliga id:n överst |
| `categoryPosts.php` | `categoryPosts($post): array` | Filtrerar `$post` till fält vars namn slutar på `Category` |
| `deleteIdSql.php` | `deleteIdSql($id, $tableName): string` | Bygger en `DELETE`-sats för given tabell och id |
| `focusTable.php` | `focusTable($idPosts): string\|null` | Avgör vilken tabell som är "i fokus" utifrån vilka `*Id`-fält som postats – prioriterar map/database/schema/group före övriga typer, annars härleds tabellen från det första postade id-fältets namn |
| `hasStringKeys.php` | `hasStringKeys(array $array): bool` | Kontrollerar om en array har minst en textnyckel (dvs. är associativ snarare än numeriskt indexerad). **Ingen användning observerad ännu** i det vi granskat – flaggad nedan |
| `idPosts.php` | `idPosts($post): array` | Filtrerar `$post` till fält vars namn slutar på `Id`, med explicit undantag för `fromMapId`/`toMapId`/`fromGroupId`/`toGroupId` (dessa hanteras separat vid `operation`-kommandot, se manage.php) |
| `isArrayColumn.php` | `isArrayColumn($column): bool` | Kontrollerar om en given kolumn är en Postgres-array-kolumn, genom att slå upp den mot listan i `constants/arrayColumns.php`. Avslutar programmet (`die()`) om `$column` inte är en icke-tom sträng |
| `isFullTarget.php` | `isFullTarget($target): bool` | Avgör om en target är "full" (innehåller hela konfigurationen) snarare än "basic" (bara ett id) – se förklaring av target-konceptet ovan |
| `makeBasicTarget.php` | `makeBasicTarget($type, $id): array` | Skapar en basic target `[$type => $id]`. Avslutar programmet vid ogiltiga argument |
| `makeFullTarget.php` | `makeFullTarget($type, $config): array` | Skapar en full target `[$type => $config]` direkt från en redan känd konfigurationsrad. Avslutar programmet vid ogiltiga argument |
| `makeTargetFull.php` | `makeTargetFull($target, $configTablesOrDbh): array` | Tar en basic (eller full) target och returnerar en full target, genom att slå upp konfigurationen via `targetConfig()` (ej granskad ännu) om den saknas. Avslutar programmet om indata inte är en giltig target |
| `markMapsChanged.php` | `markMapsChanged(&$dbh, $mapIds): void` | Sätter `maps.changed = 't'` för samtliga angivna kartor i en enda batch-SQL (flera `UPDATE`-satser konkatenerade med `; `). **Bekräftar tidigare hypotes:** detta är motparten till `markMapUnchanged()` i writeConfig-modulen – manage-modulen flaggar en karta som "ändrad, behöver publiceras om" varje gång något som påverkar den redigeras, och writeConfig-modulen nollställer flaggan efter lyckad publicering |
| `postButton.php` | `postButton($post): string\|null` | Hittar namnet på den POST-parameter vars namn slutar på `Button` – det är detta namn (`<typ>Button`) som `manage.php` sedan bryter isär för att få fram `$type` |

**Filsystem:** läser QGIS-projektfiler (`.qgs`) direkt från disk vid
uppdatering av layer/source, samma mönster som i `info.php` och
`writeTablesForAllLayers.php`.

**Databas:** läser och skriver till praktiskt taget samtliga
konfigurationstabeller (via `configTables()` och dynamiskt genererad SQL).

## JS-filer och funktioner

**Plats:** `adm/js-functions/manage/`
**Laddas:** inline i `<script>`-taggen i `<head>`, via samma
`includeDirectory()`-mönster som används för PHP (se ARKITEKTUR.md)

| Fil | Funktion | Beskrivning |
|---|---|---|
| `toggleTopFrame.js` | `toggleTopFrame(type)` | Visar/döljer den delade toppmonterade iframen (`#topFrame`). Håller reda på vilken typ av innehåll som visas (global variabel `topFrame`, deklarerad i `manage.php`s inline-script) – klick på samma typ igen döljer den, klick på en annan typ byter innehåll och scrollar upp |
| `resizeIframe.js` | `resizeIframe(iframe)` | Anpassar en iframes höjd efter dess faktiska innehåll, genom att tillfälligt sätta höjden till `1px` och sedan mäta `scrollHeight` i nästa animationsframe |
| `initMessageListener.js` | `initMessageListener()` | Sätter upp en global `postMessage`-lyssnare för hela sidan. Validerar avsändarens origin (måste matcha `window.location.origin`) och att datan har rätt form innan den hanteras. Hanterar tre fall: `action:'close'` utan `targetId` (stäng topFrame, t.ex. från help.php), `action:'resize'` (justera topFrame-höjd), och `targetId`+`value` (fyll i ett textarea-fält med värde från multiselect-verktyget, samt uppdatera den tillhörande multiselect-knappens `value`-attribut så nästa öppning av multiselect visar rätt förval) |
| `formChangeButton.js` | `formChangeButton()` | Lägger till en CSS-klass (`change`) på ett formulärs "Uppdatera"-knapp så fort något fält i formuläret ändras (utom dolda fält), för att visuellt signalera osparade ändringar |
| `preservePageScroll.js` | `preservePageScroll()` | Sparar scrollpositionen i `sessionStorage` vid formulärinskick, och återställer den efter att sidan laddats om – kompenserar för att `manage.php` är en traditionell serverrenderad sida där varje åtgärd (spara, välja ett nytt objekt) innebär en full sidomladdning |
| `updateSelect.js` | `updateSelect(id, array)` | Fyller om en `<select>`-listas alternativ med ett nytt innehåll. Specialhantering för element vars id slutar på `Categories`: värdet sätts till det råa (understreck-separerade) kategorinamnet men visningstexten har understreck ersatta med mellanslag |

## Kommunikationsmönster: iframe ↔ huvudsida

`manage.php` bygger på ett återkommande mönster där verktyg som körs i
en iframe (`help.php`, `multiselect.php`, och potentiellt andra) pratar
med huvudsidan via `postMessage`:

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
- **⚠️ `initMessageListener.js` saknar null-kontroll på
  `multiselectButton`:**
```js
  const multiselectButton = document.getElementById(targetId + ":multiselect");
  let multiselectButtonValue = multiselectButton.getAttribute('value');
```
  Om inget element med id `<targetId>:multiselect` finns i DOM:en (t.ex.
  om ett fält kan fyllas via multiselect-verktyget utan att ha en
  tillhörande multiselect-knapp, eller om knappens id-konvention någon
  gång avviker), kastar detta ett `TypeError: Cannot read properties of
  null` och stoppar resten av händelsehanteraren. Värt att lägga till
  samma typ av null-kontroll som redan finns för `textarea` några rader
  ovanför.
- **Skört strängmönster för att uppdatera multiselect-knappens
  `value`-attribut:**
```js
  multiselectButtonValue.replace(/^([^:]*::[^:]*).*$/, '$1:' + value);
```
  Denna regex förutsätter exakt samma `<textareaId>::<tabell>:<värden>`-
  format som vi dokumenterade i `multiselect.md` (se
  `multiselect.php`s query-parameterparsning). De två platserna – här
  och i `multiselect.php` – måste hållas i synk manuellt; om formatet
  någonsin ändras på ena stället måste det ändras på båda. Ytterligare
  ett skäl (utöver läsbarhetsargumentet vi redan noterat i
  `multiselect.md`) att överväga att ersätta den hopkodade strängen med
  separata, tydligt namngivna data-attribut.
- **Två funktioner med samma namn (`updateSelect`) i olika moduler:**
  denna fils `updateSelect(id, array)` (manage) skiljer sig i
  **parameterordning och beteende** från `update(menu)` i
  multiselect-modulen (som vi dokumenterade tidigare som `update.js` –
  notera att den filen faktiskt exporterar en funktion vid namn
  `update`, inte `updateSelect`, så namnkonflikten är mindre akut än
  den såg ut vid första anblick, men värt att dubbelkolla att inga
  andra js-mappar har en `updateSelect`-funktion med annan signatur,
  eftersom alla js-filer i en mapp laddas globalt utan namnrymder).
- **`formChangeButton()` letar bara efter en knapp med exakt
  `value="update"`** – om ett formulär har flera submit-knappar (t.ex.
  separata knappar för "spara" och "kopiera" som vi sett i
  `manage.php`s `$command`-hantering: `copy`/`create`/`delete`/`update`/
  `operation`), får bara `update`-knappen den visuella
  ändrings-markeringen. Rimligt om det är den enda knappen som ska
  visa "osparade ändringar", men värt att bekräfta att det är avsiktligt
  och inte ett förbiseende för de andra kommandona.
- **Konsekvent, modern JS-stil** (`const`/`let`, arrow functions,
  destrukturering, `querySelectorAll`/`forEach`) genomgående i samtliga
  sex filer – till skillnad från flera äldre PHP-delar av kodbasen.
  Bekräftar att JS-lagret överlag är nyare/mer omsorgsfullt underhållet
  än en del av den äldre PHP-koden (t.ex. news/authorization).
- Ingen av filerna har enhetstester eller motsvarande, men koden är
  tillräckligt enkel och fri från globala sidoeffekter (förutom delade
  DOM-element och den globala `topFrame`-variabeln) att den skulle vara
  relativt lätt att testa isolerat om det blir aktuellt.
- **⚠️ SQL injection-risk i `deleteIdSql.php`:** `$id` klistras in direkt
  i SQL-strängen utan escaping (`"... WHERE $tablePkColumn = '".$id."'"`).
  `$id` kommer ytterst från `$post[$type . 'IdDel']` i `manage.php` –
  alltså direkt användarinput (om än från en inloggad administratör).
  Samma mönster som redan flaggats på flera andra ställen i kodbasen;
  eftersom detta är en av de mest centrala och känsliga operationerna
  (radering) i hela adminverktyget, är den här filen en god kandidat att
  prioritera vid en eventuell säkerhetsstädning, tillsammans med
  `markMapsChanged.php` (som har samma mönster för `$mapId`, om än
  `$mapId` här kommer från redan validerad `usedInMaps()`-data snarare
  än direkt användarinput, vilket sannolikt gör risken lägre där).
- **Flera funktioner avslutar hela programmet med `die()` vid ogiltiga
  argument** (`makeBasicTarget`, `makeFullTarget`, `makeTargetFull`,
  `isArrayColumn`). Detta är ett medvetet "fail fast"-mönster för
  interna programmeringsfel (fel typ av argument skickat av misstag),
  snarare än för förväntade felsituationer med användarinput – rimligt
  för hjälpfunktioner som bara anropas internt med redan kontrollerad
  data, men det gör dem svåra att återanvända i sammanhang där ett
  ogiltigt anrop bör hanteras mjukare (t.ex. return `false`/kasta ett
  exception som kan fångas). Genomgående mönster värt att känna till
  innan man refaktorerar kring dessa funktioner.
- **`hasStringKeys.php` har ingen synlig användning** i det material vi
  granskat hittills. Kan vara använd längre fram i `functions/manage/`
  (vi har bara sett en bråkdel av filerna), eller vara kvarlämnad
  död kod. Flaggas för uppföljning när fler filer granskats.
- **`isArrayColumn()` läser sin konstant med ett funktionslokalt
  `require()`** (`require("./constants/arrayColumns.php");` inuti
  funktionskroppen) snarare än att konstanten skickas in som parameter
  eller läses en gång centralt. Fungerar (PHP cachar inte `require` per
  session, men körs bara en gång per anrop av funktionen så
  prestandapåverkan är minimal), men avviker från mönstret i t.ex.
  `deleteIdSql.php`/`markMapsChanged.php` som också gör motsvarande
  lokala `require` av `configSchema.php` – **detta är alltså ett
  konsekvent mönster i manage-modulen** (till skillnad från
  writeConfig-modulen där konstanter oftast lästes högre upp), värt att
  notera som en skillnad i kodstil mellan de två stora modulerna snarare
  än en bugg i endera.
- **`categories()`s namn `"Alla"` är hårdkodat på svenska** direkt i
  logiken (inte via någon översättningsfunktion som `toSwedish()`) –
  konsekvent med att UI-text genomgående är på svenska i hela
  kodbasen, men värt att notera som en skillnad mot `toSwedish()`-
  mönstret som annars använts för att översätta interna namn.
- Ingen av filerna har `strict_types` eller fullständig parametertypning
  (returtyper anges ibland i kommentarer men inte i kod), konsekvent
  med övriga äldre delar av kodbasen.
