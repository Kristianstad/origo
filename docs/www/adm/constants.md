# Konstanter (constants)

**Plats:** `adm/constants/` (samt vissa på toppnivå, utanför `adm/`)
**Laddas av:** enskilda `require`/`require_once`-satser där de behövs
(se ARKITEKTUR.md – *inte* automatiskt via `includeDirectory()`).

| Konstant | Fil | Beskrivning | Används av |
|---|---|---|---|
| `$authMethod` | `authMethod.php` | Styr autentiseringsmetod (`'ldap'` eller annat) | news, restrictedLayer, export, authorization, forwardauth (indirekt) |
| `$configSchema` | `configSchema.php` | Postgres-schemanamn för appens tabeller | news, mapstate, info, read_db_schemas, export, manage (flera filer), read_schema_tables |
| `$proxyRoot` | `proxyRoot.php` | Bas-URL-prefix, för korrekta länkar bakom proxy | news, authorization, writeConfig, renderCssTags/renderJavaScriptTags |
| `$mapstateMaxUnused` | `mapstateMaxUnused.php` | Dagar innan oanvänt mapstate städas bort | mapstate |
| `$adldapConfig` | `adldapConfig.php` | LDAP-anslutningsinställningar (adldap2) | authorization |
| `$adDomain` | `adDomain.php` | AD-domännamn för LDAP-autentisering | authorization |
| `$cookieConfig` | `cookieConfig.php` | Cookieinställningar: namn, krypteringsnyckel, livslängd | authorization |
| `$forwardauthSessionConfig` | `forwardauthSessionConfig.php` | Sessionsinställningar för forward-auth: slideExtension, absoluteMax, baseLifetime | forwardauth |
| `$azureConfig` | `azureConfig.php` | Azure AD-appregistrering: clientId, clientSecret, redirectUri, tenant, scopes | forwardauth |
| `$restrictedServiceUrl` | `restrictedServiceUrl.php` | Bas-URL till bakomliggande karttjänst för restrictedLayer | restrictedLayer |
| `RESTRICTEDLAYERS` (fil-konstant) | `RESTRICTEDLAYERS.php` (toppnivå) | Lista över skyddade lager m. behöriga användare/grupper. Genereras av writeConfig | restrictedLayer |
| `$dbhConnectionStringForUpdated` | `dbhConnectionStringForUpdated.php` | Dedikerad anslutningssträng för updated-modulen | updated |
| `$webRoot` | `webRoot.php` | Rotkatalog dit publicerade kartor skrivs | writeConfig |
| `$previewBase` | `previewBase.php` | Bas-URL för förhandsgranskningsläge | writeConfig |
| (flera, geo/publisher) | `searchEngineMeta.php` | Metadata för strukturerad SEO-data (geo-koordinater, publisher-info) | writeConfig |
| `$sourcesQueryColumns` | `sourcesQueryColumns.php` | Vilka kolumner som ska med som query-parametrar för en källas URL | writeConfig (addSourcesToJson) |
| `$arrayColumns` | `arrayColumns.php` | Lista över kolumner som är Postgres-arrayer | manage (isArrayColumn) |
| `$multiselectables` | `multiselectables.php` | Vilka konfigurationsfält som ska ha en multiselect-knapp | manage (printTextarea) |
| `$tableAliases` | `tableAliases.php` | Alias-mappning för tabellnamn använda av multiselect-knappar | manage (printMultiselectButton/2) |
| `$views` | `views.php` | Definierar vilka tabeller/kolumner som visas i respektive vy | manage (printHeadForms) |
| `$keywordCategorized` | `keywordCategorized.php` | Vilka tabeller som ska nyckelordskategoriseras | manage (printHeadForm; jfr `viewKeywordCategorized()`) |

## Konstanter sedda men ej fullständigt beskrivna
`iconTtl.php` → `$iconTtl` (writeConfig, addLayersToJson – TTL för
legend-/ikon-URL:er); `webRoot.php` bekräftad ovan.
