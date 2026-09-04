# Group layer fix-modul (grouplayerfix)

**Entry point:** `adm/grouplayerfix.php`
**Funktionsfiler:** `adm/functions/grouplayerfix/*.php`

## Syfte
En specialiserad proxy mot QGIS Server som löser ett QGIS-specifikt
problem: QGIS Server stödjer inte alltid "grupplager" (ett lagernamn som
egentligen representerar en grupp av flera underliggande lager) för vissa
anropstyper. Denna proxy upptäcker sådana fall, tar reda på gruppens
faktiska underlager (via QGIS-projektets `GetProjectSettings`-XML,
cachad), och expanderar anropet till att gälla alla underlager istället –
transparent för den anropande klienten (Origo-kartan).

Hanterar två specialfall (se nedan) och vidarebefordrar allt annat
oförändrat till QGIS Server.

## Anropas med
`grouplayerfix.php?qgis_url=<url-eller-relativ-path>&<godtyckliga WMS/WFS-parametrar>`

| Parameter | Beskrivning |
|---|---|
| `qgis_url` | Full URL eller path som börjar med `/` till QGIS Server. Om relativ path byggs full URL från klientens `HTTP_HOST` |
| `ttl` | (valfri) Cache-livslängd i sekunder för projektstruktur/describeFeatureType, default 600 |
| övriga | Godtyckliga WMS/WFS-parametrar, vidarebefordras till QGIS Server |

**Specialfall 1 – WMS GetMap med FILTER på grupplager:**
Triggas när `REQUEST=GetMap`, `SERVICE=WMS`, `FILTER` är satt, och
`LAYERS`/`layers` pekar på ett lager som visar sig vara en grupp. Både
`LAYERS` och `FILTER` expanderas till att räkna upp gruppens underlager.

**Specialfall 2 – WFS describeFeatureType på grupplager:**
Triggas när `request=describeFeatureType`, `service=WFS`, `typeName` är
satt. Om `typeName` är en grupp görs **parallella** describeFeatureType-
anrop (via `curl_multi`) mot varje underlager, med individuell retry
(upp till 4 försök per lager, exponentiell backoff), och resultaten slås
ihop till ett enda `featureTypes`-svar.

**Allt annat:** vidarebefordras oförändrat via `forwardToQgisServer()`.

## Beror på
**OBS: `functions/common/` inkluderas INTE i denna modul** – till skillnad
från nästan alla andra moduler vi dokumenterat. Detta är medvetet
kommenterat i koden (`//includeDirectory("./functions/common");`) men
motivet är inte förklarat. Konsekvens: ingen databasanslutning, ingen
session, ingen av de vanliga common-hjälpfunktionerna är tillgängliga
här – modulen är helt fristående från resten av `adm/`.

**Cache:** APCu (`apcu_fetch`/`apcu_store`) om tillgängligt, annars
filbaserad cache i systemets temp-katalog (`sys_get_temp_dir()`). Loggar
ett meddelande om APCu saknas.

**Externt system:** QGIS Server, anropat via `curl` (både enskilda
anrop och parallella `curl_multi`-anrop).

**Filsystem:** skriver cache-filer till `sys_get_temp_dir()`, läses
tillbaka baserat på filens ändringstid jämfört med TTL.

## Filer och funktioner

*Sorterad alfabetiskt efter filnamn.*

| Fil | Funktion | Beskrivning |
|---|---|---|
| `forwardToQgisServer.php` | `forwardToQgisServer($url, $params, $maxRetries = 4): array` | Skickar ett anrop till QGIS Server via curl, med retry vid transienta fel (ingen HTTP-status alls, d.v.s. nätverksfel – **inte** vid 4xx/5xx specifikt, se flaggning). Returnerar `['body' => ..., 'headers' => ...]` |
| `getCachedDescribeFeatureType.php` | `getCachedDescribeFeatureType($qgisUrl, $typeName): string` | Hämtar describeFeatureType-JSON för ett enskilt lager, cachad enligt samma mönster |
| `getCachedProjectSettings.php` | `getCachedProjectSettings($qgisUrl): string` | Hämtar QGIS-projektets `GetProjectSettings`-XML, cachad (APCu eller fil) enligt TTL |
| `getLayerNamesInGroup.php` | `getLayerNamesInGroup($xml, $groupName): array` | Rekursiv, namespace-säker XPath-sökning: hittar alla "löv"-lagernamn under en given grupp i projekt-XML:en. Returnerar tom array om `$groupName` inte är en grupp |
| `getResponseContentType.php` | `getResponseContentType($params): string` | Bestämmer Content-Type baserat på `outputFormat`-parametern, annars `text/xml`. **Verkar oanvänd** – se flaggning |

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **⚠️ Sannolik bugg: odefinierad variabel `$DEFAULT_QGIS_SERVER_PATH`.**
  Koden sätter `$DEFAULT_QGIS_SERVER_URL = '';` men läser sedan
  `$_GET['qgis_url'] ?? $DEFAULT_QGIS_SERVER_PATH` – **fel variabelnamn**
  (`_PATH` istället för `_URL`). Eftersom `$DEFAULT_QGIS_SERVER_PATH`
  aldrig sätts, kommer detta antingen ge en PHP-varning (undefined
  variable) och falla tillbaka till `null`/tom sträng, eller ett fatalt
  fel beroende på PHP-version och felnivå. I praktiken fungerar koden
  troligen ändå eftersom `qgis_url` verkar skickas med i alla riktiga
  anrop, men fallback-beteendet är trasigt. **Bör rättas** till
  `$DEFAULT_QGIS_SERVER_URL` vid nästa redigering.
- **⚠️ Operatorprecedens-bugg i `getCachedProjectSettings.php`:**
  `if ($response['headers']['http_code'] ?? 0 >= 200 && ...)` – på grund
  av PHP:s operatorprioritet tolkas detta som
  `$response['headers']['http_code'] ?? (0 >= 200 && ...)`, **inte** som
  avsett `($response['headers']['http_code'] ?? 0) >= 200`. Eftersom
  `0 >= 200` alltid är `false`, blir hela högerledet `?? false`, vilket
  betyder att villkoret i praktiken bara utvärderas till sanningsvärdet
  av `$response['headers']['http_code']` direkt (utan `>= 200`-kontrollen
  någonsin appliceras meningsfullt). **Detta är en riktig bugg** som gör
  att cachning kan ske (eller utebli) på fel grunder. Jämförelsevis har
  `getCachedDescribeFeatureType.php` samma kontroll skriven korrekt med
  parenteser (`$httpCode >= 200 && $httpCode < 300`) – bra referens för
  hur det ska se ut. **Rekommenderas åtgärdas snarast**, då det är i en
  cache-vägen och kan orsaka cachning av felaktiga/tomma svar.
- **⚠️ Duplicerad kodblock i `grouplayerfix.php`:** i specialfall 2
  (describeFeatureType) finns två identiska kommentarsblock och
  variabeltilldelningar i rad:
```php
  // Grupp → parallell hämtning av describe för alla lager
  $combinedFeatureTypes = [];
  ...
  // Grupp → parallell hämtning av describe för alla lager med retry per handle
  $combinedFeatureTypes = [];
  ...
```
  (samma för `$ttl`-blocket). Ofarligt (andra tilldelningen skriver bara
  över med samma värden) men tydligt en kopieringsrest från
  AI-assisterad redigering. Kandidat för enkel städning.
- **`forwardToQgisServer()`s retry-villkor skiljer sig från
  `fetchWithStatus()`i restrictedLayer-modulen:** här görs om vid
  *nätverksfel* (`$rawResponse === false || $httpCode === 0`), medan
  `fetchWithStatus()` gör om specifikt vid *5xx-svar*. Två olika
  QGIS-proxyer i samma kodbas med olika retry-strategier för i grunden
  samma typ av problem (opålitlig bakomliggande karttjänst) – värt att
  fundera på om detta är medvetet (olika krav på de två endpointarna)
  eller om det vore bättre att harmonisera till en delad
  retry-hjälpfunktion vid refaktorering.
- **`getResponseContentType()` verkar oanvänd** i den kod vi ser –
  `grouplayerfix.php` sätter Content-Type-headers direkt inline på flera
  ställen istället för att anropa denna funktion. Kandidat för
  borttagning, eller så är den avsedd för framtida bruk – oklart utan
  mer kontext.
- **Mycket omfattande `// GROK:`-kommentering** genomgående i denna
  modul (betydligt mer än i forwardauth-modulen där vi såg det första
  gången) – bekräftar att denna modul till stor del AI-genererats/
  redigerats. Kommentarerna är i sig informativa och kan behållas, men
  om ni vill ha en enhetlig kommentarstil i kodbasen är det här den fil
  där flest sådana kommentarer behöver bedömas/städas.
- **Bekräftat: ingen säkerhetsrisk trots avsaknad av behörighetskontroll.**
  `grouplayerfix.php` och `restrictedLayer.php` pekar mot **två skilda
  QGIS-tjänster**, så avsaknaden av koppling till `RESTRICTEDLAYERS`/
  `$_SESSION['user']` i grouplayerfix läcker inte skyddad information.
  Det finns för närvarande inget behov att slå ihop de två proxy-vägarna.
- **Begränsad nuvarande användning:** enligt uppgift har
  `grouplayerfix.php` för närvarande begränsad användning i systemet.
  Kan vara relevant vid prioritering av framtida refaktoreringsinsatser
  – troligen lägre prioritet än mer centrala moduler som `manage`.
- **Curl-inställningarna är dupliterade tre gånger** inom
  `grouplayerfix.php` (initiering + retry-block för describeFeatureType)
  och en fjärde gång i `forwardToQgisServer.php` – samma
  `CURLOPT_*`-uppsättning kopierad om och om igen. Stark kandidat för att
  brytas ut till en delad hjälpfunktion, t.ex. `buildQgisCurlHandle($url,
  $headers)`, vid förenkling.
- Ingen `strict_types` eller parametertypning i någon fil.
