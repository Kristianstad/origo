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
