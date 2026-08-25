# Gemensamma funktioner (common)

**Plats:** `adm/functions/common/`
**Laddas av:** samtliga huvudfiler i `adm/`, via `includeDirectory()` (se ARKITEKTUR.md)

> Denna fil fylls på allteftersom fler moduler dokumenteras. Endast
> funktioner som faktiskt observerats användas listas — se mappen för
> fullständig lista över vad som finns.

| Funktion | Fil | Beskrivning | Används hittills av |
|---|---|---|---|
| `readAndCloseSession()` | `readAndCloseSession.php` | Läser in `$_SESSION` och stänger sessionen (troligen för att undvika sessionslås mellan requests) | news |
| `dbh()` | `dbh.php` | Öppnar och returnerar en PostgreSQL-anslutning | news, mapstate |
| `initUserLdap($dbh)` | `initUserLdap.php` | Initierar användarinfo via LDAP, används när `$authMethod === 'ldap'` | news |
| `pgArrayToPhp($pgArray)` | `pgArrayToPhp.php` | Konverterar Postgres arraysyntax (`{a,b,c}`) till PHP-array | news |
| `includeDirectory($path)` | *(i adm/functions/, ej i common/)* | Laddar alla `.php`-filer i angiven mapp med `require_once` | alla moduler |
