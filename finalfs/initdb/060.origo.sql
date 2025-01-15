CREATE SCHEMA map_configs;

CREATE TABLE map_configs.controls
(
    control_id character varying COLLATE pg_catalog."default" NOT NULL,
    options json,
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT controls_pkey PRIMARY KEY (control_id)
);

INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('home#1','{ "zoomOnStart": true }','Ställer in kartans utbredning till den som angivits i alternativen för kontrollen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('mapmenu#1','{ "isActive": false }','Skapar en meny uppe till höger för kontroller.');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('sharemap#1','Skapar en delbar länk till kartan. Aktuell utbredning och zoom, synliga lager och kartnålen (om tillämpligt) kommer att delas. Om ett objekt på kartan väljs, kommer objektets ID att finnas i länken som gör att kartan zoomar in på den när den laddas. Detta gäller för WFS, Geojson, Topojson och AGS Feature lager. Sharemap-kontrollen kommer också med möjlighet att spara karttillstånd på servern (kräver Origo Server). Ett sparat karttillstånd hämtas med ett ID istället för en URL.');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('geoposition#1','Lägger till en knapp som när du klickar på den centrerar och zoomar kartan till den aktuella positionen. Genom att klicka på knappen en andra gång aktiveras spårningsläget (om enableTracking har satts till true).');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('print#1','Lägger till en utskriftskontroll.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('about#1','{ "buttonText": "Om Origo", "title": "Om Origo", "content": "<p>Origo är ett ramverk för webbkartor. Ramverket bygger på JavaScript-biblioteket OpenLayers. Du kan använda Origo för att skapa egna webbaserade kartapplikationer.</p><br><p>Projektet drivs och underhålls av ett antal svenska kommuner. Besök gärna <a href=\"https://github.com/origo-map/origo\" target=\"_blank\">Origo på GitHub</a> för mer information.</p>" }','Lägger till en om-kartkontroll. En knapp läggs till i menyn. När du klickar på knappen kommer ett popup-fönster att visa allmän information om kartan. OBS - kräver mapmenu-kontrollen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('link#1','{ "title": "Origo", "url": "https://github.com/origo-map/origo" }','Lägger till en knapp på kartmenyn som när den klickas öppnar en ny webbläsarflik med den angivna webbadressen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('legend#1','{ "labelOpacitySlider": "Opacity", "useGroupIndication" : true }','Lägger till en legend i menyn och som en kartförklaring till kartan.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('position#1','{ "title": "Web Mercator", "projections": { "EPSG:4326": "WGS84", "EPSG:3006": "Sweref99 TM" } }','Kontroll för att visa koordinater. Musens position och mittposition på kartan kan växlas. Koordinater kan sökas på i mittpositionsläget.');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('measure#1','Lägger till en mätningskontroll. Mät längd, area eller höjd (kräver tillgång till extern höjddatawebbtjänst) i kartan.');

CREATE TABLE map_configs.footers
(
    footer_id character varying COLLATE pg_catalog."default" NOT NULL,
    img character varying COLLATE pg_catalog."default",
    url character varying COLLATE pg_catalog."default",
    text character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT footers_pkey PRIMARY KEY (footer_id)
);

INSERT INTO map_configs.footers(footer_id,img,url,text,abstract) VALUES ('origo#1','img/png/logo.png','https://github.com/origo-map/origo','Origo','En sidfot som vid klick öppnar Origoprojektets Github-sida i en ny flik.');

CREATE TABLE map_configs.groups
(
    group_id character varying COLLATE pg_catalog."default" NOT NULL,
    title character varying COLLATE pg_catalog."default",
    expanded boolean NOT NULL DEFAULT false,
    abstract character varying COLLATE pg_catalog."default",
    groups character varying[] COLLATE pg_catalog."default",
    layers character varying[] COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    show_meta boolean,
    keywords character varying[] COLLATE pg_catalog."default",
    CONSTRAINT groups_pkey PRIMARY KEY (group_id)
);

INSERT INTO map_configs.groups(group_id,title,expanded,layers,abstract) VALUES ('background#1','Bakgrundskartor',true,'{osm#1}','Grupp som innehåller alla bakgrundslager.');
INSERT INTO map_configs.groups(group_id,layers,abstract) VALUES ('none#1','{origo-mask#1}','Grupp som inte visas i lagerträdet.');

CREATE TABLE map_configs.layers
(
    layer_id character varying COLLATE pg_catalog."default" NOT NULL,
    title character varying COLLATE pg_catalog."default",
    source character varying COLLATE pg_catalog."default",
    style_layer character varying COLLATE pg_catalog."default",
    type character varying COLLATE pg_catalog."default" DEFAULT 'WMS'::character varying,
    queryable boolean DEFAULT true,
    legend boolean,
    visible boolean DEFAULT false,
    attributes json,
    icon character varying COLLATE pg_catalog."default",
    icon_extended character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    style_filter character varying COLLATE pg_catalog."default",
    style_config json,
    editable boolean,
    gutter integer,
    tiled boolean DEFAULT true,
    opacity numeric(3,2) NOT NULL DEFAULT 1,
    featureinfolayer character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    format character varying COLLATE pg_catalog."default" DEFAULT 'image/png'::character varying,
    adusers character varying[] COLLATE pg_catalog."default",
    adgroups character varying[] COLLATE pg_catalog."default",
    layers character varying[] COLLATE pg_catalog."default",
    layertype character varying COLLATE pg_catalog."default",
    clusteroptions json,
    maxscale integer,
    minscale integer,
    clusterstyle json,
    attribution character varying COLLATE pg_catalog."default",
    swiper character varying COLLATE pg_catalog."default",
    allowededitoperations character varying COLLATE pg_catalog."default",
    geometryname character varying COLLATE pg_catalog."default",
    geometrytype character varying COLLATE pg_catalog."default",
    exports character varying[] COLLATE pg_catalog."default",
    resources character varying COLLATE pg_catalog."default",
    contact character varying COLLATE pg_catalog."default",
    updated date,
    web character varying COLLATE pg_catalog."default",
    update character varying COLLATE pg_catalog."default",
    origin character varying COLLATE pg_catalog."default",
    tables character varying[] COLLATE pg_catalog."default",
    history character varying COLLATE pg_catalog."default",
    show_meta boolean,
    show_icon boolean DEFAULT false,
    show_iconext boolean,
    keywords character varying[] COLLATE pg_catalog."default",
    drawtools json,
    featurelistattributes character varying COLLATE pg_catalog."default",
    indexweight integer,
    exportable boolean DEFAULT true,
    CONSTRAINT layers_pkey PRIMARY KEY (layer_id)
);

INSERT INTO map_configs.layers(layer_id,title,type,attributes,visible,style_config,source,abstract,show_meta,origin) VALUES ('origo-cities#1','Origokommuner','GEOJSON','[ { "name": "name" } ]',true,'[ [ { "label": "Origokommuner", "circle": { "radius": 10, "stroke": { "color": "rgba(0,0,0,1)", "width": 2.5 }, "fill": { "color": "rgba(255,255,255,0.9)" } } }, { "circle": { "radius": 2.5, "stroke": { "color": "rgba(0,0,0,0)", "width": 1 }, "fill": { "color": "rgba(37,129,196,1)" } } } ] ]','data/origo-cities-3857.geojson','Lager som visar kommuner delaktiga i Origoprojektet.',true,'origo');
INSERT INTO map_configs.layers(layer_id,title,type,visible,style_config,source,queryable,opacity,abstract,show_meta) VALUES ('origo-mask#1','Origo-mask','GEOJSON',true,'[ [ { "stroke": { "color": "rgba(0,0,0,1.0)" }, "fill": { "color": "rgba(0,0,0,1.0)" } } ] ]','data/origo-mask-3857.geojson',false,0.25,'Lager som tonar ner de delar av kartan som inte utgör del av en Origokommun.',false);
INSERT INTO map_configs.layers(layer_id,title,type,visible,show_icon,icon,queryable,abstract,show_meta,origin) VALUES ('osm#1','OpenStreetMap','OSM',true,true,'img/png/osm.png',false,'Bakgrundslager från OpenStreetMap.',true,'osm');

CREATE TABLE map_configs.maps
(
    map_id character varying COLLATE pg_catalog."default" NOT NULL,
    controls character varying[] COLLATE pg_catalog."default" NOT NULL DEFAULT '{home#1,mapmenu#1,sharemap#1,geoposition#1,print#1,about#1,link#1,legend#1,position#1,measure#1}'::character varying[],
    mapgrid boolean NOT NULL DEFAULT true,
    projectioncode character varying COLLATE pg_catalog."default" NOT NULL DEFAULT 'EPSG:3857'::character varying,
    projectionextent box NOT NULL DEFAULT '(-20026376.39,-20048966.10),(20026376.39,20048966.10)'::box,
    extent box NOT NULL DEFAULT '(-20026376.39,-20048966.10),(20026376.39,20048966.10)'::box,
    center point NOT NULL DEFAULT '(1770000,8770000)'::point,
    zoom integer NOT NULL DEFAULT 7,
    enablerotation boolean NOT NULL DEFAULT true,
    constrainresolution boolean NOT NULL DEFAULT true,
    resolutions numeric[] NOT NULL DEFAULT '{156543.03,78271.52,39135.76,19567.88,9783.94,4891.97,2445.98,1222.99,611.50,305.75,152.87,76.437,38.219,19.109,9.5546,4.7773,2.3887,1.1943,0.5972}'::numeric[],
    proj4defs character varying[] COLLATE pg_catalog."default" DEFAULT '{EPSG:3006}'::character varying[],
    featureinfooptions json DEFAULT '{ "infowindow": "overlay" }'::json,
    groups character varying[] COLLATE pg_catalog."default",
    layers character varying[] COLLATE pg_catalog."default",
    footer character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    tilegrid character varying COLLATE pg_catalog."default",
    show_meta boolean,
    embedded boolean,
    abstract character varying COLLATE pg_catalog."default",
    keywords character varying[] COLLATE pg_catalog."default",
    css character varying COLLATE pg_catalog."default",
    title character varying COLLATE pg_catalog."default",
    icon character varying COLLATE pg_catalog."default" DEFAULT '../img/png/logo.png'::character varying,
    css_files character varying[] COLLATE pg_catalog."default" DEFAULT '{css/style.css}'::character varying[],
    js character varying COLLATE pg_catalog."default",
    js_files character varying[] COLLATE pg_catalog."default" DEFAULT '{js/origo.min.js}'::character varying[],
    url character varying COLLATE pg_catalog."default",
    palette json,
    CONSTRAINT map_pk PRIMARY KEY (map_id)
);

INSERT INTO map_configs.maps(map_id,footer,layers,groups,abstract,show_meta) VALUES ('origo-cities#1','origo#1','{origo-cities#1}','{none#1,background#1}','En demokarta som visar kommuner delaktiga i Origoprojektet.',true);
INSERT INTO map_configs.maps(map_id,footer,groups,abstract,show_meta) VALUES ('preview','origo#1','{background#1}','En karta som används för att visa förhandsgranskningar i administrationsverktyget.',true);

CREATE TABLE map_configs.proj4defs
(
    code character varying COLLATE pg_catalog."default" NOT NULL,
    projection character varying COLLATE pg_catalog."default",
    alias character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT proj4defs_pkey PRIMARY KEY (code)
);

INSERT INTO map_configs.proj4defs(code,projection) VALUES ('EPSG:3006','+proj=utm +zone=33 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs');

CREATE TABLE map_configs.services
(
    service_id character varying COLLATE pg_catalog."default" NOT NULL,
    base_url character varying COLLATE pg_catalog."default",
    alias character varying COLLATE pg_catalog."default",
    type character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT services_pkey PRIMARY KEY (service_id)
);

CREATE TABLE map_configs.sources
(
    source_id character varying COLLATE pg_catalog."default" NOT NULL,
    service character varying COLLATE pg_catalog."default",
    with_geometry boolean,
    fi_point_tolerance integer,
    ttl integer,
    info character varying COLLATE pg_catalog."default",
    tilegrid character varying COLLATE pg_catalog."default",
    contact character varying COLLATE pg_catalog."default",
    updated date,
    tables character varying[] COLLATE pg_catalog."default",
    history character varying COLLATE pg_catalog."default",
    softversion character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT sources_pkey PRIMARY KEY (source_id)
);

CREATE TABLE map_configs.tilegrids
(
    tilegrid_id character varying COLLATE pg_catalog."default" NOT NULL,
    alignbottomleft boolean,
    extent box,
    minzoom integer,
    resolutions numeric[],
    tilesize integer,
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT tilegrids_pkey PRIMARY KEY (tilegrid_id)
);

CREATE TABLE map_configs.contacts
(
    contact_id character varying COLLATE pg_catalog."default" NOT NULL,
    name character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    email character varying COLLATE pg_catalog."default",
    web character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT contacts_pkey PRIMARY KEY (contact_id)
);

CREATE TABLE map_configs.exports
(
    export_id character varying COLLATE pg_catalog."default" NOT NULL,
    resource character varying COLLATE pg_catalog."default",
    style character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT exports_pkey PRIMARY KEY (export_id)
);

CREATE TABLE map_configs.origins
(
    origin_id character varying COLLATE pg_catalog."default" NOT NULL,
    name character varying COLLATE pg_catalog."default",
    email character varying COLLATE pg_catalog."default",
    web character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT origins_pkey PRIMARY KEY (origin_id)
);

INSERT INTO map_configs.origins(origin_id,name,web,abstract) VALUES ('origo','Origo','https://github.com/origo-map/origo','Origo är ett ramverk för webbkartor. Ramverket bygger på JavaScript-biblioteket OpenLayers. Du kan använda Origo för att skapa egna webbaserade kartapplikationer. Projektet drivs och underhålls av ett antal svenska kommuner.');
INSERT INTO map_configs.origins(origin_id,name,web,abstract) VALUES ('osm','OpenStreetMap','https://www.openstreetmap.org/','OpenStreetMap är en karta över världen, skapad av människor som du och fri att använda under en öppen licens.');

CREATE TABLE map_configs.updates
(
    update_id character varying COLLATE pg_catalog."default" NOT NULL,
    "interval" character varying COLLATE pg_catalog."default",
    method character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    name character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT updates_pkey PRIMARY KEY (update_id)
);

CREATE TABLE map_configs.databases
(
    database_id character varying COLLATE pg_catalog."default" NOT NULL,
    connectionstring character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT database_pkey PRIMARY KEY (database_id)
);

CREATE TABLE map_configs.schemas
(
    schema_id character varying COLLATE pg_catalog."default" NOT NULL,
    info character varying COLLATE pg_catalog."default",
    contact character varying COLLATE pg_catalog."default",
    updated date,
    update character varying COLLATE pg_catalog."default",
    origin character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    keywords character varying[] COLLATE pg_catalog."default",
    CONSTRAINT schemas_pkey PRIMARY KEY (schema_id)
);

CREATE TABLE map_configs.tables
(
    table_id character varying COLLATE pg_catalog."default" NOT NULL,
    info character varying COLLATE pg_catalog."default",
    contact character varying COLLATE pg_catalog."default",
    updated date,
    update character varying COLLATE pg_catalog."default",
    origin character varying COLLATE pg_catalog."default",
    history character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    keywords character varying[] COLLATE pg_catalog."default",
    CONSTRAINT tables_pkey PRIMARY KEY (table_id)
);

CREATE TABLE map_configs.keywords
(
    keyword_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    CONSTRAINT keywords_pkey PRIMARY KEY (keyword_id)
);

CREATE TABLE map_configs.helps
(
    help_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    CONSTRAINT helps_pkey PRIMARY KEY (help_id)
);

INSERT INTO map_configs.helps(help_id,abstract) VALUES ('help:help_id','<b>Hjälp > Verktygsfält</b><br>Det fulla namnet för det verktygsfält som ska tilldelas en hjälptext (för muspekaren över namnet på ett verktygsfält för att få upp det fulla namnet).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('help:abstract','<b>Hjälp > Hjälptext</b><br>Meningsfull hjälptext (html) som visas när användaren klickar på<button class="smallHelpButton">?</button> till höger om det aktuella verktygsfältet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:indexweight','<html lang="sv-SE"><head><style>table{line-height:1;float:left}td{min-width:3em;text-align:left;vertical-align:top}.p-indent{padding-left:2em}</style></head><body><b>Lager > Indexvikt</b><br>Indexvikt kan sättas på lager och anges som ett positivt eller negativt heltalsvärde. Värdet påverkar ritordningen och ibland lagerträdet.<table cellspacing="0" cellpadding="0"><tbody><tr><td colspan="3"><h3>Inga indexvikter satta</h3></td><td></td><td colspan="3"><h3>Lager3=1 <em>eller</em> Lager2=-1</h3></td><td></td><td colspan="3"><h3>Lager4=2</h3></td><td></td><td colspan="3"><h3>Lager3=2 <em>och</em> Lager4=2</h3></td></tr><tr><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td></tr><tr><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p>Lager1</p><p>Lager2</p><p>Lager3</p><p>Lager4</p><p></p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p>Lager1</p><p><strong><em>Lager3</em></strong></p><p><strong><em>Lager2</em></strong></p><p>Lager4</p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent"><strong><em>Lager4</em></strong></p><p class="p-indent"><strong><em>Lager3</em></strong></p></td><td></td><td><p>Lager1</p><p><strong><em>Lager4</em></strong></p><p><strong><em>Lager2</em></strong></p><p><strong><em>Lager3</em></strong></p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p><em><strong>Lager3</strong></em></p><p><em><strong>Lager4</strong></em></p><p><em><strong>Lager1</strong></em></p><p><em><strong>Lager2</strong></em></p></td></tr></tbody></table></body></html>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:layer_id','<b>Lager > Lager-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#layers-1" target="_blank">Se lagerkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:group_id','<b>Grupp > Grupp-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#groups" target="_blank">Se gruppkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:map_id','<b>Karta > Kart-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#basic-settings" target="_blank">Se grundkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:control_id','<b>Kontroll > Kontroll-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#controls-1" target="_blank">Se kontrollkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:source_id','<b>Källa > Käll-id</b><br>Ett unikt id som används som primärnyckel i databasen samt för identifiering i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:service_id','<b>Tjänst > Tjänst-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('tilegrid:tilegrid_id','<b>Tilegrid > Tilegrid-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a><br><a href="https://origo-map.github.io/origo-documentation/latest/#tilegridoptions" target="_blank">Se tilegrid-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:footer_id','<b>Sidfot > Sidfots-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#footer" target="_blank">Se sidfotskonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:code','<b>Proj4def > EPSG-kod</b><br>Ett unikt id som används som primärnyckel i databasen samt utgör code i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#proj4defs" target="_blank">Se proj4def-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:attributes','<b>Lager > Attribut</b><br>JSON-formaterat fält som anger vilka attribut som ska visas för valt lager.<br><a href="https://origo-map.github.io/origo-documentation/latest/#attributes" target="_blank">Se attributkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_config','<b>Lager > Stilkonfiguration</b><br>Manuell stilkonfiguration (JSON) som huvudsakligen används för vektorlager. Om fältet används för andra typer av lager, så som WMS-lager, inaktiveras den förenklade stilsättningen som använder fälten "Stilfilter", "Ikon", "Utfälld ikon" m fl.<br><a href="https://origo-map.github.io/origo-documentation/latest/#style-basics" target="_blank">Se stilkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_layer','<b>Lager > Stillager</b><br>Kan sättas till ett lager-id varifrån stilsättningen hämtas. Inaktiverar annan stilsättning som satts i fälten "Stilkonfiguration", "Stilfilter", "Ikon", "Utfälld ikon" m fl.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_filter','<b>Lager > Stilfilter</b><br>Kan innehålla ett sträng som filtererar lagret efter attributvärden. Har ingen effekt om något av fälten "Stillager" eller "Stilkonfiguration" är satta.<br>Exempel (visa företeelser där attributet "signatur" har värdet MW eller MD): <i>[signatur] == ''MW'' OR [signatur] == ''MD''</i><br><a href="https://origo-map.github.io/origo-documentation/latest/#filter" target="_blank">Se filterkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:url','<b>Karta > Url</b><br>Frivilligt, informativt fält där man kan ange en webblänk till kartan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:layers','<b>Karta > Lager</b><br>En kommaseparerad lista med lager-idn för de lager som ska ligga i roten av kartan (utan att ligga i någon undergrupp). Ordningen i listan bestämmer ordningen i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:groups','<b>Karta > Grupper</b><br>En kommaseparerad lista med grupp-idn för de grupper som ska ligga i roten av kartan. Ordningen i listan bestämmer ordningen i lagerträdet.');
