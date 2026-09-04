# Forward-auth-modul (forwardauth)

**Entry points:** `adm/forwardauth.php`, `adm/azure-callback.php`
**Funktionsfiler:** `adm/functions/forwardauth/*.php`

## Syfte
Implementerar en Traefik ForwardAuth-integration: en reverse proxy (Traefik)
skickar varje inkommande request till `forwardauth.php` för att avgöra om
den ska släppas igenom. Autentisering sker mot Azure AD/Entra ID via
OAuth2 (biblioteket `thenetworg/oauth2-azure` ovanpå `league/oauth2-client`).

Detta är alltså ett **annat autentiseringsspår än `authorization.php`**
(som använder LDAP direkt mot lokal AD). Bägge sätter `$_SESSION['user']`,
men med olika struktur och olika käll-system – se skillnader nedan.

Stödjer valfri **gruppbaserad åtkomstkontroll**: en skyddad resurs kan i
Traefik-konfigurationen ange `?required_group=Grupp1,Grupp2`, och
requesten släpps bara igenom om användaren är medlem i minst en av de
angivna Azure AD-grupperna.

## Anropas med

**`forwardauth.php`** (av Traefik, en gång per skyddad request):

| Parameter | Beskrivning |
|---|---|
| `required_group` | Kommaseparerad lista med Azure AD-gruppnamn. Användaren måste tillhöra minst en (OR-logik). Utelämnas för att bara kräva inloggning |
| `return_to` | Vart användaren ska skickas efter lyckad Azure-inloggning. Sätts normalt automatiskt från Traefiks forwarded-headers om ej angiven |

Svar: `200` (släpp igenom, inkl. header `X-Auth-TTL` med sekunder kvar av
sessionen) · `403` (inloggad men fel grupp) · `302` (ej inloggad, redirect
till Azure).

**`azure-callback.php`** (anropas av Azure efter genomförd inloggning,
med `code` och `state` som Azure sätter):

| Parameter | Beskrivning |
|---|---|
| `code` | Auktoriseringskod från Azure, växlas mot access token |
| `state` | Måste matcha `$_SESSION['oauth2state']` (CSRF-skydd) |
| `error` / `error_description` | Sätts av Azure vid misslyckad inloggning |

## Session-struktur
Vid lyckad inloggning sätts:

```php
$_SESSION['user'] = [
    'id'               => string,   // lowercased on-premises SAM account name, eller Azure-id som fallback
    'mail'             => string,
    'name'             => string,
    'groups'           => string[], // Azure AD-gruppnamn (displayName)
    'authenticated_at' => int,      // unix timestamp
    'expires_at'       => int,      // unix timestamp, sliding expiration i forwardauth.php
];
```

**Sliding expiration:** varje lyckad kontroll i `forwardauth.php` kan
förlänga `expires_at` med `forwardauthSessionConfig['slideExtension']`,
upp till ett absolut tak `forwardauthSessionConfig['absoluteMax']` räknat
från ursprunglig inloggning.

## Beror på
**Common-funktioner** (`adm/functions/common/`):
- `ensureSessionWritable()` – säkerställer skrivbar session

**Konstanter:**
- `constants/forwardauthSessionConfig.php` → `$forwardauthSessionConfig`
  (`slideExtension`, `absoluteMax`, `baseLifetime`)
- `constants/azureConfig.php` → `$azureConfig` (`clientId`, `clientSecret`,
  `redirectUri`, `tenant`, `scopes`), läses inuti `getAzureProvider()`

**Externa bibliotek** (composer, utanför `adm/`):
- `thenetworg/oauth2-azure` (`../../composer/oauth2-azure/autoload.php`)
- `league/oauth2-client` (`../../composer/oauth2-client/autoload.php`)

**Externt API:** Microsoft Graph (`graph.microsoft.com`), anropas direkt
med `curl` (inte via OAuth2-biblioteket) för att hämta gruppmedlemskap och
on-premises-användarnamn.

**Reverse proxy:** förutsätter att Traefik (eller motsvarande) är
konfigurerad att anropa `forwardauth.php` som auth-endpoint och vidarebefordra
`X-Forwarded-*`/`X-Original-*`-headers (`Host`, `Uri`, `Proto`, `Method`)
för att `forwardauth.php` ska kunna återskapa ursprunglig URL.

## Filer och funktioner

*Sorterad alfabetiskt efter filnamn.*

| Fil | Funktion | Beskrivning |
|---|---|---|
| `getAzureAuthUrl.php` | `getAzureAuthUrl()` | Bygger Azure-inloggnings-URL och sparar `oauth2state` i sessionen (CSRF-skydd) |
| `getAzureGroups.php` | `getAzureGroups($graphToken): array` | Hämtar (paginerat) användarens Azure AD-gruppmedlemskap via Microsoft Graph |
| `getAzureProvider.php` | `getAzureProvider()` | Skapar en konfigurerad OAuth2-klient (`TheNetworg\OAuth2\Client\Provider\Azure`), tvingar Microsoft Graph som API-mål |
| `getGraphToken.php` | `getGraphToken($token)` | Växlar ett access-token mot ett Graph-specifikt token via refresh_token-flödet. **Verkar för närvarande oanvänd** – se flaggning nedan |
| `getOnPremisesSamAccountName.php` | `getOnPremisesSamAccountName($graphToken)` | Hämtar användarens lokala AD-kontonamn (`onPremisesSamAccountName`) via Microsoft Graph |
| `isSafeReturnTo.php` | `isSafeReturnTo(string $url): bool` | Validerar att en return-URL:s host slutar på `kristianstad.se`. **Hårdkodad domän** – se flaggning nedan |

## Kända begränsningar / observationer (ej åtgärdat ännu)

- **⚠️ Hårdkodad organisationsdomän** i `isSafeReturnTo.php`
  (`kristianstad.se`) och som fallback-URL i `azure-callback.php`
  (`https://kartor.kristianstad.se`). Det här avslöjar för första gången
  vilken organisation systemet tillhör – i sig ofarligt, men **bör flyttas
  till en konstant** (t.ex. `constants/allowedReturnDomain.php`) både för
  konsekvens med resten av kodbasen och för att göra koden
  miljöoberoende (test/demo-miljöer med annan domän).
- **⚠️ Två olika funktioner med snarlika namn och syfte men olika logik:**
  `isSafeReturnUrl()` (authorization-modulen, matchar mot `HTTP_HOST`) och
  `isSafeReturnTo()` (denna modul, matchar mot hårdkodad `kristianstad.se`).
  Namnlikheten (`ReturnUrl` vs `ReturnTo`) gör det lätt att förväxla dem
  vid framtida ändringar. Kandidat att antingen slå ihop till en
  gemensam, konfigurerbar common-funktion, eller döpa om tydligare för
  att markera att de har olika skyddsnivå/syfte.
- **`getGraphToken.php` verkar vara död kod:** i `azure-callback.php` är
  anropen till `getGraphToken($token)` utkommenterade
  (`//$graphToken = getGraphToken($token);`) till förmån för att skicka
  `$token` direkt till `getAzureGroups()`/`getOnPremisesSamAccountName()`.
  Funktionen `getGraphToken()` verkar därmed oanvänd i nuvarande flöde –
  kandidat för borttagning, om inte den behövs någon annanstans vi inte
  sett än.
- **Missvisande kommentar i `getOnPremisesSamAccountName.php`:** filens
  docblock säger `Hämtar grupper med Microsoft Graph token` (kopierad
  från `getAzureGroups.php`) trots att funktionen hämtar
  `onPremisesSamAccountName`, inte grupper. Enkel copy-paste-bugg i
  kommentaren, bör rättas vid nästa redigering av filen.
- **`getAzureGroups()` hanterar paginering korrekt** (`@odata.nextLink`)
  – bra praxis värd att notera som förebild om andra Graph-anrop
  tillkommer.
- **Utökad felsökningslogik finns kvar men avstängd** (utkommenterade
  `error_log(sprintf(...))`-block överst i båda filerna) – ofarligt, men
  värt att städa bort eller flytta bakom en debug-flagga vid
  refaktorering, snarare än att lämnas som utkommenterad kod.
- **Ingen typad `strict_types`**, och flera funktioner saknar
  returtypdeklaration (`getAzureProvider()`, `getGraphToken()`,
  `getOnPremisesSamAccountName()`) trots att de har tydliga, enhetliga
  returtyper i praktiken.
- **`forwardauth.php` litar på `X-Forwarded-*`/`X-Original-*`-headers**
  för att återskapa ursprunglig URL. Detta är korrekt *förutsatt* att
  Traefik är konfigurerad att sätta dessa headers pålitligt och att
  applikationen aldrig nås direkt förbi proxyn (annars kan headers
  förfalskas av klienten). Värt att bekräfta som en infrastrukturell
  förutsättning snarare än en kodbugg.
