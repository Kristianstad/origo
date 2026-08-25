## Include-mönster

Samtliga huvudfiler (entry points) i `adm/` följer samma inledande mönster:

```php
require_once("./functions/includeDirectory.php");
includeDirectory("./functions/common");
includeDirectory("./functions/<modulnamn>");
```

`includeDirectory()` (definierad i `adm/functions/includeDirectory.php`)
laddar **samtliga** `.php`-filer i en given mapp med `require_once`.
Det betyder:

- Alla ~25 filer i `functions/common/` laddas alltid, oavsett vilken
  modul som körs — även om modulen bara använder ett fåtal av dem.
- Det finns inget sätt att se i en modul-fil exakt vilka common-funktioner
  som är tillgängliga utan att läsa `functions/common/`-mappens innehåll.
- Vid dokumentation av en modul listar vi bara de common-funktioner som
  modulen *faktiskt anropar* (inte alla som är tillgängliga) — se
  `common.md` för fullständig referens över alla funktioner i mappen.

**Konsekvens för refaktorering:** eftersom allt i `common/` alltid laddas
ihop finns ingen risk att "glömma ett include" om vi flyttar en funktion
mellan filer i `common/` — men om vi bryter ut delar av `common/` till en
egen undermapp måste vi uppdatera `includeDirectory()`-anropet i *varje*
entry point-fil som behöver den nya mappen.

## Konstanter kontra common-funktioner – laddningssätt

Till skillnad från `functions/common/`, som laddas i sin helhet via
`includeDirectory()` (se ovan), laddas filer i `adm/constants/` **individuellt**
med explicit `require`/`require_once` där just den konstanten behövs, t.ex.:

```php
require("./constants/configSchema.php");
```

Det betyder att en konstant **inte** är automatiskt tillgänglig bara för att
den finns i `constants/`-mappen — varje modul måste själv inkludera de
konstantfiler den behöver. Vid dokumentation av en modul listar vi därför
bara de konstanter som modulen faktiskt `require`:ar, inte alla som finns
i mappen (se `constants.md` för fullständig, växande referens).

## JS-filer

Vissa moduler har klientlogik i `adm/js-functions/<modul>/`, laddad
inline i en `<script>`-tagg via samma `includeDirectory()`-funktion som
används för PHP (se ovan) – d.v.s. alla `.js`-filer i mappen klistras in
i sidans HTML vid varje sidladdning, inte som separata `<script src="...">`-
taggar. JS-funktioner dokumenteras i respektive modul-`.md` tillsammans
med PHP-delen, i ett eget avsnitt "JS-filer och funktioner", eftersom de
utgör samma funktionella helhet (PHP renderar, JS hanterar interaktion).

## Två parallella autentiseringssystem

Applikationen har **två separata, icke sammankopplade sätt att autentisera
användare**, där ett är på väg att fasas ut:

1. **`authorization.php`** – LDAP-baserad inloggning (äldre spår, planerat
   att fasas ut men lämnas kvar som fallback-alternativ tills vidare).
   Tänkt att visas som sida/iframe som användaren interagerar med direkt.
   Sätter `$_SESSION['user']['id']` (enkel struktur) samt en egen krypterad
   cookie. Se `authorization.md`.

2. **`forwardauth.php` / `azure-callback.php`** – Azure AD/Entra ID via
   OAuth2 (nytt, avsett spår framöver), anropad av Traefik (reverse proxy)
   som en "får requesten fortsätta?"-kontroll innan trafiken ens når
   applikationen. Sätter `$_SESSION['user']` med en rikare struktur
   (`mail`, `name`, `groups`, `expires_at` för sliding expiration). Se
   `forwardauth.md`.

**Status:** de två systemen används **inte samtidigt** – det är antingen
LDAP eller Azure/forwardauth som gäller för en given installation/miljö,
styrt av `$authMethod`. Azure/forwardauth är den långsiktiga riktningen;
LDAP-spåret finns kvar som alternativ för den som föredrar det, men är
inte under aktiv vidareutveckling. Detta är relevant vid förenkling: kod
i LDAP-spåret bör inte tas bort, men kan prioriteras lägre än
Azure-spåret vid framtida arbete.

**Konsekvens för `restrictedLayer`-modulen:** den modulen kontrollerar
`$_SESSION['user']['groups']` och `$_SESSION['user']['id']` utan att bry
sig om vilket autentiseringsspår som satte dem – den fungerar därför med
båda, förutsatt att sessionsstrukturen är kompatibel (vilket den är,
se `userAuthorized.php`).
