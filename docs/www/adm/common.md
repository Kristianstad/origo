# Gemensamma funktioner (common)

**Plats:** `adm/functions/common/`
**Laddas av:** samtliga huvudfiler i `adm/`, via `includeDirectory()` (se ARKITEKTUR.md)

> Denna fil fylls på allteftersom fler moduler dokumenteras. Endast
> funktioner som faktiskt observerats användas listas — se mappen för
> fullständig lista över vad som finns.

| Funktion | Fil | Beskrivning | Används hittills av |
|---|---|---|---|
| `readAndCloseSession()` | `readAndCloseSession.php` | Läser in `$_SESSION` och stänger sessionen | news |
| `dbh($connectionString = null)` | `dbh.php` | Öppnar och returnerar en PostgreSQL-anslutning. Utan argument ansluter den till standard-/konfigurationsdatabasen; med en anslutningssträng som argument kan den ansluta till en **godtycklig extern databas** (se read_db_schemas) | news, mapstate, info, authorization, read_db_schemas |
| `initUserLdap($dbh)` | `initUserLdap.php` | Initierar användarinfo via LDAP, används när `$authMethod === 'ldap'`. **Bieffekt:** skriver och stänger sessionen | news, authorization |
| `pgArrayToPhp($pgArray)` | `pgArrayToPhp.php` | Konverterar Postgres arraysyntax till PHP-array | news, export |
| `toSwedish($string)` | `toSwedish.php` | Översätter interna typ-/kolumnnamn till svenska för visning | info, multiselect, printCopyButton, printDeleteButton, printAddOperation, printRemoveOperation, printHeadForm/Forms, validateUpdate |
| `all_from_table($dbh, $schema, $table)` | `all_from_table.php` | Hämtar alla rader från angiven tabell | info, multiselect, read_db_schemas, export |
| `array_column_search($value, $column, $rows)` | `array_column_search.php` | Hittar första raden i en array där given kolumn matchar värdet | info, read_db_schemas, export |
| `pkColumnOfTable($table)` | `pkColumnOfTable.php` | Returnerar primärnyckelns kolumnnamn för en tabell | info, writeTablesForAllLayers, target-infrastruktur (targetId, targetIdColumn via targetTable), validateUpdate |
| `findAllParents($dbh, $child)` | `findAllParents.php` | Hittar rekursivt alla objekt som refererar till ett givet objekt | info |
| `assoc_array_values($array)` | `assoc_array_values.php` | Kontrollerar/hämtar faktiska värden i en nästlad associativ array | info |
| `includeDirectory($path)` | *(i adm/functions/, ej i common/)* | Laddar alla `.php`-filer i angiven mapp med `require_once` | alla moduler |
| `ensureSessionWritable()` | `ensureSessionWritable.php` | Säkerställer att sessionen är öppen/skrivbar innan skrivning | authorization, forwardauth |
| `getCookieOptions($expiryTimestamp)` | `getCookieOptions.php` | Bygger array med cookie-inställningar (path, domain, secure, httponly, samesite) för ett givet utgångsdatum | authorization |
| `tableNamesFromSchema($dbh, $schema)` | `tableNamesFromSchema.php` | Listar tabellnamn i ett givet databasschema | read_schema_tables |
| `tablesFromQgsXml($qgsXml, $layerName)` | `tablesFromQgsXml.php` | Tolkar en QGIS-projektfils XML för att hitta databastabeller ett lager bygger på | writeTablesForAllLayers |
| `configTables($dbh)` | `configTables.php` | Hämtar samtliga konfigurationstabeller (maps, groups, layers, m.fl.) i ett svep, avsedd att packas upp med `extract()` | writeConfig |
| `defineFileConstant($name, $value)` | `defineFileConstant.php` | Skriver en PHP-konstant till en fil på disk, läsbar senare via `includeFileConstant()`. Källan till `RESTRICTEDLAYERS`-konstanten | writeConfig |
| `isTarget($target)` | `isTarget.php` | Kontrollerar om en variabel har formen av ett giltigt "target" (se target-konceptet i manage.md) | manage |
