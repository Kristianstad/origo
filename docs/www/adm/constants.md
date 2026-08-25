# Konstanter (constants)

**Plats:** `adm/constants/`
**Laddas av:** enskilda `require`-satser där de behövs (till skillnad från
common/functions laddas INTE alla konstanter automatiskt av `includeDirectory()`)

| Konstant | Fil | Beskrivning | Används hittills av |
|---|---|---|---|
| `$authMethod` | `authMethod.php` | Styr autentiseringsmetod (t.ex. `'ldap'`) | news |
| `$configSchema` | `configSchema.php` | Postgres-schemanamn där appens tabeller ligger | news, mapstate, info |
| `$proxyRoot` | `proxyRoot.php` | Bas-URL-prefix, används för att bygga länkar korrekt bakom proxy | news, authorization |
| `$mapstateMaxUnused` | `mapstateMaxUnused.php` | Antal dagar ett oanvänt mapstate får ligga kvar innan städning | mapstate |
| `$adldapConfig` | `adldapConfig.php` | LDAP-anslutningsinställningar för adldap2-biblioteket | authorization |
| `$adDomain` | `adDomain.php` | AD-domännamn använt vid LDAP-autentisering | authorization |
| `$cookieConfig` | `cookieConfig.php` | Cookieinställningar: namn, krypteringsnyckel, livslängd | authorization |
