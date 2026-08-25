# Gemensamma funktioner (common)

**Plats:** `adm/functions/common/`
**Laddas av:** samtliga huvudfiler i `adm/`, via `includeDirectory()` (se ARKITEKTUR.md)

> Denna fil fylls på allteftersom fler moduler dokumenteras. Endast
> funktioner som faktiskt observerats användas listas — se mappen för
> fullständig lista över vad som finns.

| Funktion | Fil | Beskrivning | Används hittills av |
|---|---|---|---|
| `readAndCloseSession()` | `readAndCloseSession.php` | Läser in `$_SESSION` och stänger sessionen | news |
| `dbh()` | `dbh.php` | Öppnar och returnerar en PostgreSQL-anslutning | news, mapstate, info |
| `initUserLdap($dbh)` | `initUserLdap.php` | Initierar användarinfo via LDAP, används när `$authMethod === 'ldap'` | news |
| `pgArrayToPhp($pgArray)` | `pgArrayToPhp.php` | Konverterar Postgres arraysyntax till PHP-array | news |
| `toSwedish($string)` | `toSwedish.php` | Översätter interna typ-/kolumnnamn till svenska för visning | info |
| `all_from_table($dbh, $schema, $table)` | `all_from_table.php` | Hämtar alla rader från angiven tabell | info |
| `array_column_search($value, $column, $rows)` | `array_column_search.php` | Hittar första raden i en array där given kolumn matchar värdet | info |
| `pkColumnOfTable($table)` | `pkColumnOfTable.php` | Returnerar primärnyckelns kolumnnamn för en tabell | info |
| `findAllParents($dbh, $child)` | `findAllParents.php` | Hittar rekursivt alla objekt som refererar till ett givet objekt | info |
| `assoc_array_values($array)` | `assoc_array_values.php` | Kontrollerar/hämtar faktiska värden i en nästlad associativ array | info |
| `includeDirectory($path)` | *(i adm/functions/, ej i common/)* | Laddar alla `.php`-filer i angiven mapp med `require_once` | alla moduler |
