# origo
https://github.com/Kristianstad/origo/pkgs/container/origo

Docker image of Origo (https://github.com/origo-map). The image is built on https://github.com/Kristianstad/nginx/pkgs/container/nginx (check out for webserver settings). Listens on port 8080 internally. Files and directories in the Origo config directory are added to the Origo web directory at startup. There is also an optional management tool for Origo and metadata included in the -adm tag. (Path to manage tool is adm/manage.php and default login is origo, origo. Each created map gets their own html-file. Source code for the management tool is available in the with_php branch.)

Try out the image in [Iximiuz Labs](https://labs.iximiuz.com/playgrounds):
```
1. Start a Docker playground.
2. Run the following command at the command prompt:
   docker run -p 8080:8080 ghcr.io/kristianstad/origo:2.10.0-adm
3. Klick Expose ports in the menu and make port 8080 exposed publicly, then click on the url.
4. To access the admin tool add "/adm/" to the url and login with origo, origo.
```

A swedish tutorial of the management tool is available [Here](https://raw.githubusercontent.com/Kristianstad/origo/refs/heads/with_php/finalfs/www/Origo_admin_tutorial_swedish.pdf).

## Docker run examples
### If you just need Origo
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:2.10.0
### If you also want Kristianstad's management tool for Origo and metadata
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:2.10.0-adm

## Environment variables
### Runtime variables with default value
* VAR_LINUX_USER="nginx" (User running VAR_FINAL_COMMAND)
* VAR_ORIGO_CONFIG_DIR="/etc/origo" (Directory containing configuration files for Origo)
* VAR_CONFIG_DIR="/etc/nginx" (Directory containing configuration files for Nginx)
* VAR_LOG_LEVEL="info"
* VAR_ADMUSER="origo" (Only for management tool)
* VAR_ADMPASSWORD="origo" (Only for management tool)
* VAR_FINAL_COMMAND="nginx -g 'daemon off; error_log stderr \$VAR_LOG_LEVEL;'" (Command run by VAR_LINUX_USER)

### Format of runtime configuration variables (mainly used by the with_php tag)
* VAR_wwwconf_&lt;param name&gt;: Parameter in <span>ww</span>w.conf.
* VAR_phpini_&lt;param name&gt;: Parameter in /etc/php7/conf.d/50-setting.ini (overrides defaults set in php.ini).
* Dot (.) is representated as double underscore (\_\_) in variable names.
* VAR_ldapconf_&lt;param name&gt;: Parameter in /etc/ldap/ldap.conf.

## Capabilities
Can drop all but CHOWN, SETPCAP, SETGID and SETUID.

# Installation av administrationsverktyg för Origo

Detta repository innehåller Kristianstads kommuns PHP-baserade administrationsverktyg för Origo. Verktyget hanterar bland annat kartor, lager, grupper, metadata, behörighetsklassning och publicering av Origo-konfiguration.

Den rekommenderade installationen använder den publicerade `-adm`-imagen:

`ghcr.io/kristianstad/origo:2.10.0-adm`

Imagen innehåller nginx, PHP-FPM, PostgreSQL, Origo och administrationsverktyget.

## Ställningstaganden inför installation

### Windows eller Linux

Linux rekommenderas för produktion. Docker Engine kan köras utan Docker Desktop, har lägre overhead och passar bättre på servrar. Nackdelen är att serverns uppdateringar, brandvägg, TLS, backup och övervakning måste hanteras av driftorganisationen.

Windows passar bra för utveckling, test och demonstration. [Docker Desktop](https://www.docker.com/products/docker-desktop/) ger en enkel Docker-/WSL2-miljö, men har mer overhead och kan innebära licenskostnad beroende på organisation och användning. Kontrollera aktuella Docker-villkor före kommersiell användning.

### Docker eller installation helt utan Docker

Docker är rekommenderat eftersom nginx, PHP-FPM, PHP-tillägg, PostgreSQL, Origo och Composer-bibliotek paketeras i en testad kombination. Samma image kan användas i utveckling, test och produktion. En docker-installation kan även kombineras med separata del-tjänster som ligger utanför dockermiljön (läs mer nedan).

Bare-metal-installation helt utan Docker ger mer kontroll men kräver egen installation och samordning av nginx, PHP-FPM, PHP-tillägg, Composer, PostgreSQL, autentisering, filrättigheter och initierings-SQL. Versionsskillnader blir också lättare att introducera.

### Inbyggd Origo eller separat Origo (vid dockerinstallation)

Inbyggd Origo är enklast. Adminverktyg, kartor och preview körs med samma image och på samma värd. Det passar särskilt bra för utveckling, test och mindre installationer.

Separat Origo kan ge oberoende uppgraderingar, skalning och tydligare separation mellan admin och publik kartvisning. Det kräver dock egen reverse-proxy-konfiguration, hantering av delad fillagring etc. Om man redan har en väl fungerande Origo-installation kan det vara bra att låta den ligga separat.

**Rekommendation:** om man redan har en väl fungerande Origo-installation kan det vara bra att fortsätta att använda den, annars är det enklare att använda den inbyggda.

### Inbyggd PostgreSQL eller separat PostgreSQL (vid dockerinstallation)

Inbyggd PostgreSQL är standard i `-adm`-avbilden och passar utveckling, test och mindre installationer. Det krävs altså ingen separat databas, men databas och webbapplikation delar container och livscykel. Datan kan läggas på en permanent lagringsyta som återanvänds vid containeruppgradering, men om avbilden byter Postgresql-version kommer det krävas att man gör databas-dump och återställning.

Installation av en separat PostgreSQL-databasserver kräver en hel del arbete, men om man redan har tillgång till en sådan databasserver är en separat databas ofta att föredra. Bland annat blir uppgraderingar smidigare.

**Rekommendation:** inbyggd PostgreSQL för utveckling/test och separat PostgreSQL för produktion med högre driftkrav.

## Installation med Docker

### Förutsättningar

Installera Docker Engine på Linux eller Docker Desktop på Windows. Kontrollera installationen:

```bash
docker --version
```

På Windows ska Docker Desktop vara startat och använda Linux-containrar.

### Starta publicerad image

```bash
docker pull ghcr.io/kristianstad/origo:2.10.0-adm
docker run --name origo-admin \
  --detach \
  --restart unless-stopped \
  --publish 8080:8080 \
  --env VAR_ADMUSER=origo \
  --env VAR_ADMPASSWORD=byt-det-har-losenordet \
  --mount type=bind,source=/srv/origo-admin/pgdata,target=/pgdata \
  --mount type=bind,source=/srv/origo-admin/constants,target=/www/adm/constants \
  --mount type=bind,source=/srv/origo-admin/maps,target=/www/maps \
  ghcr.io/kristianstad/origo:2.10.0-adm
```

Öppna <http://localhost:8080/adm/> och logga in med de angivna uppgifterna. Använd inte standardlösenordet `origo` i en nätåtkomlig miljö.

### Hostkataloger och fileshare

Exemplet ovan använder bind mounts i stället för namngivna Docker-volymer. En
bind mount kopplar en vanlig katalog på hosten direkt till en katalog i
containern. Det gör backup, inspektion och flytt av data enklare. Katalogerna
som anges som `source` måste redan existera på hosten.

Windows-exempel i PowerShell:

```powershell
docker run --name origo-admin --detach --publish 8080:8080 `
  --env VAR_ADMUSER=origo `
  --env VAR_ADMPASSWORD=byt-det-har-losenordet `
  --mount type=bind,source=C:\origo-admin\pgdata,target=/pgdata `
  --mount type=bind,source=C:\origo-admin\constants,target=/www/adm/constants `
  --mount type=bind,source=C:\origo-admin\maps,target=/www/maps `
  ghcr.io/kristianstad/origo:2.10.0-adm
```

En hostkatalog kan även vara en fileshare som monterats på hosten, till exempel
NFS eller SMB. Montera filesharen först och använd sedan dess lokala mountpoint
som `source` i `--mount`. Fileshare är dock inte optimalt för `/pgdata`, där
lokal lagring med tillförlitlig låsning och filsystemsstöd rekommenderas.
Fileshare passar bättre för `/www/maps` och `/www/adm/constants`.

Montera inte en tom katalog över hela `/www`, eftersom det döljer webbkoden som
finns i imagen. Montera i stället de avsedda underkatalogerna:

- `/pgdata` för PostgreSQL-data
- `/www/adm/constants` för lokala anslutnings-, cookie-, auth- och proxyinställningar
- `/www/maps` för genererade kartkonfigurationer

### Kontrollera container och loggar

```bash
docker ps
docker logs -f origo-admin
```

Vid första uppstarten initieras PostgreSQL och SQL-filerna i `finalfs/initdb/` körs. Det kan ta en stund innan tjänsten är klar.

```bash
docker stop origo-admin
docker start origo-admin
docker rm -f origo-admin
```

Det sista kommandot tar bort containern men inte hostkatalogerna. Ta inte bort
`/srv/origo-admin/pgdata` eller `C:\origo-admin\pgdata` om databasen ska behållas.

### Port och reverse proxy

För lokal utveckling räcker `--publish 8080:8080`. I produktion bör containern ligga bakom en reverse proxy med HTTPS, DNS, brandvägg och begränsad åtkomst till `/adm/`.

Om endast en lokal reverse proxy ska nå containern:

```bash
--publish 127.0.0.1:8080:8080
```

## Installation utan Docker

Välj detta endast om organisationen redan har en plattform för PHP och
PostgreSQL. Microsoft IIS kan användas i stället för nginx, men detta är en
manuell integrationsinstallation och inte den primära, testade
distributionsvägen.

IIS kan köra PHP via FastCGI. Du behöver då själv konfigurera PHP för IIS,
FastCGI-processen, statiska filer, URL-/sökvägshantering, autentisering och
skrivbehörigheter. Nginx-konfigurationen i projektet kan inte användas direkt
i IIS. PHP måste fortfarande ha tillgång till PostgreSQL, Composer-bibliotek,
`/www/adm/constants`, `/www/maps` och eventuella `/services`-filer.

**Rekommendation:** använd IIS endast om organisationen redan har standardiserad
PHP/FastCGI-drift på Windows. Använd annars Docker Desktop för utveckling eller
Linux med Docker Engine för serverdrift.

Installera:

- nginx eller Apache med PHP-FPM, eller IIS med PHP NTS och FastCGI
- PHP med `json`, `pgsql`, `session`, `simplexml`, `openssl` och vid LDAP `ldap`
- PostgreSQL
- Composer och Git
- Composer-paketen `adldap2/adldap2`, `matthiasmullie/minify`, `thenetworg/oauth2-azure` och `league/oauth2-client`

Placera webbkoden så att `finalfs/www/adm/` motsvarar `/www/adm/`. PHP/FastCGI
måste kunna läsa koden och skriva till `$webRoot/maps` när kartkonfigurationer
publiceras.

På samma sätt som i Docker bör `/www/adm/constants` monteras från en katalog
på hosten. Där ligger lokala anslutnings-, cookie-, auth- och proxyinställningar.
Montera även `/pgdata` på hosten om PostgreSQL körs lokalt i den egna
installationen.

Skapa databasen och initiera schemat:

```bash
psql --dbname=origo --file=finalfs/initdb/050.postgres.sql
psql --dbname=origo --file=finalfs/initdb/060.origo.sql
```

Anpassa därefter `finalfs/www/adm/constants/dbhConnectionString.php`. Standardsträngen använder `localhost`, databasen `origo`, användaren `postgres` och lösenordet `postgres`; detta ska inte användas oförändrat i produktion.

Webbservern måste skicka PHP-filer till PHP-FPM eller IIS FastCGI, skydda
`/adm/`, ge PHP tillgång till PostgreSQL och tillåta skrivning av genererade
kartfiler. Studera Dockerfile och `finalfs/start/stage3/100.authnginx` för
nginx-referens; IIS kräver motsvarande manuell FastCGI-konfiguration.

## Origo-konfiguration och kartor

Egna kartor läggs till genom adminverktyget och publiceras med **Skriv kartkonfiguration**. `writeConfig.php` skapar kataloger under `$webRoot/maps/<kartnamn>` rekursivt.

Om extern Origo används ska `/www/maps` monteras från samma gemensamma
lagringsplats som den externa Origo-installationen läser från. Det kan vara en
lokal hostkatalog eller en monterad fileshare. Annars kan adminverktyget skriva
kartkonfigurationen utan att den publika Origo-servern hittar filerna.

## Konfiguration och miljövariabler

Viktiga Docker-variabler:

| Variabel | Syfte |
|---|---|
| `VAR_ADMUSER` | Användarnamn för adminautentisering |
| `VAR_ADMPASSWORD` | Lösenord för adminautentisering |
| `VAR_LOG_LEVEL` | Nginx-loggnivå |
| `VAR_LINUX_USER` | Användare för huvudprocessen |
| `VAR_phpini_*` | PHP-konfiguration |
| `VAR_wwwconf_*` | PHP-FPM-konfiguration |

Verifiera alltid variabler mot den valda image-versionen. Lägg inte känsliga värden i Git, publika kommandon eller shellhistorik.

## Backup och uppgradering

Säkerhetskopiera minst hostkatalogen som monteras på `/pgdata`, hostkatalogen
som monteras på `/www/maps`, samt `/www/adm/constants` och egna proxy-
konfigurationer. En logisk dump kan skapas så här:

```bash
docker exec origo-admin pg_dump --username postgres --dbname origo > origo.sql
```

Testa återställning i en separat databas.

Vid uppgradering: ta backup, hämta ny image, stoppa och ta bort containern utan att ta bort hostkatalogerna, starta med samma bind mounts och kontrollera loggar, admin och publicerade kartor. Kör inte om initierings-SQL manuellt mot en befintlig databas utan att först kontrollera om tabeller och data redan finns.

### Databasschema vid ny version

En ny Docker-image uppdaterar inte automatiskt en redan initierad PostgreSQL-
databas. `finalfs/initdb/060.origo.sql` används framför allt när databasen
skapas första gången. Om en ny version innehåller nya tabeller, kolumner, index,
constraints eller grunddata i den filen måste databasschemat därför kontrolleras
och uppdateras separat.

Jämför den gamla och den nya versionens initierings-SQL innan uppgraderingen:

```bash
git diff OLD_VERSION..NEW_VERSION -- finalfs/initdb/060.origo.sql
```

Testa schemaändringen på en återställd kopia av produktionsdatabasen. Kontrollera
bland annat att befintliga data passar nya `NOT NULL`-kolumner, att nya index och
constraints kan skapas och att eventuella nya standardrader inte krockar med
lokalt ändrade rader. Kör sedan endast de nödvändiga `CREATE TABLE`, `ALTER
TABLE`, `CREATE INDEX` eller `INSERT`-satserna i en kontrollerad SQL-migrering,
helst i en transaktion när PostgreSQL tillåter det.

Kör inte hela `060.origo.sql` mot en befintlig produktionsdatabas. Filen
innehåller bland annat tabellskapande och initiala `INSERT`-satser som kan ge
fel eller duplicera data. Ta en ny backup efter migreringen och verifiera att
adminverktyget kan läsa och skriva alla berörda tabeller innan den nya versionen
öppnas för användare. Samma schemaarbete krävs oavsett om adminverktyget körs
med eller utan Docker.

### Uppgradera administrationsverktyget från GitHub

Källkoden för administrationsverktyget finns i branchen `with_php` på
[GitHub](https://github.com/Kristianstad/origo/tree/with_php). Säkerhetskopiera
databasen, `/www/maps` och `/www/adm/constants` före uppgradering. Kontrollera
även ändringar i initierings-SQL och databasschema innan en ny version tas i
drift.

Med Docker rekommenderas normalt den publicerade imagen. Hämta den nya
image-versionen med `docker pull` och skapa om containern med samma bind mounts.

Stoppa därefter den gamla containern och starta den nya imagen med samma
miljövariabler, portar och bind mounts. Ta inte bort hostkatalogerna.

Utan Docker uppdateras en befintlig checkout i stället för en container:

```bash
cd /sökväg/till/origo
git fetch origin
git checkout with_php
git pull --ff-only origin with_php
```

Stoppa webbservern eller PHP-processen under filuppdateringen, installera eller
uppdatera de Composer-paket som installationen använder, och publicera de nya
filerna från `finalfs/www/adm/`. Starta sedan webbservern/PHP igen och kontrollera
inloggning, kartskrivning, loggar och eventuella schemaändringar. Behåll lokala
filer i `/www/adm/constants` och kartdata i `/www/maps`.

## Felsökning

### Adminsidan går inte att öppna

```bash
docker ps
docker logs origo-admin
```

Kontrollera därefter port, brandvägg och URL:en `/adm/`.

### Inloggningen fungerar inte

Kontrollera `VAR_ADMUSER`, `VAR_ADMPASSWORD`, auth-konfiguration, cookies, HTTPS och reverse-proxy-sökväg.

### Kartkonfigurationen skrivs inte

Kontrollera att kartan är markerad som ändrad, att PHP-FPM eller IIS FastCGI
kan skriva `$webRoot/maps`, att PostgreSQL fungerar och att kartan finns i
`map_configs.maps`.

### PostgreSQL startar inte

Kontrollera lagringskatalogens rättigheter, `docker logs` och att `/pgdata` inte används med en inkompatibel PostgreSQL-version.

## Säkerhetschecklista före produktion

- Byt standardlösenordet.
- Använd HTTPS.
- Begränsa åtkomsten till `/adm/`.
- Ordna med regelbundna backuper.