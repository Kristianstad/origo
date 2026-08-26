# Gemensamma funktioner (common)

**Plats:** `adm/functions/common/`
**Laddas av:** samtliga huvudfiler i `adm/`, via `includeDirectory()` (se ARKITEKTUR.md)

> Denna fil fylls på allteftersom fler moduler dokumenteras. Endast
> funktioner som faktiskt observerats användas listas — se mappen för
> fullständig lista över vad som finns.

| Funktion | Fil | Beskrivning | Används av |
|---|---|---|---|
| `readAndCloseSession()` | `readAndCloseSession.php` | Läser in `$_SESSION` och stänger sessionen | news, export |
| `dbh($connectionString=null)` | `dbh.php` | Öppnar PostgreSQL-anslutning. Utan argument: standarddatabasen. Med anslutningssträng: godtycklig extern databas | news, mapstate, info, authorization, read_db_schemas, export, writeConfig, manage, read_schema_tables, updated, help, writeTablesForAllLayers |
| `initUserLdap($dbh)` | `initUserLdap.php` | Initierar användarinfo via LDAP (`$authMethod==='ldap'`). **Bieffekt:** skriver och stänger sessionen | news, authorization, export, restrictedLayer |
| `pgArrayToPhp($pgArray)` | `pgArrayToPhp.php` | Konverterar Postgres arraysyntax (`{a,b,c}`) till PHP-array | news, export, writeConfig |
| `toSwedish($string)` | `toSwedish.php` | Översätter interna typ-/kolumnnamn till svenska för visning | info, multiselect, manage |
| `all_from_table($dbh, $schema, $table)` | `all_from_table.php` | Hämtar alla rader från angiven tabell | info, multiselect (⚠️ hårdkodar schema `map_configs`), read_db_schemas, export, writeTablesForAllLayers |
| `array_column_search($value, $column, $rows)` | `array_column_search.php` | Hittar första raden där given kolumn matchar värdet | info, read_db_schemas, export, writeConfig, manage, writeTablesForAllLayers |
| `pkColumnOfTable($table)` | `pkColumnOfTable.php` | Returnerar primärnyckelns kolumnnamn för en tabell | info, manage, writeTablesForAllLayers |
| `findAllParents($dbh, $child)` | `findAllParents.php` | Rekursivt: alla objekt som refererar till ett givet objekt | info, manage |
| `findParents($tableToRemoveFrom, $target)` | `findParents.php` | Icke-rekursiv variant: hittar direkta föräldrar | manage (printRemoveOperation) |
| `assoc_array_values($array)` | `assoc_array_values.php` | Kontrollerar/hämtar faktiska värden i nästlad associativ array | info, manage |
| `ensureSessionWritable()` | `ensureSessionWritable.php` | Säkerställer att sessionen är öppen/skrivbar | authorization, forwardauth |
| `getCookieOptions($expiryTimestamp)` | `getCookieOptions.php` | Bygger cookie-inställningar (path/domain/secure/httponly/samesite) | authorization |
| `tableNamesFromSchema($dbh, $schema)` | `tableNamesFromSchema.php` | Listar tabellnamn i ett databasschema | read_schema_tables |
| `tablesFromQgsXml($qgsXml, $layerName)` | `tablesFromQgsXml.php` | Tolkar QGIS-projekt-XML för att hitta databastabeller ett lager bygger på | writeTablesForAllLayers, manage |
| `configTables($dbh)` | `configTables.php` | Hämtar samtliga konfigtabeller i ett svep, avsedd att packas upp med `extract()` | writeConfig, manage, read_json |
| `defineFileConstant($name, $value)` | `defineFileConstant.php` | Skriver en PHP-konstant till fil, läsbar via `includeFileConstant()`. Källan till `RESTRICTEDLAYERS`-konstanten | writeConfig |
| `includeFileConstant($name)` | `includeFileConstant.php` | Läser en fil-konstant skriven av `defineFileConstant()` | restrictedLayer |
| `isTarget($target)` | `isTarget.php` | Kontrollerar om en variabel har formen av ett giltigt "target" (se manage.md) | manage |
| `targetType($target)` | `targetType.php` | Returnerar typen (nyckeln) för ett target | manage |
| `makeTargetBasic($target)` | `makeTargetBasic.php` | Konverterar en full target till en basic target | manage (används flitigt i samtliga print*Form) |
| `updated_from_table($dbh, $tableWithSchema)` | `updated_from_table.php` | Enklare variant av `updated_from_table2()` (updated-modulen); används av manage för att visa senaste ändringsdatum för en tabell | manage |
| `includeDirectory($path)` | *(i `adm/functions/`, ej i `common/`)* | Laddar alla `.php`-filer i angiven mapp med `require_once` | samtliga moduler |

## Funktioner sedda men ej fullständigt beskrivna
Namn bekräftade via användning i manage.php, fullständig beskrivning
väntar tills respektive fil granskats: `targetConfig()` (används av
`makeTargetFull()`).
