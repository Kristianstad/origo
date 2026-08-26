# Översikt – adm/ (Origo-adminverktyg)

Denna fil är en karta över samtliga moduler i `adm/`. Varje modul har
sin egen `.md`-fil (namn enligt kolumnen "Dokumentation") med fullständig
detaljbeskrivning. Se `ARKITEKTUR.md` för genomgående mönster (include-
system, autentisering, m.m.), `common.md`/`constants.md` för delade
funktioner/konstanter.

## Kärnmoduler (adminfunktionalitet)

| Modul | Entry point(s) | Status | Beskrivning |
|---|---|---|---|
| manage | `manage.php` | 🟡 Pågående (entry point + ~40 av 60+ funktionsfiler klara) | Den centrala CRUD-motorn för all konfiguration: kartor, lager, grupper, källor, tjänster, m.fl. |
| writeConfig | `writeConfig.php` | ✅ Klar | Genererar Origo-JSON + publicerad HTML-sida från databasen. "Kompileringssteget" |
| read_json | `read_json.php.off` (avstängd) | ✅ Klar (låg prioritet) | Motsatsen till writeConfig: importerar Origo-JSON till databasen. Avstängd, sällan använd, känd teknisk skuld |
| news | `news.php` | ✅ Klar | Nyheter/meddelanden för inloggade användare |
| mapstate | `mapstate.php` | ✅ Klar | Stateless JSON-API för att spara/hämta karttillstånd (delbara länkar) |
| info | `info.php` | ✅ Klar | Detaljvy för ett objekt + "Används av"-lista (iframe-popup) |
| multiselect | `multiselect.php` | ✅ Klar | Generisk flervalskomponent (iframe-popup) |
| help | `help.php` | ✅ Klar | Hjälptexter (generella eller fältspecifika), iframe-popup |

## Autentisering (två parallella spår, se ARKITEKTUR.md)

| Modul | Entry point(s) | Status | Beskrivning |
|---|---|---|---|
| authorization | `authorization.php`, `authorization-iframe.php` | ✅ Klar | LDAP-baserad inloggning (äldre spår, underhålls men fasas ut) |
| forwardauth | `forwardauth.php`, `azure-callback.php` | ✅ Klar | Azure AD/Entra ID via OAuth2 + Traefik ForwardAuth (nytt, avsett spår) |

## Karttjänst-proxyer

| Modul | Entry point(s) | Status | Beskrivning |
|---|---|---|---|
| restrictedLayer | `restrictedLayer.php` | ✅ Klar | Behörighetsstyrd proxy mot karttjänst; degraderar svar för obehöriga |
| grouplayerfix | `grouplayerfix.php` | ✅ Klar | QGIS-specifik proxy; expanderar "grupplager" till underlager. Begränsad användning, ingen behörighetskoppling (annan QGIS-instans än restrictedLayer) |

## Databas-/schemaunderhåll

| Modul | Entry point(s) | Status | Beskrivning |
|---|---|---|---|
| read_db_schemas | `read_db_schemas.php` | ✅ Klar | Synkar scheman från extern databas till konfig-databasen |
| read_schema_tables | `read_schema_tables.php` | ✅ Klar | Synkar tabeller från ett schema till konfig-databasen |
| updated | `updated.php` | ✅ Klar | Slår upp senaste ändringsdatum för databastabeller |
| writeTablesForAllLayers | `writeTablesForAllLayers.php` | ✅ Klar | Batchscript: fyller i vilka tabeller QGIS-lager bygger på |

## Export (organisationsspecifik)

| Modul | Entry point(s) | Status | Beskrivning |
|---|---|---|---|
| export | `export.php` | ✅ Klar | Asynkron export av kartutsnitt via FME Server. **Ej i publikt repo, hårt org-specifik** |

## Ej dokumenterade / lågprioriterade

| Fil | Status | Anteckning |
|---|---|---|
| `export.old.php`, `read_json.php.off.old`, `restrictedLayer-rancher*.php`, `importmeta.php.old`, diverse `_old`/`_old2`-filer i `info/plan/` | ⛔ Avsiktligt hoppade över | Gamla varianter, kandidater för radering |
| `adm/search.php` | ⛔ Tom fil (0 bytes) | Oanvänd/ofärdig |
| `printSearchmodelForm.php`, `printSearchtableForm.php` | 🟡 Sedda men lågprioriterade | Se manage.md – innehåller en trolig bugg (variabelnamn `$searchtable` vs `$searchmodel` förväxlade) |
| Loader-filer (`authorization/`, `export/`, `forwardauth/`, `grouplayerfix/`, `mapstate/`, `news/`, `updated/` på toppnivå) | ⛔ Sparade till egen genomgång | Bekräftat aktivt använda (se authorization.md), inte bara historiska kvarlevor |
| `export/` (toppnivå, med egen `functions/`/`constants/`) | ⛔ Sparad till loader-genomgången | Delvis dubblerad kod jämfört med `adm/functions/export/` – mer än en tunn loader |

## Statistik (ungefärligt, för planering)
- `functions/manage/`: 60+ filer, ~40 granskade hittills
- Övriga moduler: samtliga funktionsfiler granskade
