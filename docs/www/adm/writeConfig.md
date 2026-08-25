# Write config-modul (writeConfig)

**Entry point:** `adm/writeConfig.php`
**Funktionsfiler:** `adm/functions/writeConfig/*.php`

**⚠️ STATUS: preliminär dokumentation.** Följande filer i
`functions/writeConfig/` är **ännu inte granskade** och saknas i detta
utkast: `addLayersToJson.php` (506 rader – central funktion, avgörande
för fullständig förståelse), `publishMapFiles.php`,
`renderCssTags.php`, `renderJavaScriptTags.php`, `saveFile.php`,
`pgArrayToText.php`, `pgBoolToText.php`, `pgBoxToText.php`,
`pgCoordsToText.php`. Detta dokument uppdateras när dessa granskats.

## Syfte
Den centrala "publiceringsfunktionen" i systemet: läser en kartas
fullständiga konfiguration från databasen (lager, grupper, källor,
stilar, kontroller, plugins) och genererar dels en Origo-kompatibel
JSON-konfiguration, dels en komplett, fristående, minifierad HTML-sida
som innehåller den färdiga kartan. Skriver resultatet till disk under
`<webRoot>/maps/<mapNamn>/`. Genererar även SEO-relaterad strukturerad
data (schema.org JSON-LD) och en `sitemap.xml` för kartor som är
markerade som sökmotorindexerbara. **Skriver även `RESTRICTEDLAYERS`-
konstanten** som används av `restrictedLayer.php` (se nedan) – detta är
den bekräftade källan till den kopplingen vi tidigare flaggade som
öppen fråga.

Anropas troligen från en "Publicera"/"Spara"-knapp i `manage.php` (se
`functions/manage/printWriteConfigButton.php`, ej dokumenterad ännu).

## Anropas med
`writeConfig.php?map=<mapId>&<flaggor>`

| Parameter | Beskrivning |
|---|---|
| `map` | Id för kartan att publicera. Kan innehålla ett extra "swiper"-relaterat suffix separerat med `\` (workaround, se kod-kommentar i writeConfig.php) |
| `getJson` | `y` → returnera bara JSON-konfigurationen istället för HTML |
| `getHtml` | `y` → generera en förhandsgranskningsversion (annan bas-URL, från `previewBase` istället för `proxyRoot`); kan kombineras med `group`/`layer` för att förhandsgranska ett enskilt lager/grupp isolerat |
| `download` | `y` → skicka resultatet som nedladdningsbar fil istället för att visas i webbläsaren |
| `group` / `layer` | (endast med `getHtml=y`) begränsar förhandsgranskningen till en specifik grupp eller ett specifikt lager |
| `badJson` | Internt flöde – sätts av koden själv vid JSON-fel för att visa rå (ej validerad) JSON för felsökning, inte avsett att anropas direkt av användare |

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `dbh()`, `configTables($dbh)` – **ny, ej tidigare dokumenterad**,
  hämtar sannolikt samtliga konfigtabeller (`maps`, `groups`, `layers`,
  `sources`, `services`, `styles`, `plugins`, `controls`, `proj4defs`,
  `tilegrids`, `footers`, m.fl.) i ett svep, extraheras sedan till
  lokala variabler med `extract()` (se flaggning nedan)
- `pgArrayToPhp()`, `array_column_search()`
- `defineFileConstant($name, $value)` – **ny, ej tidigare dokumenterad**,
  skriver en PHP-konstant till en fil på disk (grund för hur
  `RESTRICTEDLAYERS` och andra `includeFileConstant()`-lästa konstanter
  uppstår)

**Konstanter:**
- `constants/webRoot.php` → `$webRoot`
- `constants/proxyRoot.php`, `constants/previewBase.php`
- `constants/searchEngineMeta.php` → geo/publisher-metadata för
  strukturerad data

**Externt bibliotek:** `matthiasmullie/minify` (composer,
`../../composer/minify/autoload.php`) för CSS/JS-minifiering.

**Databas:** läser samtliga konfigtabeller (via `configTables()`),
skriver `maps.changed = 'f'` (via `markMapUnchanged`).

**Filsystem:** skriver till `<webRoot>/maps/<mapNamn>/index[nummer].html`,
`structured-data[nummer].json`, `sitemap.xml` (troligen via
`publishMapFiles()`, ej sedd ännu). Skapar mappen om den inte finns.

## Filer och funktioner (granskade hittills)

| Fil | Funktion | Beskrivning |
|---|---|---|
| `addControlsToJson.php` | `addControlsToJson($mapControls=null, &$mapCss, &$mapJs, &$mapOnload)` | Bygger JSON-arrayen för kartkontroller, samlar även ihop respektive kontrolls CSS/JS/onload-kod i de refererade variablerna |
| `addGroupsToJson.php` | `addGroupsToJson($mapGroups)` | Rekursivt: bygger JSON för grupphierarkin, bygger samtidigt upp `$mapLayers` (global) med vilka lager som hör till varje grupp |
| `addPlugins.php` | `addPlugins($mapPlugins=null, &$mapCssFiles, &$mapJsFiles, &$mapCss, &$mapJs, &$mapOnload)` | Samlar ihop JS/CSS (inline och som filer) samt onload-kod för varje aktiverat plugin |
| `addSourcesToJson.php` | `addSourcesToJson()` | Bygger JSON för datakällor (`source`), inkl. URL-uppbyggnad, tile grid-inställningar, query-parametrar. Läser globalt (`GLOBAL`) istället för parametrar |
| `addStylesToJson.php` | `addStylesToJson()` | Bygger JSON för lagerstilar. Om `style_config` saknas i databasen, byggs en enkel standardstil (label/ikon/filter) istället |
| `array_move.php` | `array_move(&$a, $oldpos, $newpos)` | Generisk hjälpfunktion: flyttar ett element i en array från ett index till ett annat |
| `compressBrotli.php` | `compressBrotli(string $data): ?string` | Komprimerar med Brotli om PHP-tillägget finns, annars `null` |
| `compressGzip.php` | `compressGzip(string $data): ?string` | Komprimerar med gzip (nivå 9) |
| `createSymlinkIfNotExists.php` | `createSymlinkIfNotExists(string $target, string $link): bool` | Skapar en symlänk om den inte redan finns (undviker fel vid upprepad publicering) |
| `fetchResourceContent.php` | `fetchResourceContent(string $resource): string\|false` | Hämtar innehåll från en lokal fil eller URL, med tillfälligt katalogbyte för att lösa relativa sökvägar. Används sannolikt för att bädda in externa CSS/JS-resurser i den publicerade HTML-sidan |
| `fixDuplicateDeclarations.php` | `fixDuplicateDeclarations($jsCode): string` | Textbaserad JS-transformation: hittar dubbeldeklarerade variabler (`const`/`let`/`var` med samma namn i samma "rot-scope") i administratörsskriven onload-JS, och skriver om dem till giltig JS (undviker `SyntaxError: Identifier has already been declared`). Se flaggning nedan – detta är en betydande mängd egen parsning |
| `getArrayValuesRecursively.php` | `getArrayValuesRecursively(array $array): array` | Plattar ut en nästlad array till en enkel lista med alla "löv"-värden |
| `groupDepth.php` | `groupDepth($groupIds, $layerIds=[])` | Rekursivt: bygger en nästlad struktur som visar vilka lager som finns i varje grupp (och undergrupper), med lagernamn prefixade med sin grupps sökväg (`grupp>lager`) |
| `indexweightedLayersList.php` | `indexweightedLayersList($layersList)` | Sorterar om lagerlistan baserat på ett `indexweight`-värde per lager – flyttar viktade lager till en specifik position i listan via upprepad `array_move()` |
| `json_format.php` | `json_format($json): string` | Formaterar en JSON-sträng med indrag för läsbarhet (egen handskriven parser, äldre ursprung enligt kodkommentar – "Nicejson", 2008) |
| `markMapUnchanged.php` | `markMapUnchanged(&$dbh, $mapId)` | Sätter `maps.changed = 'f'` efter lyckad publicering |
| `pgArrayToText.php` | `pgArrayToText($pgArray): string` | Konverterar Postgres arraysyntax (`{a,b,c}`) till kommaseparerad text utan klamrar – enklare variant av `pgArrayToPhp()` som ger en sträng istället för en PHP-array |
| `pgBoolToText.php` | `pgBoolToText($pgBool)` | Konverterar Postgres bool-representation (`'t'`/`'f'`) till JS-litteralerna `"true"`/`"false"`. Returnerar värdet oförändrat om det inte är `'t'`/`'f'` |
| `pgBoxToText.php` | `pgBoxToText($pgBox): string` | Konverterar Postgres box-syntax (`(x2,y2),(x1,y1)`) till en kommaseparerad koordinatlista, och sorterar hörnen så att den mindre x-koordinaten kommer först |
| `pgCoordsToText.php` | `pgCoordsToText($pgCoords): string` | Konverterar Postgres punkt-syntax (`(x,y)`) till kommaseparerad text utan parenteser |
| `publishMapFiles.php` | `publishMapFiles($filepathWithoutSuffix, $html, $json, $mapId): void` | Skriver den genererade HTML- och JSON-filen till disk, skapar Brotli- och gzip-komprimerade varianter av båda (om stöd finns), och skapar publika symlänkar i webbroten som pekar på de faktiska filerna |
| `renderCssTags.php` | `renderCssTags(array $items): string` | Bygger HTML för CSS-inkludering: `include(sökväg)`-syntax läses in och minifieras som inline `<style>`, annars renderas som vanlig `<link rel="stylesheet">` |
| `renderJavaScriptTags.php` | `renderJavaScriptTags(array $items): string` | Bygger HTML för JS-inkludering: stödjer `include(...)`/`include_minify(...)` (minifieras) och `include_nominify(...)` (lämnas oförändrad, för redan minifierade bundles), annars renderas som vanlig `<script src="...">` |
| `saveFile.php` | `saveFile(string $path, string $content): bool` | Enkel, defensiv wrapper runt `file_put_contents()` |
| `addLayersToJson.php` | `addLayersToJson($mapLayersList, &$layersMeta, $groupLayer=false)` | Den mest centrala och komplexa funktionen i modulen (506 rader). Bygger JSON för varje lager: grundfält, typspecifik logik (WMS/WFS/GEOJSON), en sammansatt HTML-"abstract"-beskrivning, legend-/ikon-URL:er mot bakomliggande WMS-tjänst, hantering av klusterstilar. Rekursiv för GROUP-lager. Samlar även ihop `$mapSources`/`$mapStyles` och avslutar (vid toppnivåanrop) med att trigga `addSourcesToJson()`/`addStylesToJson()` |

## Kända begränsningar / observationer (preliminära – gäller granskade filer)

- **⚠️ JSON byggs som strängkonkatenering, inte som PHP-array +
  `json_encode()`.** Hela `$json`-variabeln i `writeConfig.php` byggs
  upp bit för bit med `.` -konkatenering av handskriven JSON-syntax
  (citattecken, kommatecken, hakparenteser skrivs manuellt). Detta gäller
  genomgående i `addControlsToJson`, `addGroupsToJson`, `addSourcesToJson`,
  `addStylesToJson` m.fl. Risk: databasvärden (titlar, abstracts, etc.)
  som innehåller citattecken eller specialtecken kan **förstöra JSON-
  strukturen** om de inte är korrekt escapade innan de når databasen.
  `writeConfig.php` validerar visserligen den färdiga strängen med
  `json_decode($json) === null` och visar ett felmeddelande om den är
  trasig – men detta upptäcker bara att något gick fel, inte *vilket*
  fält som orsakade det, vilket gör felsökning svårt för en
  administratör som fyllt i en titel med ett citattecken i. **Detta är
  den enskilt största kandidaten för förenkling** i hela modulen:
  bygga upp en vanlig PHP-array/associativ struktur och avsluta med ett
  enda `json_encode()`-anrop skulle eliminera hela denna kategori av
  buggar. Stor omskrivning dock, inte en snabb fix.
- **Tung användning av globala variabler (`GLOBAL`)** genomgående i
  writeConfig-funktionerna (`$json`, `$map`, `$groups`, `$layers`,
  `$sources`, `$services`, `$tilegrids`, `$mapLayers`, `$mapSources`,
  `$mapStyles`, `$controls`, `$plugins` m.fl.), i kombination med
  `extract($configTables)` i huvudfilen som skapar okänt antal lokala
  variabler dynamiskt. Detta gör det **mycket svårt att spåra
  dataflödet** genom modulen utan att läsa alla filer samtidigt – en
  funktion som `addSourcesToJson()` tar inga parametrar alls utan
  litar helt på att rätt globala variabler redan är satta i rätt skick
  när den anropas. Detta är den svåraste delen av hela kodbasen att
  bryta ner i mindre, oberoende delar utan en genomgripande omskrivning
  till att skicka data explicit via parametrar/returvärden.
- **`extract($configTables)`** skapar lokala variabler med namn som
  bestäms av `configTables()`s returvärde, vilket inte syns i
  `writeConfig.php` själv. Det går inte att veta vilka variabler
  (`$maps`, `$groups`, etc.) som faktiskt existerar utan att läsa
  `configTables.php` (finns i `functions/common/`, ej dokumenterad
  ännu). Detta är samma typ av "dolt beroende" som vi flaggade för
  `allLayerIds()` i export-modulen, men i större skala.
- **`fixDuplicateDeclarations()` är en handskriven, förenklad
  JS-parser** (radbaserad, med enkel sträng/scope-djup-spårning via
  räkning av `{`/`}`). Den hanterar inte flerradiga deklarationer,
  kommentarer som innehåller `{`/`}`, template literals, eller andra
  JS-syntax-särfall fullt ut. Fungerar sannolikt för det begränsade
  JS-mönster administratörer faktiskt skriver i onload-fält, men är
  skört mot mer komplex JS. Innehåller även en stor utkommenterad
  kodsektion (`$mapOnloadInit`) samt ett exempel-testblock i botten av
  filen – båda kan städas bort om funktionen anses stabil.
- **`json_format()` är en egen, handskriven JSON-formaterare** från
  2008 (enligt kommentar), med en kommentar om att anpassa den till
  PHP ≥5.4 – PHP har sedan version 5.4 haft `JSON_PRETTY_PRINT` inbyggt
  i `json_encode()`. Funktionen har redan logik för att använda detta
  (`if (phpversion() >= 5.4) return json_encode($json, JSON_PRETTY_PRINT);`)
  men **bara om indata inte redan är en sträng** – eftersom
  `writeConfig.php` alltid skickar in en redan färdig JSON-**sträng**
  (`json_format($json)` där `$json` redan är en strängvariabel), tas
  denna genväg aldrig i praktiken, och hela den manuella
  tecken-för-tecken-parsningen körs alltid. Kandidat för enkel
  förenkling: `json_encode(json_decode($json), JSON_PRETTY_PRINT |
  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)` skulle sannolikt
  kunna ersätta hela filen, given att `$json` redan valideras med
  `json_decode()` strax innan i `writeConfig.php`.
- **`markMapUnchanged.php` har SQL injection-risk:** `$mapId` klistras
  in direkt i SQL-strängen utan escaping, samma mönster som flera
  tidigare `die()`-baserade underhållsfunktioner. `$mapId` kommer
  ursprungligen från `$_GET['map']` (bearbetad genom flera `explode()`-
  steg, men inte SQL-escapad).
- **`createSymlinkIfNotExists.php` har en `// GROK:`-kommentar** – ännu
  ett tecken på AI-assisterad kod i denna modul, konsekvent med
  mönstret i forwardauth/grouplayerfix.
- **God separation i de mindre hjälpfunktionerna** (`array_move`,
  `compressBrotli`, `compressGzip`, `getArrayValuesRecursively`) – dessa
  är rena, väldokumenterade, testbara funktioner utan globala
  beroenden. Bra förebilder för hur resten av modulen skulle kunna se
  ut efter refaktorering.
- Blandad kodstil: vissa filer har PHP 8-stil typade parametrar och
  returtyper (`compressBrotli`, `fetchResourceContent`,
  `getArrayValuesRecursively` delvis), andra har ingen typning alls
  (`addControlsToJson`, `groupDepth`, m.fl.) – bekräftar att modulen är
  en blandning av äldre och nyare kod, i linje med tidigare
  observationer om kodbasens historik.
- **Dödkod i både `renderCssTags.php` och `renderJavaScriptTags.php`:**
  variabeln `$url` byggs upp (`$proxyRoot . $_SERVER["REQUEST_URI"] . ...
  . 'badJson=y'`) i felhanteringsgrenen men **används aldrig** – koden
  skriver bara ut ett `alert()` med felmeddelandet och avslutar med
  `exit`, utan att navigera till `$url` (till skillnad från motsvarande
  felhantering i `writeConfig.php` självt, som *gör* en
  `window.location.href`-omdirigering till en `badJson=y`-variant för
  felsökning). Sannolikt en ofärdig kopiering av samma mönster – om
  avsikten var att erbjuda samma felsöknings-omväg här, saknas
  `window.location.href`-raden. Enkel att åtgärda eller ta bort
  variabeln om den inte behövs.
- **`renderCssTags.php` och `renderJavaScriptTags.php` är nästan
  identiska** i struktur (loop, regex-matchning av `include(...)`,
  felhantering, minifiering) – JS-varianten har bara fler kommandovarianter
  (`include_minify`/`include_nominify`). Kandidat att slå ihop till en
  gemensam hjälpfunktion med en parameter för tagg-typ, om ni vill minska
  dubblering vid framtida förenkling.
- **Blandat `require` / `require_once`** för samma konstant
  (`constants/proxyRoot.php`) mellan de två annars nästan identiska
  filerna (`renderCssTags.php` använder `require_once`,
  `renderJavaScriptTags.php` använder `require`) – ofarligt eftersom
  koden ändå avslutar med `exit` direkt efter, men ytterligare ett tecken
  på att filerna kopierats från varandra utan fullständig konsekvens.
- **`pgBoxToText()` gör antagandet att en box alltid har exakt två
  koordinatpar** (`explode('),(', ...)` följt av indexering `[0]`/`[1]`
  utan kontroll). Detta är korrekt för Postgres `box`-typen per
  definition, så ingen bugg, men värt att notera som ett implicit
  antagande om indata-formatet.
- **God, tydlig dokumentation i `publishMapFiles.php`** (PHPDoc-kommentar
  med parameterbeskrivningar) – bra förebild jämfört med de äldre
  `pg*ToText`-funktionerna som saknar all dokumentation.
- Fortsatt blandad typning: `publishMapFiles`, `renderCssTags`,
  `renderJavaScriptTags`, `saveFile` har fullständig PHP 8-typning;
  `pgArrayToText`, `pgBoolToText`, `pgBoxToText`, `pgCoordsToText` har
  ingen alls – ytterligare bekräftelse på åldersskiktning inom samma
  funktionsmapp.
