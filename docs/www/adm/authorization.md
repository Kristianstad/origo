# Auktoriseringsmodul (authorization)

**Entry point:** `adm/authorization.php`
**Iframe-wrapper:** `adm/authorization-iframe.php`
**Funktionsfiler:** `adm/functions/authorization/*.php`
**Stilmall:** `adm/styles/authorization.css`
**Extern startpunkt (dokumenteras separat):** `authorization/authorization-loader.php`,
`authorization/authorization-iframe.php` (toppnivå, utanför adm – se separat loader-genomgång)

## Syfte
Hanterar inloggning och utloggning för adminpanelen mot Active Directory
via LDAP (biblioteket `adldap2`). Sätter dels en PHP-session
(`$_SESSION['user']`), dels en egen krypterad cookie som identifierar
användaren mellan sessioner. Visar antingen ett inloggningsformulär eller,
om användaren redan är inloggad, en "inloggad"-vy med utloggningsknapp och
en inbäddad nyhetslista (`news.php?action=subjects`, se news.md).

Stöder en `return_to`-parameter för att skicka användaren till en annan
sida efter lyckad inloggning, skyddad mot open redirect via
`isSafeReturnUrl()`.

## Anropas med
`authorization.php` (GET för att visa formulär, POST för att logga in)

| Parameter | Metod | Beskrivning |
|---|---|---|
| `logout` | GET (finns) | Loggar ut användaren direkt |
| `displaylogout` | GET (finns) | Tvingar fram "inloggad"-vyn (utloggningsknapp) |
| `call` | GET/POST | Skickas tyst genom till inloggningsformuläret (dolt fält), okänt exakt syfte ännu – flaggat nedan |
| `return_to` | GET/POST | URL att skicka användaren till efter lyckad inloggning. Valideras mot open redirect |
| `user`, `passwd` | POST | Inloggningsuppgifter (skickas endast via POST, aldrig GET) |

Route-logiken (i tur och ordning): logout → visa "inloggad"-vy (om
inloggad utan SERVICE-param) → hantera POST som inloggningsförsök → visa
inloggningsformulär.

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `dbh()` – databasanslutning (endast vid LDAP-metod)
- `initUserLdap($dbh)` – hämtar/initierar användarens LDAP-data,
  **skriver och stänger sessionen som en bieffekt** (se `login.php`s
  kommentar `// skriver + stänger sessionen`) – viktigt att känna till
  vid felsökning av sessionsrelaterade buggar
- `ensureSessionWritable()` – säkerställer att sessionen kan skrivas till
  (troligen löser ett låsningsproblem – bör läsas i sin helhet vid
  dokumentation av `common.md`)
- `getCookieOptions($expiryTimestamp)` – bygger array med cookie-inställningar
  (path, domain, secure, httponly, samesite) givet ett utgångsdatum

**Konstanter:**
- `constants/authMethod.php` → `$authMethod`
- `constants/proxyRoot.php` → `$proxyRoot`
- `constants/adldapConfig.php` → `$adldapConfig` (LDAP-anslutningsinställningar)
- `constants/adDomain.php` → `$adDomain`
- `constants/cookieConfig.php` → `$cookieConfig` (cookienamn, krypteringsnyckel, livslängd)

**Externt bibliotek:** `adldap2` (LDAP-klient för PHP), laddas via
`../../composer/adldap2/autoload.php` – en composer-installerad
tredjepartsberoende utanför `adm/`. **OBS:** denna `require_once` ligger
på toppnivå i `login.php` (inte inuti en `if`), vilket betyder att
autoloadern alltid laddas så fort `functions/authorization/` inkluderas
via `includeDirectory()` — även när `$authMethod` inte är `'ldap'`. Se
flaggning nedan.

**Session:** sätter `$_SESSION['user']` vid lyckad inloggning (exakt
struktur sätts av `initUserLdap()`, ej dokumenterad ännu).

**Cookies:** sätter en egen krypterad cookie (`$cookieConfig['cookieName']`)
utöver PHP:s sessionscookie, för att identifiera användaren mellan
sessioner (AES-256-CBC-krypterat användarnamn).

## Filer och funktioner

*Sorterad alfabetiskt efter filnamn.*

| Fil | Funktion | Beskrivning |
|---|---|---|
| `displayHtmlFooter.php` | `displayHtmlFooter()` | Skriver ut avslutande `</body></html>` |
| `displayHtmlHeader.php` | `displayHtmlHeader()` | Skriver ut `<html><head>` inklusive `authorization.css` |
| `displayLogin.php` | `displayLogin()` | Visar HTML-inloggningsformulär (användarnamn + lösenord) |
| `displayLogout.php` | `displayLogout()` | Visar "inloggad"-vy med utloggningsknapp och inbäddad nyhetslista |
| `displayWithHtml.php` | `displayWithHtml($content)` | Wrapper: header + valfritt innehåll + footer |
| `isSafeReturnUrl.php` | `isSafeReturnUrl(string $url): bool` | Whitelist-kontroll mot open redirect – tillåter relativa URL:er eller samma host som `HTTP_HOST` |
| `login.php` | `login(&$dbh)` | Autentiserar mot LDAP, sätter krypterad cookie, initierar session, redirectar till `return_to` eller visar "inloggad"-vy |
| `logout.php` | `logout()` | Förstör session och cookies (session, autentiseringscookie, refresh-cookie), visar inloggningsformulär igen |

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **⚠️ Cookie-format kan vara skört:** i `login.php` byggs cookien som
  `base64_encode($encrypted . '::' . $iv)` – d.v.s. binär krypterad data
  och binär IV slås ihop med separatorn `'::'` som en vanlig sträng.
  Eftersom `$encrypted` är rå binärdata (inte text) finns en teoretisk
  risk att bytesekvensen `::` råkar förekomma i den krypterade datan,
  vilket skulle göra uppdelningen vid avkodning felaktig. Vi har inte
  sett avkodningsfunktionen än (troligen i `common/` eller
  `initUserLdap.php`) – bör verifieras där, men ett säkrare mönster är
  att lagra IV med fast längd (t.ex. alltid först N bytes) istället för
  en textseparator.
- **⚠️ `adldap2`-biblioteket laddas ovillkorligt** vid varje request till
  `authorization.php`, oavsett `$authMethod` – se OBS ovan under "Beror
  på". Om `$authMethod` någon gång är något annat än `'ldap'` (t.ex. vid
  lokal utveckling utan AD) kan detta orsaka ett fatalt fel om
  composer-biblioteket inte är installerat. Kandidat att flytta `require_once`
  in i `login()`-funktionen, villkorat på `$authMethod === 'ldap'`.
- ~~**`displayLogout.php` har dödkodskommentar:** `if ($authMethod === 'ldap')`
  är utkommenterad men koden innanför körs alltid ändå~~ Villkoret är
  aktiv, ej utkommenterad kod – utloggningsknappen byggs faktiskt bara
  när `$authMethod === 'ldap'`. Ingen dödkod, ingen bugg.
- **`$_GET['call']` skickas genom till formuläret som ett dolt fält utan
  synlig användning** i den kod vi sett hittills (varken läses eller
  valideras förutom escaping). Oklart syfte – flaggar för uppföljning,
  eventuellt använt av anropande kod utanför denna modul (t.ex. en
  loader-fil eller frontend-integration).
- **`login.php` och `displayLogout.php` innehåller identisk logik** för
  att avgöra `$src` (news-iframens URL) baserat på
  `basename($formAction) === 'authorization-loader.php'`. Duplicerad kod
  – kandidat för att brytas ut till en delad hjälpfunktion, t.ex.
  `getNewsIframeSrc($formAction)`, när vi förenklar modulen. Kopplingen
  till `authorization-loader.php` (utanför `adm/`) är också ett konkret
  exempel på varför loader-filerna bör dokumenteras – den här modulen
  beter sig olika beroende på hur den anropas utifrån.
- **Lösenord hanteras källkodsmässigt korrekt** (nollas explicit med
  `$passwd = null; unset($passwd);` direkt efter användning) – bra
  säkerhetspraxis värd att lyfta fram som förebild.
- **`isSafeReturnUrl()` är ett bra, återanvänt skyddsmönster** – används
  konsekvent i både `displayLogin()` och `login()`. Värd att peka på om
  liknande redirect-hantering behövs i andra moduler framöver.
- Ingen `strict_types` eller parametertyper i `logout()`, `displayLogin()`,
  `displayLogout()`, `displayWithHtml()`, `displayHtmlHeader()`,
  `displayHtmlFooter()` (endast `login()` har typad referensparameter,
  `isSafeReturnUrl()` är helt typad).
