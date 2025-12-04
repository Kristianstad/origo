CREATE SCHEMA map_configs;

CREATE TABLE map_configs.controls
(
    control_id character varying COLLATE pg_catalog."default" NOT NULL,
    options json,
    css character varying COLLATE pg_catalog."default",
    js character varying COLLATE pg_catalog."default",
    onload character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT controls_pkey PRIMARY KEY (control_id)
);

INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('home#1','{ "zoomOnStart": true }','Ställer in kartans utbredning till den som angivits i alternativen för kontrollen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('mapmenu#1','{ "isActive": false }','Skapar en meny uppe till höger för kontroller.');
INSERT INTO map_configs.controls(control_id,abstract,options) VALUES ('sharemap#1','Skapar en delbar länk till kartan. Aktuell utbredning och zoom, synliga lager och kartnålen (om tillämpligt) mm kommer att delas. Karttillståndet sparas med ett unikt id på servern och kan hanteras under Mapstates.','{ "storeMethod": "saveStateToServer", "serviceEndpoint": "/mapstate/mapstate-loader.php", "loadMapStateIdMethod": "query" }');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('geoposition#1','Lägger till en knapp som när du klickar på den centrerar och zoomar kartan till den aktuella positionen. Genom att klicka på knappen en andra gång aktiveras spårningsläget (om enableTracking har satts till true).');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('print#1','Lägger till en utskriftskontroll.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('about#1','{ "buttonText": "Om Origo", "title": "Om Origo", "content": "<p>Origo är ett ramverk för webbkartor. Ramverket bygger på JavaScript-biblioteket OpenLayers. Du kan använda Origo för att skapa egna webbaserade kartapplikationer.</p><br><p>Projektet drivs och underhålls av ett antal svenska kommuner. Besök gärna <a href=\"https://github.com/origo-map/origo\" target=\"_blank\">Origo på GitHub</a> för mer information.</p>" }','Lägger till en om-kartkontroll. En knapp läggs till i menyn. När du klickar på knappen kommer ett popup-fönster att visa allmän information om kartan. OBS - kräver mapmenu-kontrollen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('link#1','{ "title": "Origo", "url": "https://github.com/origo-map/origo" }','Lägger till en knapp på kartmenyn som när den klickas öppnar en ny webbläsarflik med den angivna webbadressen.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('legend#1','{ "labelOpacitySlider": "Opacity", "useGroupIndication" : true }','Lägger till en legend i menyn och som en kartförklaring till kartan.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('position#1','{ "title": "Web Mercator", "projections": { "EPSG:4326": "WGS84", "EPSG:3006": "Sweref99 TM" } }','Kontroll för att visa koordinater. Musens position och mittposition på kartan kan växlas. Koordinater kan sökas på i mittpositionsläget.');
INSERT INTO map_configs.controls(control_id,abstract) VALUES ('measure#1','Lägger till en mätningskontroll. Mät längd, area eller höjd (kräver tillgång till extern höjddatawebbtjänst) i kartan.');
INSERT INTO map_configs.controls(control_id,options,abstract) VALUES ('splash#login1','{ "title": "Välkommen!", "url": "./authorization/authorization-iframe.php", "hideButton": { "visible": false }, "style": "width: 500px;height: 250px;", "hideWhenEmbedded": true }','Inloggningssida som visas när kartan öppnas.');

CREATE TABLE map_configs.plugins
(
    plugin_id character varying COLLATE pg_catalog."default" NOT NULL,
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    js character varying COLLATE pg_catalog."default",
    onload character varying COLLATE pg_catalog."default",
    css character varying COLLATE pg_catalog."default",
    css_files character varying[] COLLATE pg_catalog."default",
    js_files character varying[] COLLATE pg_catalog."default",
    CONSTRAINT plugins_pkey PRIMARY KEY (plugin_id)
);

INSERT INTO map_configs.plugins(plugin_id,abstract,onload,css) VALUES ('layerfavorites#1', 'Ett verktyg för att spara och tända lagerkombinationer som används ofta. Verktyget kommer man åt genom att föra muspekaren längst upp i kartrfönstret, strax ovanför sökfältet (kan dras ner på pekplattor). Tänd lagerkombinationen som du vill spara med hjälp av lagerträdet, ge lagerfavoriten ett unikt namn och klicka på spara. Sedan kan du enkelt och snabbt tända lagerkombinationen när du vill.',
'/* Generate document-specific prefix for localStorage keys */
const docPrefix = location.pathname.replace(/[\\/\\]/g, ''_'') + ''_'';

/* AUTOSLÄCK-LÄGE */
let autoClearMode = JSON.parse(localStorage.getItem(docPrefix + ''autoClearMode'') || ''false'');

/* LÅST-LÄGE */
let lockedMode = JSON.parse(localStorage.getItem(docPrefix + ''lockedMode'') || ''false'');

/* Släckningsfunktion */
const performClear = () => {
  origo.api().getLayersByProperty(''visible'', true)
    .filter((layer) => layer.get(''group'') != ''background'' && layer.get(''group'') != ''rit'' && layer.get(''name'') != ''measure'')
    .forEach(layer => layer.setVisible(false));
};

/* Uppdatera släckknappens utseende */
const updateClearButtonAppearance = () => {
  if (autoClearMode) {
    clearButton.classList.add(''auto-clear-active'');
    clearButton.title = ''Släck alla lager - Autosläck PÅ'';
  } else {
    clearButton.classList.remove(''auto-clear-active'');
    clearButton.title = ''Släck alla lager - Autosläck AV'';
  }
};

/* Spara Autosläck */
const saveAutoClearMode = () => {
  localStorage.setItem(docPrefix + ''autoClearMode'', JSON.stringify(autoClearMode));
};

/* Spara Låst-läge */
const saveLockedMode = () => {
  localStorage.setItem(docPrefix + ''lockedMode'', JSON.stringify(lockedMode));
};

/* Skapa top-bar */
const topBar = document.createElement(''div'');
topBar.className = ''top-bar no-transition'';

/* LÅSIKON – till vänster om släckknappen */
const lockButton = document.createElement(''button'');
lockButton.className = ''lock-button'';
lockButton.title = ''Autostäng verktygsfältet Lagerfavoriter'';
const lockSvg = document.createElementNS(''http://www.w3.org/2000/svg'', ''svg'');
lockSvg.setAttribute(''width'', ''18'');
lockSvg.setAttribute(''height'', ''18'');
lockSvg.setAttribute(''viewBox'', ''0 0 24 24'');
const lockPath = document.createElementNS(''http://www.w3.org/2000/svg'', ''path'');
lockSvg.appendChild(lockPath);
lockButton.appendChild(lockSvg);

/* Släckknapp */
const clearButton = document.createElement(''button'');
clearButton.className = ''clear-button'';
const clearSvgIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''svg'');
clearSvgIcon.setAttribute(''width'', ''18'');
clearSvgIcon.setAttribute(''height'', ''18'');
const clearUseIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''use'');
clearUseIcon.setAttributeNS(''http://www.w3.org/1999/xlink'', ''xlink:href'', ''#ic_visibility_off_24px'');
clearSvgIcon.appendChild(clearUseIcon);
clearButton.appendChild(clearSvgIcon);

/* Enkelklick: släck */
clearButton.onclick = () => {
  performClear();
};

/* Dubbelklick / långtryck: toggla Autosläck */
let clickCount = 0;
let clickTimer = null;
clearButton.addEventListener(''click'', (e) => {
  clickCount++;
  if (clickCount === 1) {
    clickTimer = setTimeout(() => clickCount = 0, 300);
  } else if (clickCount === 2) {
    clearTimeout(clickTimer);
    clickCount = 0;
    autoClearMode = !autoClearMode;
    saveAutoClearMode();
    updateClearButtonAppearance();
  }
});

/* Långtryck på touch */
let longPressTimer = null;
clearButton.addEventListener(''touchstart'', (e) => {
  e.preventDefault();
  longPressTimer = setTimeout(() => {
    autoClearMode = !autoClearMode;
    saveAutoClearMode();
    updateClearButtonAppearance();
  }, 500);
});
clearButton.addEventListener(''touchend'', () => clearTimeout(longPressTimer));

/* Dropdown */
const loadSelect = document.createElement(''select'');
loadSelect.title = ''Välj lagerfavorit att tända'';
loadSelect.innerHTML = ''<option value="">Tänd lagerfavorit...</option>'';

const updateSelectColor = () => {
  loadSelect.style.color = loadSelect.value === '''' ? ''#ccc'' : ''#000'';
};

const updateDropdown = () => {
  const savedIds = JSON.parse(localStorage.getItem(docPrefix + ''savedLayersIds'') || ''[]'');
  loadSelect.innerHTML = ''<option value="">Tänd lagerfavorit...</option>'';
  savedIds.forEach(id => {
    const opt = document.createElement(''option'');
    opt.value = id;
    opt.textContent = id;
    loadSelect.appendChild(opt);
  });
  updateSelectColor();
};

loadSelect.onchange = () => {
  const id = loadSelect.value;
  if (id) {
    if (autoClearMode) performClear();
    const saved = localStorage.getItem(docPrefix + ''savedLayers_'' + id) || '''';
    saved.split('','').forEach(name => name && origo.api().getLayer(name)?.setVisible(true));
    loadSelect.value = '''';
    updateSelectColor();
  }
};

/* Input + Save + Delete */
const saveInput = document.createElement(''input'');
saveInput.type = ''text'';
saveInput.placeholder = ''Lagerfavorit'';
saveInput.title = ''Ange lagerfavorit att skapa, skriva över eller radera'';

const saveButton = document.createElement(''button'');
saveButton.className = ''save-button'';
saveButton.title = ''Spara/skriv över angiven lagerfavorit'';
const saveSvgIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''svg'');
saveSvgIcon.setAttribute(''width'', ''18'');
saveSvgIcon.setAttribute(''height'', ''18'');
const saveUseIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''use'');
saveUseIcon.setAttributeNS(''http://www.w3.org/1999/xlink'', ''xlink:href'', ''#ic_save_24px'');
saveSvgIcon.appendChild(saveUseIcon);
saveButton.appendChild(saveSvgIcon);
saveButton.onclick = () => {
  const id = saveInput.value.trim();
  if (!id) return;
  const layers = origo.api().getLayersByProperty(''visible'', true)
    .filter(l => l.get(''group'') !== ''background'' && l.get(''group'') !== ''rit'' && l.get(''name'') !== ''measure'')
    .map(l => l.getProperties().name).join('','');
  localStorage.setItem(docPrefix + ''savedLayers_'' + id, layers);
  const ids = JSON.parse(localStorage.getItem(docPrefix + ''savedLayersIds'') || ''[]'');
  if (!ids.includes(id)) {
    ids.push(id);
    localStorage.setItem(docPrefix + ''savedLayersIds'', JSON.stringify(ids));
  }
  updateDropdown();
  saveInput.value = '''';
};

const deleteButton = document.createElement(''button'');
deleteButton.className = ''delete-button'';
deleteButton.title = ''Radera angiven lagerfavorit'';
const deleteSvgIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''svg'');
deleteSvgIcon.setAttribute(''width'', ''18'');
deleteSvgIcon.setAttribute(''height'', ''18'');
const deleteUseIcon = document.createElementNS(''http://www.w3.org/2000/svg'', ''use'');
deleteUseIcon.setAttributeNS(''http://www.w3.org/1999/xlink'', ''xlink:href'', ''#ic_delete_24px'');
deleteSvgIcon.appendChild(deleteUseIcon);
deleteButton.appendChild(deleteSvgIcon);
deleteButton.onclick = () => {
  const id = saveInput.value.trim();
  if (!id) return;
  localStorage.removeItem(docPrefix + ''savedLayers_'' + id);
  const ids = JSON.parse(localStorage.getItem(docPrefix + ''savedLayersIds'') || ''[]'');
  localStorage.setItem(docPrefix + ''savedLayersIds'', JSON.stringify(ids.filter(x => x !== id)));
  updateDropdown();
  saveInput.value = '''';
};

/* Grupper */
const leftGroup = document.createElement(''div'');
leftGroup.className = ''group-container'';
const rightGroup = document.createElement(''div'');
rightGroup.className = ''group-container'';

leftGroup.appendChild(lockButton);
leftGroup.appendChild(clearButton);
leftGroup.appendChild(loadSelect);
rightGroup.appendChild(saveInput);
rightGroup.appendChild(saveButton);
rightGroup.appendChild(deleteButton);

topBar.appendChild(leftGroup);
topBar.appendChild(rightGroup);
document.body.appendChild(topBar);

/* Hover-trigger */
const hoverTrigger = document.createElement(''div'');
hoverTrigger.className = ''hover-trigger'';
document.body.appendChild(hoverTrigger);

const updateTrigger = () => {
  const barRect = topBar.getBoundingClientRect();
  hoverTrigger.style.width = `${barRect.width}px`;
  hoverTrigger.style.left = ''50%'';
  hoverTrigger.style.transform = ''translateX(-50%)'';
};

updateTrigger();
window.addEventListener(''resize'', updateTrigger);

setTimeout(() => {
  topBar.className = topBar.className.replace(''no-transition'', '''');
}, 0);

/* Uppdatera lås-ikon */
const updateLockButton = () => {
  if (lockedMode) {
    /* LÅST – stängt hänglås */
    lockPath.setAttribute(''d'', ''M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1 .9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z'');
    lockButton.classList.add(''locked'');
    lockButton.title = ''Aktivera autostäng för verktygsfältet Lagerfavoriter'';
  } else {
    /* OLÅST – öppen bygel */
    lockPath.setAttribute(''d'', ''M19 10h-1V7c0-2.76-2.24-5-5-5s-5 2.24-5 5h2c0-1.66 1.34-3 3-3s3 1.34 3 3v3H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm-7 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z'');
    lockButton.classList.remove(''locked'');
    lockButton.title = ''Lås fast verktygsfältet Lagerfavoriter'';
  }
};

/* Klick på lås-ikon */
lockButton.onclick = () => {
  lockedMode = !lockedMode;
  saveLockedMode();
  updateLockButton();
  if (lockedMode) showTopBarAndPushCenter();
};

/* AUTO-DÖLJ */
let hideTimeout = null;
let lastMouseX = null;
let lastMouseY = null;
document.addEventListener(''mousemove'', e => { lastMouseX = e.clientX; lastMouseY = e.clientY; });

const hideTopBarAndResetCenter = () => {
  if (lockedMode) return;
  topBar.classList.add(''hidden'');
  document.querySelector(''.o-ui .top-center'')?.classList.remove(''top-bar-visible'');
};

const showTopBarAndPushCenter = () => {
  clearTimeout(hideTimeout);
  topBar.classList.remove(''hidden'');
  document.querySelector(''.o-ui .top-center'')?.classList.add(''top-bar-visible'');
};

const showTopBar = showTopBarAndPushCenter;

const hideTopBar = e => {
  if (lockedMode) return;
  if (e?.relatedTarget && (topBar.contains(e.relatedTarget) || hoverTrigger.contains(e.relatedTarget))) return;
  if ([saveInput, saveButton, loadSelect, clearButton, deleteButton, lockButton].includes(document.activeElement)) return;
  hideTimeout = setTimeout(() => {
    if (lastMouseX !== null && document.elementFromPoint(lastMouseX, lastMouseY)?.closest(''.top-bar, .hover-trigger'')) return;
    hideTopBarAndResetCenter();
  }, 1000);
};

/* Händelser */
[clearButton, loadSelect, saveInput, saveButton, deleteButton, lockButton].forEach(el => {
  el.addEventListener(''mouseenter'', showTopBar);
  el.addEventListener(''focus'', showTopBar);
});
loadSelect.addEventListener(''mousedown'', showTopBar);
saveInput.addEventListener(''mousedown'', showTopBar);
topBar.addEventListener(''mousemove'', showTopBar);
lockButton.addEventListener(''mouseenter'', showTopBar);

let touchStartY = null;
let touchStartedInTrigger = false;
document.addEventListener(''touchstart'', e => {
  const t = e.touches[0];
  touchStartY = t.clientY;
  const r = hoverTrigger.getBoundingClientRect();
  touchStartedInTrigger = t.clientY >= r.top && t.clientY <= r.bottom && t.clientX >= r.left && t.clientX <= r.right;
});
document.addEventListener(''touchend'', e => {
  if (lockedMode) return;
  const t = e.changedTouches[0];
  const endY = t.clientY;
  const threshold = window.innerWidth <= 768 ? 15 : 20;
  if (touchStartedInTrigger && endY > touchStartY) {
    e.preventDefault();
    showTopBarAndPushCenter();
  } else if (endY <= threshold && touchStartY > endY && document.activeElement !== saveInput) {
    e.preventDefault();
    clearTimeout(hideTimeout);
    hideTopBarAndResetCenter();
  }
  touchStartY = null;
  touchStartedInTrigger = false;
});

hoverTrigger.addEventListener(''mouseenter'', showTopBar);
topBar.addEventListener(''mouseenter'', showTopBar);
topBar.addEventListener(''mouseleave'', hideTopBar);
document.addEventListener(''mouseleave'', hideTopBar);

document.addEventListener(''click'', e => {
  if (lockedMode) return;
  if (!topBar.contains(e.target) && document.activeElement !== saveInput) {
    clearTimeout(hideTimeout);
    hideTopBarAndResetCenter();
  }
});

[loadSelect, saveButton, deleteButton].forEach(el => el.addEventListener(''change'', updateTrigger));
[saveButton, deleteButton].forEach(el => el.addEventListener(''click'', updateTrigger));

/* Initiera */
topBar.className = ''top-bar hidden no-transition'';
updateDropdown();
updateClearButtonAppearance();
updateLockButton();

/* VISA TOP-BAR DIREKT OM LÅST */
if (lockedMode) {
  setTimeout(() => {
    showTopBarAndPushCenter();
  }, 50); // Liten fördröjning för att säkerställa DOM-uppdatering
}',
'.o-ui .top-center {
  top: 1rem;
  transition: top 0.3s ease-in-out;
}

.o-ui .top-center.top-bar-visible {
  top: 3.5rem;
}

.top-bar {
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  background-color: #fff;
  padding: 8px; /* Updated padding for desktop */
  display: inline-flex;
  flex-wrap: wrap;
  gap: 12px; /* Spacing between groups */
  transition: transform 0.3s ease-in-out;
  z-index: 1000;
  box-sizing: border-box;
  border-bottom-left-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
  box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.2);
  max-width: 600px; /* Width for big screens */
}

.top-bar.hidden {
  transform: translateX(-50%) translateY(-100%);
}

.top-bar.no-transition {
  transition: none !important;
}

/* Container for left group (clearButton + loadSelect) and right group (saveInput + saveButton + deleteButton) */
.group-container {
  display: flex;
  gap: 8px; /* Spacing between elements */
  flex: 1; /* Equal widths for groups in unwrapped state */
  min-width: 150px; /* Prevent groups from collapsing too small */
  box-sizing: border-box;
}

.top-bar button {
  padding: 0.5rem; /* Unified padding for all buttons */
  font-size: 16px;
  cursor: pointer;
  border: none;
  background-color: #f5f5f5;
  color: #000;
  border-radius: 50%; /* Circular buttons */
  box-sizing: border-box;
  min-width: 34px; /* Adjusted for smaller icons */
}

.top-bar button:hover {
  background-color: #e0e0e0;
}

/* Specific styles for clearButton with SVG icon */
.top-bar button.clear-button {
  display: flex;
  justify-content: center;
  align-items: center;
}

.top-bar button.clear-button svg {
  width: 18px; /* Smaller icon size */
  height: 18px;
}

/* Specific styles for saveButton with SVG icon */
.top-bar button.save-button {
  display: flex;
  justify-content: center;
  align-items: center;
}

.top-bar button.save-button svg {
  width: 18px; /* Smaller icon size */
  height: 18px;
}

/* Specific styles for deleteButton with SVG icon */
.top-bar button.delete-button {
  display: flex;
  justify-content: center;
  align-items: center;
}

.top-bar button.delete-button svg {
  width: 18px; /* Smaller icon size */
  height: 18px;
}

/* Apply fill to all button SVGs */
.top-bar button.clear-button svg,
.top-bar button.save-button svg,
.top-bar button.delete-button svg {
  fill: #4a4a4a;
}

.top-bar select {
  padding: 2px 16px;
  font-size: 16px;
  cursor: pointer;
  border: none;
  outline: none;
  background-color: #f5f5f5;
  color: #000;
  border-radius: 2em;
  flex: 1;
  box-sizing: border-box;
  min-width: 0;
}

.top-bar select:hover {
  background-color: #e0e0e0;
}

.top-bar select option[value=""] {
  color: #ccc;
}

.top-bar select option:not([value=""]) {
  color: #000;
}

.top-bar input {
  padding: 2px 16px;
  font-size: 16px;
  border: none;
  background-color: #f5f5f5;
  color: #000;
  border-radius: 2em;
  flex: 1;
  box-sizing: border-box;
  min-width: 0;
}

.top-bar input:focus {
  outline: none; /* Remove focus outline */
  border: none; /* Ensure no border on focus */
}

.top-bar input::placeholder {
  color: #ccc;
}

.hover-trigger {
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  height: 3px;
  z-index: 999;
}

@media screen and (max-width: 768px) {
  .top-bar {
    padding: 6px;
    gap: 8px;
    max-width: 360px; /* Accommodates buttons */
    width: calc(100% - 12px); /* Respect 6px left + right padding */
    justify-content: center;
  }

  .group-container {
    gap: 6px;
    width: 100%; /* Full width in wrapped state */
    max-width: calc(50% - 4px); /* Equal group widths, accounting for gap */
    flex: none; /* Remove flex-grow */
    min-width: 0;
  }

  .top-bar button {
    padding: 0.5rem;
    font-size: 14px;
    border: none;
    min-width: 30px; /* Adjusted for smaller icons */
  }

  .top-bar select {
    padding: 2px 12px;
    font-size: 14px;
    border: none;
    border-radius: 2em;
    max-width: 130px; /* Balances group widths */
  }

  .top-bar select option[value=""] {
    color: #ccc;
  }

  .top-bar select option:not([value=""]) {
    color: #000;
  }

  .top-bar input {
    padding: 2px 12px;
    font-size: 14px;
    border: none;
    border-radius: 2em;
    max-width: 130px; /* Balances group widths */
  }

  .top-bar input:focus {
    outline: none; /* Remove focus outline */
    border: none; /* Ensure no border on focus */
  }

  .hover-trigger {
    height: 30px;
  }
}

/* Handle very small screens to ensure equal group widths and padding */
@media screen and (max-width: 400px) {
  .top-bar {
    padding: 6px;
    gap: 6px;
    width: calc(100% - 12px); /* Respect 6px left + right padding */
    max-width: 320px; /* Accommodates buttons */
  }

  .group-container {
    gap: 4px;
    width: 100%;
    max-width: calc(50% - 3px); /* Equal group widths, account for gap */
    flex: none;
  }

  .top-bar button {
    padding: 0.5rem;
    font-size: 12px;
    min-width: 26px; /* Adjusted for smaller icons */
  }

  .top-bar select {
    padding: 2px 10px;
    font-size: 12px;
    max-width: 110px; /* Balances group widths */
  }

  .top-bar input {
    padding: 2px 10px;
    font-size: 12px;
    max-width: 110px; /* Balances group widths */
  }

  .top-bar input:focus {
    outline: none; /* Remove focus outline */
    border: none; /* Ensure no border on focus */
  }
}

/* Inverterade färger när Autosläck är PÅ */
.top-bar button.clear-button.auto-clear-active {
  background-color: #333 !important;
  color: #fff;
}

.top-bar button.clear-button.auto-clear-active svg {
  fill: #fff !important;
}

.top-bar button.clear-button.auto-clear-active:hover {
  background-color: #555 !important;
}

/* .top-center (oförändrat) */
.o-ui .top-center {
  top: 1rem;
  transition: top 0.3s ease-in-out;
}
.o-ui .top-center.top-bar-visible {
  top: 3.5rem;
}

/* LÅSIKON – till vänster om släckknappen */
.top-bar .lock-button {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.5rem;
  font-size: 16px;
  cursor: pointer;
  border: none;
  background-color: #f5f5f5;
  color: #000;
  border-radius: 50%;
  box-sizing: border-box;
  min-width: 34px;
}

.top-bar .lock-button:hover {
  background-color: #e0e0e0;
}

.top-bar .lock-button svg {
  width: 18px;
  height: 18px;
  fill: #4a4a4a;
}

/* INVERTERADE FÄRGER I LÅST LÄGE */
.top-bar .lock-button.locked {
  background-color: #333 !important;
}

.top-bar .lock-button.locked svg {
  fill: #fff !important;
}

.top-bar .lock-button.locked:hover {
  background-color: #555 !important;
}

/* DÖLJ LÅSKNAPP PÅ SMÅ SKÄRMAR */
@media screen and (max-width: 768px) {
  .top-bar .lock-button {
    display: none !important;
  }
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urlzoomtolayer#1', 'Zoomar till lagret angiven i url-parametern zoomToLayer. Lagret måste finnas i kartan och tänds automatiskt, namnet anges utan hash-suffix. För att zooma till markers sätt zoomToLayer=markerLayer (obs! denna plugin måste läggas efter den/de plugins som skapar markers).',
'const zoomToLayer = getUrlParam(''zoomToLayer'');
if (zoomToLayer != null)
{
	let layer;
	if (!(layer=origo.api().getLayer(zoomToLayer)))
	{
		console.error(''zoomToLayer '' + zoomToLayer + '' does not exist!'');
	}
	else
	{
		function zoomLoop(zoomMaxAttempts, zoomAttempt = 1 ) {
			setTimeout(() => {
				if (zoomAttempt <= zoomMaxAttempts) {
					const extent = layer.getSource().getExtent();
					if (extent && Array.isArray(extent) && extent.length === 4 && extent.every(coord => Number.isFinite(coord))) {
						/*console.log(''Valid extent:'', extent);*/
						origo.api().zoomToExtent(Origo.ol.geom.Polygon.fromExtent(extent));
					} else {
						zoomLoop(zoomMaxAttempts, zoomAttempt + 1);
					}
				} else {
					console.error(''zoomToLayer: Extent not available after max attempts'');
				}
			}, 100);
		}

		origo.api().getMap().getView().setZoom(0);
		layer.setVisible(true);	
		zoomLoop(100);
	}
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urlsearch#1', 'Söker mha sökkontrollen på url-parametern poi eller poim (tex poi=tivoligatan%205). Sätt parametern hideSearchInfo=true för att dölja textrutan med sökresultatet. Man kan även ange en specifik zoomnivå med parametern zoom. (poim är ett legacy-namn och har exakt samma funktion som poi).',
'function keySimulation({ key = "Enter", elementId = "hjl", eventType = "keyup" } = {}) {
  try {
    const event = new KeyboardEvent(eventType, {
      key,
      code: key,
      keyCode: key === "Enter" ? 13 : 0,
      which: key === "Enter" ? 13 : 0,
      bubbles: true,
      cancelable: true
    });

    const element = document.getElementById(elementId);
    if (!element) {
      console.warn(`keySimulation: Element with ID ''${elementId}'' not found.`);
      return;
    }

    element.dispatchEvent(event);
  } catch (error) {
    console.error(`keySimulation: Error dispatching ${eventType} event`, error);
  }
}

function finishSearch(hideSearchInfo = "false") {
  const zoom = getUrlParam(''zoom'');
  const checkInterval = 100; /* Check every 100ms */
  const maxAttempts = 100; /* 10 seconds total (100 * 100ms) */
  const hideTimeoutDuration = 5000; /* 5 seconds for hiding */
  let attempts = 0;

  /* Temporarily hide suggestion lists */
  const list1 = document.querySelector("#awesomplete_list_1");
  const list2 = document.querySelector("#awesomplete_list_2");
  if (list1) list1.style.display = "none";
  if (list2) list2.style.display = "none";

  function zoomLoop(view, zoomLevel, zoomMaxAttempts, zoomAttempt = 1 ) {
    let currentZoom;
    setTimeout(() => {
      if (zoomAttempt <= zoomMaxAttempts) {
        try {
          currentZoom = view.getZoom();
          /*console.log("zoomAttempt: " + zoomAttempt + ", currentzoom: " + currentZoom);*/
          if (currentZoom != zoomLevel) {
            view.setZoom(zoomLevel);
          } else {
            zoomLoop(view, zoomLevel, zoomMaxAttempts, zoomAttempt + 1);
          }
        } catch (zoomError) {
          console.error(`finishSearch: Error setting zoom level`, zoomError);
        }
      }
    }, 500);
  }

  function tryClick() {
    const suggestion1 = document.querySelector("#awesomplete_list_1 > li > div.suggestion");
    const suggestion2 = document.querySelector("#awesomplete_list_2 > li > div.suggestion");

    if (suggestion1 || suggestion2) {
      try {
        /* Click the suggestion (still works even if parent is hidden) */
        (suggestion1 || suggestion2).click();

        /* Apply zoom if valid */
        if (zoom) {
          const zoomLevel = parseInt(zoom, 10);
          if (!isNaN(zoomLevel)) {
		    let view = origo.api().getMap().getView();
		    view.setZoom(zoomLevel);
			zoomLoop(view, zoomLevel, 15);
          } else {
            console.warn(`finishSearch: Invalid zoom parameter ''${zoom}''`);
          }
        }

        /* Restore visibility of suggestion lists after click */
        if (list1) list1.style.display = "";
        if (list2) list2.style.display = "";

        if (hideSearchInfo === "true") {
          /* Check and hide both #o-popup and #sidebarcontainer if they exist */
          const targets = ["#o-popup", "#sidebarcontainer"];
          targets.forEach(targetElement => {
            let hideObserver = new MutationObserver((hideMutations, hideObs) => {
              const element = document.querySelector(targetElement);
              if (element) {
                element.style.display = "none";
                hideObs.disconnect();
              }
            });

            hideObserver.observe(document.body, { childList: true, subtree: true });

            setTimeout(() => {
              hideObserver.disconnect();
              if (!document.querySelector(targetElement)) {
                console.warn(`finishSearch: Target element ${targetElement} not found after ${hideTimeoutDuration}ms.`);
              }
            }, hideTimeoutDuration);
          });
        }
      } catch (e) {
        console.error("finishSearch: Error clicking suggestion", e);
        /* Restore visibility if click fails */
        if (list1) list1.style.display = "";
        if (list2) list2.style.display = "";
      }
    } else if (attempts < maxAttempts) {
      attempts++;
      setTimeout(tryClick, checkInterval);
    } else {
      console.warn("finishSearch: Timed out waiting for suggestions after", maxAttempts * checkInterval, "ms.");
      /* Restore visibility on timeout */
      if (list1) list1.style.display = "";
      if (list2) list2.style.display = "";
    }
  }

  tryClick();
}

let poim = getUrlParam(''poim'') || getUrlParam(''poi'');
const hideSearchInfo = getUrlParam(''hideSearchInfo'') === "true" ? "true" : "false";
if (poim) {
	try {
		poim = decodeURI(poim);
	} catch (e) {
		console.warn("Event listener: Error decoding poim/poi", e);
		return;
	}
	let input = document.getElementById("hjl");
	if (input) {
		input.value = poim;
		keySimulation();
		setTimeout(() => finishSearch(hideSearchInfo), 100);
	} else {
		console.warn("Event listener: Element with ID ''hjl'' not found.");
	}
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urlmarker#1', 'Lägger till en eller flera markers i kartan utefter givna url-parametrar. Url-parametrar som kan ges är xyi<nummer>=<x-koordinat>,<y-koordinat>,<infotext> (tex. xyi1=14.1529,56.0354,txt1&xyi2=14.2529,56.1354,p2). Det finns även några legacy-parametrar, xym och diainfo (tex. xym=14.0953039,56.0017954&diainfo=Vä%20bibliotek). Koordinater anges i EPSG:4326 eller EPSG:3008. Sätter man bara ut en marker kommer denna att visas med info-popup om man inte även anger parametern hideSearchInfo=true. Urlmarker kan kombineras med pluginet urlzoomtolayer (ange zoomToLayer=markerLayer), men då måste urlzoomtolayer läggas efter urlmarker i plugins-listan.',
'const xym = getUrlParam(''xym'');
const xyiarray=[];
if (xym != null)
{
	let diainfo = getUrlParam(''diainfo'');
	if (diainfo != null)
	{
		String.prototype.trimLeft = function(charlist) {
			if (charlist === undefined) {
				charlist = "\\s";
			}
			return this.replace(new RegExp("^[" + charlist + "]+"), "");
		};
		String.prototype.trimRight = function(charlist) {
			if (charlist === undefined) {
				charlist = "\\s";
			}
			return this.replace(new RegExp("[" + charlist + "]+$"), "");
		};
		diainfo = diainfo.trimLeft(''["'').trimRight(''"]'');
	}
	else
	{
		diainfo = "";
	}
	xyiarray.push(xym + "," + diainfo);
}
const xyi1 = getUrlParam(''xyi1'');
if (xyi1 != null)
{
	let n=1;
	let value;
	while (value = getUrlParam(''xyi'' + n))
	{
		xyiarray.push(value);
		n++;
	}
}
if (xyiarray.length > 0)
{
	origo.api().addStyle(''Marker'', [[
		{
			"circle": {
				"radius": 10,
				"stroke": {
					"color": "rgba(0,0,0,1)",
					"width": 2.5
				},
				"fill": {
					"color": "rgba(255,255,255,0.9)"
				}
			}
		},
		{
			"circle": {
				"radius": 2.5,
				"stroke": {
					"color": "rgba(0,0,0,0)",
					"width": 1
				},
				"fill": {
					"color": "rgba(37,129,196,1)"
				}
			}
		}
	]]);

	function printXyi(xyi)
	{
		const xyiArray=xyi.split('','');
		if (xyiArray[2]=== undefined)
		{
			xyiArray[2]='''';
		}
		let jsonxy = JSON.parse("[" + xyiArray[0] + "," + xyiArray[1] + "]");
		if (xyi.charAt(2) == ''.'')
		{
			jsonxy = origo.api().getMapUtils().transformCoordinate(jsonxy,''EPSG:4326'',''EPSG:3008'');
		}
		origo.api().getMap().getView().setCenter( jsonxy );
		origo.api().addMarker(jsonxy,'''',xyiArray[2],{style: ''Marker''});
	}
	xyiarray.forEach(printXyi);
	const hideSearchInfo=getUrlParam(''hideSearchInfo'');
	if (hideSearchInfo === "true" || xyiarray.length > 1) {
		const hideTimeoutDuration = 5000;
		/* Check and hide both #o-popup and #sidebarcontainer if they exist */
		const targets = ["#o-popup", "#sidebarcontainer"];
		targets.forEach(targetElement => {
			let hideObserver = new MutationObserver((hideMutations, hideObs) => {
				const element = document.querySelector(targetElement);
				if (element) {
					element.style.display = "none";
					hideObs.disconnect();
				}
			});
			hideObserver.observe(document.body, { childList: true, subtree: true });
			setTimeout(() => {
				hideObserver.disconnect();
				if (!document.querySelector(targetElement)) {
					console.warn(`finishSearch: Target element ${targetElement} not found after ${hideTimeoutDuration}ms.`);
				}
			}, hideTimeoutDuration);
		});
	}
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urllayers#1', 'Tänder overlay-lager listade i url-parametern ol och/eller ett bakgrundslager angivet som url-parametern bl. Lagren måste finnas i kartan och anges utan hash-suffix (tex. ol=nyko,deso&bl=turistkarta_nedtonad).',
'let ol = getUrlParam(''ol'');
const bl = getUrlParam(''bl'');
if (ol != null)
{
	ol = ol.split(",");
	for (let element of ol) {
		if (origo.api().getLayer(element))
		{
			origo.api().getLayer(element).setVisible(true);
		}
	}
}
if (bl != null)
{
	let currentbl = origo.api().getLayersByProperty(''group'',''background'').filter((layer) => layer.get(''visible''))[0];
	if (bl != currentbl.values_[''name''] && origo.api().getLayer(bl))
	{
		currentbl.setVisible(false);
		origo.api().getLayer(bl).setVisible(true);
	}
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urlhideallmodules#1', 'Gömmer de flesta (alla?) kontroller om urlen innehåller parametern hideallmodules=true.',
'const hideallmodules = getUrlParam(''hideallmodules'');
if (hideallmodules == "true")
{
	origo.api().getControlByName("search").hide();
	origo.api().getControlByName("home").hide();
	origo.api().getControlByName("mapmenu").hide();
	origo.api().getControlByName("legend").hide();
	origo.api().getControlByName("geoposition").hide();
	origo.api().getControlByName("measure").hide();
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('urlcenter#1', 'Panorerar i kartan till centrumkoordinaten angiven i url-parametern center. Koordinaten kan anges i EPSG:3008 (center=191815,6211237) eller EPSG:4326 (center=14.16883,56.02806). Hash-parametern center fungerar som standard i Origo (utan detta plugin) men då endast med koordinat i kartans projektion.',
'const center = getUrlParam(''center'');
if (center != null)
{
	let view = origo.api().getMap().getView();
	let jsoncenter = JSON.parse("[" + center + "]");
	if (center.charAt(2) == ''.'')
	{
		jsoncenter = origo.api().getMapUtils().transformCoordinate(jsoncenter,''EPSG:4326'',''EPSG:3008'');
	}
	view.setCenter(jsoncenter);
}');
INSERT INTO map_configs.plugins(plugin_id,abstract,onload) VALUES ('queryzoom#1', 'Zoomar i kartan efter query-parametern zoom, tex. ?zoom=5 (hash-parametern #zoom fungerar som standard i Origo).',
'const zoom = getUrlParam(''zoom'');
if (zoom != null)
{
	let view = origo.api().getMap().getView();
	view.setZoom(zoom);
}');

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

INSERT INTO map_configs.groups(group_id,title,expanded,layers,abstract) VALUES ('background#1','Bakgrundskartor',true,'{osm#1}','En lagergrupp med bakgrundslager.');
INSERT INTO map_configs.groups(group_id,title,expanded,layers,abstract) VALUES ('origosamverkan#1','Origosamverkan',true,'{origokommuner#1,kommunmask#1}','En lagergrupp som visualiserar samverkan inom Origo-projektet.');
INSERT INTO map_configs.groups(group_id,layers,abstract) VALUES ('none#1','{kommunmask#1}','En lagergrupp som är dold i lagerträdet.');

CREATE TABLE map_configs.layers
(
    layer_id character varying COLLATE pg_catalog."default" NOT NULL,
    title character varying COLLATE pg_catalog."default",
    source character varying COLLATE pg_catalog."default",
    style_layer character varying COLLATE pg_catalog."default",
    type character varying COLLATE pg_catalog."default",
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
    opacity numeric(3,2),
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
    thematicstyling boolean,
    CONSTRAINT layers_pkey PRIMARY KEY (layer_id)
);

INSERT INTO map_configs.layers(layer_id,title,type,attributes,visible,style_config,source,abstract,show_meta,origin) VALUES ('origokommuner#1','Origokommuner','GEOJSON','[ { "name": "name" } ]',true,'[ [ { "label": "Origokommuner", "circle": { "radius": 10, "stroke": { "color": "rgba(0,0,0,1)", "width": 2.5 }, "fill": { "color": "rgba(255,255,255,0.9)" } } }, { "circle": { "radius": 2.5, "stroke": { "color": "rgba(0,0,0,0)", "width": 1 }, "fill": { "color": "rgba(37,129,196,1)" } } } ] ]','origo-cities.json','Ett lager som visar kommuner delaktiga i Origoprojektet.',true,'origo');
INSERT INTO map_configs.layers(layer_id,title,type,visible,style_config,source,queryable,opacity,abstract,show_meta,origin) VALUES ('kommunmask#1','Origo-mask','GEOJSON',true,'[ [ { "stroke": { "color": "rgba(0,0,0,1.0)" }, "fill": { "color": "rgba(0,0,0,1.0)" } } ] ]','origo-mask.json',false,0.25,'Ett lager som tonar ner de delar av kartan som inte utgör en del av en Origokommun.',true,'origo');
INSERT INTO map_configs.layers(layer_id,title,type,visible,show_icon,icon,source,queryable,abstract,show_meta,origin) VALUES ('osm#1','OpenStreetMap','OSM',true,true,'img/png/osm.png','OpenStreetMap',false,'Ett bakgrundslager från OpenStreetMap.',true,'osm');

CREATE TABLE map_configs.maps
(
    map_id character varying COLLATE pg_catalog."default" NOT NULL,
    controls character varying[] COLLATE pg_catalog."default" DEFAULT '{home#1,mapmenu#1,sharemap#1,geoposition#1,print#1,about#1,link#1,legend#1,position#1,measure#1}'::character varying[],
    mapgrid boolean NOT NULL DEFAULT true,
    projectioncode character varying COLLATE pg_catalog."default" NOT NULL DEFAULT 'EPSG:3857'::character varying,
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
    plugins character varying[] COLLATE pg_catalog."default",
    css character varying COLLATE pg_catalog."default",
    title character varying COLLATE pg_catalog."default",
    icon character varying COLLATE pg_catalog."default" DEFAULT '../img/png/logo.png'::character varying,
    css_files character varying[] COLLATE pg_catalog."default" DEFAULT '{css/style.css}'::character varying[],
    js character varying COLLATE pg_catalog."default",
    onload character varying COLLATE pg_catalog."default",
    js_files character varying[] COLLATE pg_catalog."default" DEFAULT '{js/origo.min.js}'::character varying[],
    url character varying COLLATE pg_catalog."default",
    palette json,
    searchengineindexable boolean,
    changed boolean,
    CONSTRAINT map_pk PRIMARY KEY (map_id)
);

INSERT INTO map_configs.maps(map_id,title,footer,layers,groups,abstract,show_meta) VALUES ('demokarta','Demokarta - Origo','origo#1','{}','{origosamverkan#1,background#1}','En demokarta som visar kommuner delaktiga i Origoprojektet.',true);
INSERT INTO map_configs.maps(map_id,title,footer,groups,abstract,show_meta) VALUES ('preview','Förhandsgranska - Origo','origo#1','{background#1}','En karta som används för att visa förhandsgranskningar i administrationsverktyget.',true);

CREATE TABLE map_configs.proj4defs
(
    code character varying COLLATE pg_catalog."default" NOT NULL,
    projection character varying COLLATE pg_catalog."default",
    projectionextent box,
    alias character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT proj4defs_pkey PRIMARY KEY (code)
);

INSERT INTO map_configs.proj4defs(code,projection,projectionextent) VALUES ('EPSG:3006','+proj=utm +zone=33 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs','(-20026376.39,-20048966.10),(20026376.39,20048966.10)');

CREATE TABLE map_configs.services
(
    service_id character varying COLLATE pg_catalog."default" NOT NULL,
    base_url character varying COLLATE pg_catalog."default",
    alias character varying COLLATE pg_catalog."default",
    type character varying COLLATE pg_catalog."default",
    restricted boolean,
    info character varying COLLATE pg_catalog."default",
    formats character varying[] COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    CONSTRAINT services_pkey PRIMARY KEY (service_id)
);

INSERT INTO map_configs.services(service_id,type,formats,abstract) VALUES ('GeoJSON','File','{GEOJSON}','Använd denna tjänst om källan är en GeoJSON-fil.');
INSERT INTO map_configs.services(service_id,type,formats,abstract) VALUES ('OpenStreetMap','OpenStreetMap','{OSM}','Använd denna tjänst om källan är OpenStreetMap.');

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
    file character varying COLLATE pg_catalog."default",
    CONSTRAINT sources_pkey PRIMARY KEY (source_id)
);

INSERT INTO map_configs.sources(source_id,service,file,abstract) VALUES ('origo-cities.json','GeoJSON','data/origo-cities-3857.geojson','Fil med origokommuner.');
INSERT INTO map_configs.sources(source_id,service,file,abstract) VALUES ('origo-mask.json','GeoJSON','data/origo-mask-3857.geojson','Fil med en mask för origokommuner.');
INSERT INTO map_configs.sources(source_id,service,abstract) VALUES ('OpenStreetMap','OpenStreetMap','Geodata från OpenStreetMap.');

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

CREATE TABLE map_configs.news
(
    new_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    text character varying COLLATE pg_catalog."default",
    reads character varying[] COLLATE pg_catalog."default",
    deletes character varying[] COLLATE pg_catalog."default",
    date timestamp without time zone NOT NULL DEFAULT now(),
    info character varying COLLATE pg_catalog."default",
    CONSTRAINT news_pkey PRIMARY KEY (new_id)
);

CREATE TABLE map_configs.adusers
(
    aduser_id character varying COLLATE pg_catalog."default" NOT NULL,
    name character varying COLLATE pg_catalog."default",
    email character varying COLLATE pg_catalog."default",
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    lastlogin timestamp without time zone,
    adgroups character varying[] COLLATE pg_catalog."default",
    company character varying COLLATE pg_catalog."default",
    department character varying COLLATE pg_catalog."default",
    CONSTRAINT adusers_pkey PRIMARY KEY (aduser_id)
);

CREATE TABLE map_configs.mapstates
(
    mapstate_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    lastuse timestamp without time zone,
    CONSTRAINT mapstates_pkey PRIMARY KEY (mapstate_id)
);

CREATE TABLE map_configs.formats
(
    format_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    CONSTRAINT formats_pkey PRIMARY KEY (format_id)
);

INSERT INTO map_configs.formats(format_id,abstract) VALUES ('GEOJSON','GeoJSON-format');
INSERT INTO map_configs.formats(format_id,abstract) VALUES ('OSM','OpenStreetMap-format');

CREATE TABLE map_configs.helps
(
    help_id character varying COLLATE pg_catalog."default" NOT NULL,
    abstract character varying COLLATE pg_catalog."default",
    info character varying COLLATE pg_catalog."default",
    CONSTRAINT helps_pkey PRIMARY KEY (help_id)
);

INSERT INTO map_configs.helps(help_id,abstract) VALUES ('help:help_id','<b>Hjälp > Verktygsfält</b><br>Det fulla namnet för det verktygsfält som ska tilldelas en hjälptext (för muspekaren över namnet på ett verktygsfält för att få upp det fulla namnet).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('help:abstract','<b>Hjälp > Hjälptext</b><br>Meningsfull hjälptext (html) som visas när användaren klickar på<button class="smallHelpButton">?</button> till höger om det aktuella verktygsfältet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:indexweight','<html lang="sv-SE"><head><style>table{line-height:1;float:left}td{min-width:3em;text-align:left;vertical-align:top}.p-indent{padding-left:2em}</style></head><body><b>Lager > Indexvikt</b><br>Indexvikt kan sättas på lager och anges som ett positivt eller negativt heltalsvärde. Värdet påverkar ritordningen och ibland lagerträdet.<br><table cellspacing="0" cellpadding="0"><tbody><tr><td colspan="3"><h3>Inga indexvikter satta</h3></td><td></td><td colspan="3"><h3>Lager3=1 <em>eller</em> Lager2=-1</h3></td><td></td><td colspan="3"><h3>Lager4=2</h3></td><td></td><td colspan="3"><h3>Lager3=2 <em>och</em> Lager4=2</h3></td></tr><tr><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td><td></td><td><strong>Lagerträd</strong></td><td></td><td><strong>Ritordning</strong></td></tr><tr><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p>Lager1</p><p>Lager2</p><p>Lager3</p><p>Lager4</p><p></p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p>Lager1</p><p><strong><em>Lager3</em></strong></p><p><strong><em>Lager2</em></strong></p><p>Lager4</p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent"><strong><em>Lager4</em></strong></p><p class="p-indent"><strong><em>Lager3</em></strong></p></td><td></td><td><p>Lager1</p><p><strong><em>Lager4</em></strong></p><p><strong><em>Lager2</em></strong></p><p><strong><em>Lager3</em></strong></p></td><td></td><td><p>Grupp1</p><p class="p-indent">Lager1</p><p class="p-indent">Lager2</p><p>Grupp2</p><p class="p-indent">Lager3</p><p class="p-indent">Lager4</p></td><td></td><td><p><em><strong>Lager3</strong></em></p><p><em><strong>Lager4</strong></em></p><p><em><strong>Lager1</strong></em></p><p><em><strong>Lager2</strong></em></p></td></tr></tbody></table></body></html>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:layer_id','<b>Lager > Lager-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#layers-1" target="_blank">Se lagerkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:group_id','<b>Grupp > Grupp-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen. Bakgrundslager skall läggas i en grupp med id background#x och lager som ska ligga i kart-roten men inte ska synas i lagerträdet ska läggas i en grupp med id none#x.<br><a href="https://origo-map.github.io/origo-documentation/latest/#groups" target="_blank">Se gruppkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:map_id','<b>Karta > Kart-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#basic-settings" target="_blank">Se grundkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:control_id','<b>Kontroll > Kontroll-id</b><br>Ett unikt id som används som primärnyckel i databasen. Text framför #-tecknet utgör name i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#controls-1" target="_blank">Se kontrollkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:source_id','<b>Källa > Käll-id</b><br>Ett unikt id som används som primärnyckel i databasen samt för identifiering i json-konfigurationen. Text framför #-tecknet läggs till i anropet till den bakomliggande tjänsten om tjänsten är av typen QGIS eller Geoserver.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:service_id','<b>Tjänst > Tjänst-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('tilegrid:tilegrid_id','<b>Tilegrid > Tilegrid-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#source" target="_blank">Se källkonfiguration</a><br><a href="https://origo-map.github.io/origo-documentation/latest/#tilegridoptions" target="_blank">Se tilegrid-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:footer_id','<b>Sidfot > Sidfots-id</b><br>Ett unikt id som används som primärnyckel i databasen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#footer" target="_blank">Se sidfotskonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:code','<b>Proj4def > EPSG-kod</b><br>Ett unikt id som används som primärnyckel i databasen samt utgör code i json-konfigurationen.<br><a href="https://origo-map.github.io/origo-documentation/latest/#proj4defs" target="_blank">Se proj4def-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:attributes','<b>Lager > Attribut</b><br>JSON-formaterat fält som anger vilka attribut som ska visas för valt lager.<br><a href="https://origo-map.github.io/origo-documentation/latest/#attributes" target="_blank">Se attributkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_config','<b>Lager > Stilkonfiguration</b><br>Manuell stilkonfiguration (JSON) som huvudsakligen används för vektorlager. Om fältet används för andra typer av lager, så som WMS-lager, inaktiveras den förenklade stilsättningen som använder fälten "Stilfilter", "Ikon", "Utfälld ikon" m fl.<br>Fältet tar en något förenklad JSON jämfört med exemplen i Origos dokumentation. Ett exempel:<br>[[ { "stroke": { "color": "rgba(20,58,100,1.0)" }, "fill": { "color": "rgba(20,58,100,0.5)" } } ]]<br><a href="https://origo-map.github.io/origo-documentation/latest/#style-basics" target="_blank">Se stilkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_layer','<b>Lager > Stillager</b><br>Kan sättas till ett lager-id varifrån stilsättningen hämtas. Inaktiverar annan stilsättning som satts i fälten "Stilkonfiguration", "Stilfilter", "Ikon", "Utfälld ikon" m fl.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:style_filter','<b>Lager > Stilfilter</b><br>Kan innehålla ett sträng som filtererar lagret efter attributvärden. Har ingen effekt om något av fälten "Stillager" eller "Stilkonfiguration" är satta.<br>Exempel (visa företeelser där attributet "signatur" har värdet MW eller MD): <i>[signatur] == ''MW'' OR [signatur] == ''MD''</i><br><a href="https://origo-map.github.io/origo-documentation/latest/#filter" target="_blank">Se filterkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:url','<b>Karta > Url</b><br>Fältet ska innehålla en webblänk till den aktuella kartan och används av knappen "Öppna karta".');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:layers','<b>Karta > Lager</b><br>En kommaseparerad lista med lager-idn för de lager som ska ligga i roten av kartan (utan att ligga i någon undergrupp). Ordningen i listan bestämmer ordningen i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:groups','<b>Karta > Grupper</b><br>En kommaseparerad lista med grupp-idn för de grupper som ska ligga i roten av kartan. Ordningen i listan bestämmer ordningen i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:controls','<b>Karta > Kontroller</b><br>En kommaseparerad lista med kontroll-idn för de kontroller som ska finnas med i kartan. Ordningen spelar roll.<br><a href="https://origo-map.github.io/origo-documentation/latest/#controls-1" target="_blank">Se kontrollkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:proj4defs','<b>Karta > Proj4defs</b><br>En kommaseparerad lista med EPSG-koder för de Proj4-definitioner som ska vara tillgängliga i kartan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:projectioncode','<b>Karta > Projektion</b><br>EPSG-koden för den projektion (koordinatsystem) som kartan ska visas i. Den valda EPSG-koden måste finnas med i proj4defs för kartan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:projectionextent','<b>Proj4def > Projektionsutbredning</b><br>Två koordinatpar som definierar projektionens utbredning. T ex. för EPSG:3008: <i>(573714.68,7702218.01),(-72234.21,6098290.04)</i>. Olika projektionsdefinitioner och deras utbredningar finns tillgängliga på <a href="https://epsg.io/" target="_blank">epsg.io</a>.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:extent','<b>Karta > Utbredning</b><br>Två koordinatpar som definierar kartans utbredning. T ex: <i>(300000,6280000),(-80000,6130000)</i>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:center','<b>Karta > Mittpunkt</b><br>Ett koordinatpar som definierar mittpunkten för kartan vid uppstart. T ex: <i>(191000,6211212)</i>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:palette','<b>Karta > Färgpalett</b><br>Frivilligt, JSON-formaterat fält som definierar valbara färger i kartans ritverktyg etc.<br><a href="https://origo-map.github.io/origo-documentation/latest/#palette" target="_blank">Se palett-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:resolutions','<b>Karta > Upplösningar</b><br>En kommaseparerad lista av upplösningar som definierar kartans olika zoomnivåer. Upplösningarna måste vara kompatibla med kartans bakgrundslager.<br><a href="https://origo-map.github.io/origo-documentation/latest/#resolutions" target="_blank">Se konfiguration av upplösningar</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:constrainresolution','<b>Karta > Upplösningsbegränsad</b><br>Normalt begränsas kartans zoomnivåer av de definierade upplösningarna, men om "Upplösningsbegränsad" sätts till "f" så kan man zooma mer fritt i kartan. Genom att slå av upplösningsbegränsning förhindras effektiv cachning av kartan vilket kan försämra prestandan kraftigt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:show_meta','<b>Karta > Visa metadata</b><br>Om "Visa metadata" är satt till "t" så kommer relevant metadata, som lagts in konfigurationsverktyget, att skrivas in i Origo-konfigurationen när administratören klickar på knappen "Skriv kartkonfiguration". Metadata för kartans lager blir då synlig i kartans lagerträd.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:css_files','<b>Karta > CSS-filer</b><br>En kommaseparerad lista med CSS-stilfiler som ska laddas av kartan. Filer som anges med en relativ sökväg (t ex. css/style.css) länkas in i kartans html-fil. Filer som anges med en absolut sökväg (t ex. http://kartor.kristianstad.se/origo/kristianstadskartan/css/style.css) kopieras in i sin helhet i kartans html-fil.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:css','<b>Karta > CSS</b><br>CSS-kod som inkluderas direkt i kartans html-dokument.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:js_files','<b>Karta > JS-filer</b><br>En kommaseparerad lista med javascriptfiler som ska laddas av kartan. Filer som anges med en relativ sökväg (t ex. js/origo.min.js) länkas in i kartans html-fil. Filer som anges med en absolut sökväg (t ex. http://kartor.kristianstad.se/origo/kristianstadskartan/js/origo.min.js) kopieras in i sin helhet i kartans html-fil.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:js','<b>Karta > JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument. Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:abstract','<b>Karta > Beskrivning</b><br>En informativ text för administratörer som beskriver kartan och dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:title','<b>Karta > Titel</b><br>Titeln på kartan. Skrivs in i kartans html-dokument.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:icon','<b>Karta > Genvägsikon</b><br>Kartans genvägsikon (shortcut icon). Skrivs in i kartans html-dokument.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:footer','<b>Karta > Sidfot</b><br>Välj en fördefinierad sidfot för kartan eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:tilegrid','<b>Karta > Tilegrid</b><br>Välj en fördefinierad tilegrid för kartan eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:zoom','<b>Karta > Zoom</b><br>Den zoomnivå som kartan ska laddas i vid uppstart. Anges som ett heltal, där ett högre värde innebär en mer inzoomad karta.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:mapgrid','<b>Karta > Visa rutnät</b><br>Om "Visa rutnät" är satt till "t" kommer ett rutnät ritas bakom befintliga kartlager, annars är bakgrunden enfärgat vit.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:enablerotation','<b>Karta > Roterbar</b><br>Om "Roterbar" är satt till "t" är det möjligt att rotera hela kartvyn, så att väderstrecket längst upp, i mitten inte längre motsvarar norr. Om kartan visas på en pekskärm kan kartvyn vridas med två fingrar, annars behöver man hålla inne alt+shift samtidigt som man vänsterklickar och rör på musen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:embedded','<b>Karta > Inbäddad</b><br>Flera av Origos kartkontroller har möjlighet att ändra utseende om kartan bäddas in på en annan webbsida. Om "Inbäddad" sätts till "f" kommer sådana anpassningar inte längre att göras.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:keywords','<b>Karta > Nyckelord</b><br>Ett informativt fält för administratörer som innehåller en kommaseparerad lista av nyckelord som är associerade med kartan. Om man har ett mycket stort antal kartor kan man låta gruppera dessa efter nyckelord genom att lägga till ''maps'' till filen constants/keywordCategorized.php.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:info','<b>Karta > Info</b><br>Fält för administrativ information, rörande kartan, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:abstract','<b>Kontroll > Beskrivning</b><br>En informativ text för administratörer som beskriver kontrollen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:options','<b>Kontroll > Inställningar</b><br>JSON-formaterat fält med specifika inställningar (options) för vald kontroll.<br><a href="https://origo-map.github.io/origo-documentation/latest/#controls-1" target="_blank">Se kontrollkonfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:info','<b>Kontroll > Info</b><br>Fält för administrativ information, rörande kontrollen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:title','<b>Lager > Titel</b><br>Lagrets titel, som bland annat visas i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:source','<b>Lager > Källa</b><br>Välj lagrets fördefinierade källa, alternativt lämna fältet tomt om lagret är av typen "GROUP" (ett lager samansatt av andra fördefinierade lager). Klicka sedan på knappen "Uppdatera" för att administrationsgränssnittet ska visa rätt inställningsfält.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:type','<b>Lager > Typ</b><br>Välj det format som ska läsas från källan och klicka sedan på knappen "Uppdatera" för att administrationsgränssnittet ska visa rätt inställningsfält.<br>Se "type" under aktuellt format i <a href="https://origo-map.github.io/origo-documentation/latest/#layers-1" target="_blank">Origo-dokumentationen</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:tiled','<b>Lager > Tiled</b><br>Om "Tiled" sätts till "t" kommer renderMode vara tiled, annars kommer renderMode vara image. Ett lager behöver vara tiled för att kunna cachas effektivt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:info','<b>Lager > Info</b><br>Fält för administrativ information, rörande lagret, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:layers','<b>Grupp > Lager</b><br>En kommaseparerad lista med lager-idn för de lager som ska ligga i gruppen. Ordningen i listan bestämmer ordningen i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:groups','<b>Grupp > Grupper</b><br>En kommaseparerad lista med grupp-idn för de grupper som ska utgöra undergrupper till den aktuella gruppen. Ordningen i listan bestämmer ordningen i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:title','<b>Grupp > Titel</b><br>Gruppens titel, som visas i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:info','<b>Grupp > Info</b><br>Fält för administrativ information, rörande gruppen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:show_meta','<b>Grupp > Visa metadata</b><br>Om "Visa metadata" är satt till "f" kommer aldrig metadata för gruppens lager eller undergrupper att skrivas in i Origo-konfigurationen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:keywords','<b>Lager > Nyckelord</b><br>En kommaseparerad lista av nyckelord som är associerade med det aktuella lagret. Nyckelorden används bland annat för att gruppera lager i det administrativa verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:keywords','<b>Grupp > Nyckelord</b><br>En kommaseparerad lista av nyckelord som är associerade med den aktuella gruppen. Nyckelorden används för att gruppera grupper i det administrativa verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:format','<b>Lager > Format</b><br>Det bildformat som lagret ska vara i. Standard, om inget anges, är image/png. Andra vanliga format är image/jpeg eller image/png;mode=8bit.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:featureinfolayer','<b>Lager > FeatureInfo-lager</b><br>Namnet på ett lager som ska användas vid GetFeatureInfo-anrop. Frivilligt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:show_meta','<b>Lager > Visa metadata</b><br>Om "Visa metadata" är satt till "f" kommer aldrig metadata för lagret att skrivas in i Origo-konfigurationen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:abstract','<b>Lager > Beskrivning</b><br>En kort, informativ text (eller html-kod) som beskriver lagret och visas i lagerträdet. Visas även om "Visa metadata" är satt till "f".');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:abstract','<b>Grupp > Beskrivning</b><br>En kort, informativ text (eller html-kod) som beskriver lagergruppen och visas i lagerträdet. Visas även om "Visa metadata" är satt till "f".');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:service','<b>Källa > Tjänst</b><br>Välj källans fördefinierade tjänst och klicka på knappen "Uppdatera" för att administrationsgränssnittet ska visa rätt inställningsfält.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('format:format_id','<b>Format > Format</b><br>Namnet på ett format som kan tillgängliggöras av en eller flera tjänster. Några exempel på vanliga format: WMS, WFS, GEOJSON. (Under lager väljer man ett tillgängligt format som "typ".)');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:formats','<b>Tjänst > Tillgängliga format</b><br>En kommaseparerad lista med format som tjänsten tillgängliggör eller publicerar. (För de lager som läser från tjänsten väljs sedan ett av formaten som "typ".)');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:layers','<b>Lager > Lager</b><br>En kommaseparerad lista med lager-idn för de lager som ska ingå i GROUP-lagret. De ingående lagren måste ha parametern "Synlig" satt till "t".<br><a href="https://origo-map.github.io/origo-documentation/latest/#group" target="_blank">Se GROUP-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:show_icon','<b>Lager > Visa ikon</b><br>Om "Visa ikon" är satt till "f" kommer en generisk, grå listikon att visas bredvid lagernamnet i lagerträdet. Annars kommer ikonen sättas utifrån "ikon"-fältet, alternativt autogenereras. "Visa ikon" måste vara satt till "f" för att fältet "Regelbaserad visning" ska visas i verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:featureinfooptions','<b>Karta > FeatureInfoOptions</b><br>JSON-formaterat fält med featureInfoOptions.<br><a href="https://origo-map.github.io/origo-documentation/latest/#featureinfooptions" target="_blank">Se featureInfoOptions-konfiguration</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('group:expanded','<b>Grupp > Expanderad</b><br>Om "Expanderad" är satt till "t" kommer gruppen initialt visas i utfällt läge i lagerträdet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:with_geometry','<b>Källa > With_geometry</b><br>Om "With_geometry" är satt till "t" så kommer with_geometry=true att läggas till som en parameter i anropen till kartservern.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:fi_point_tolerance','<b>Källa > Fi_point_tolerance</b><br>Om "Fi_point_tolerance" är satt så kommer fi_point_tolerance=värde att läggas till som en parameter i anropen till kartservern. "Fi_point_tolerance" skall anges som ett heltal.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:ttl','<b>Källa > Ttl</b><br>Om "Ttl" är satt så kommer ttl=värde att läggas till som en parameter i anropen till kartservern. Syftet med "Ttl" är att meddela en webcache på servern, som t ex Varnish, hur länge svaret ska cachas. (Kräver särskilld anpassning på servern.)');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:tilegrid','<b>Källa > Tilegrid</b><br>Välj en fördefinierad tilegrid för källan eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:contact','<b>Källa > Kontakt</b><br>Välj en fördefinierad kontakt för källan eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:abstract','<b>Källa > Beskrivning</b><br>En informativ text för administratörer som beskriver källan och dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:info','<b>Källa > Info</b><br>Fält för administrativ information, rörande källan, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:type','<b>Tjänst > Typ</b><br>Välj typ av tjänst och klicka på knappen "Uppdatera" för att administrationsgränssnittet ska visa rätt inställningsfält. Välj "File" om data läses direkt från fil, t ex GeoJSON.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:abstract','<b>Tjänst > Beskrivning</b><br>En informativ text för administratörer som beskriver tjänsten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:info','<b>Tjänst > Info</b><br>Fält för administrativ information, rörande tjänsten, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:restricted','<b>Tjänst > Rättighetsstyrd</b><br>Ett fält som indikerar huruvida åtkomst av tjänsten är rättighetsstyrd. Om "Rättighetsstyrd" är satt till "t" kommer det, under lager som läser från tjänsten, att visas fält som kan användas för rättighetsstyrning.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('tilegrid:abstract','<b>Tilegrid > Beskrivning</b><br>En informativ text för administratörer som beskriver tilegriden.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('tilegrid:info','<b>Tilegrid > Info</b><br>Fält för administrativ information, rörande tilegriden, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:abstract','<b>Sidfot > Beskrivning</b><br>En informativ text för administratörer som beskriver sidfoten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:info','<b>Sidfot > Info</b><br>Fält för administrativ information, rörande sidfoten, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('format:abstract','<b>Format > Beskrivning</b><br>En informativ text för administratörer som beskriver formatet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('format:info','<b>Format > Info</b><br>Fält för administrativ information, rörande formatet, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:abstract','<b>Proj4def > Beskrivning</b><br>En informativ text för administratörer som beskriver proj4-definitionen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:info','<b>Proj4def > Info</b><br>Fält för administrativ information, rörande proj4-definitionen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('help:info','<b>Hjälp > Info</b><br>Fält för administrativ information, rörande hjälpen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:softversion','<b>Källa > Programversion</b><br>Ett informativt fält där man kan ange relevant versionsnummer för bakomliggande data- eller projektfil. Fältet måste ajourhållas manuellt. För Qgis Server-källor får man alltid upp korrekt version när man klickar på "Info"-knappen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:updated','<b>Källa > Uppdaterad (åååå-mm-dd)</b><br>Ett informativt fält där man kan ange när källan senast uppdaterades. Fältet måste ajourhållas manuellt. För Qgis Server-källor får man alltid upp korrekt uppdaterad-information när man klickar på "Info"-knappen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:history','<b>Källa > Tillkomsthistorik</b><br>Ett informativt fält där man kan skriva in information om hur data skapades/togs fram.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('service:base_url','<b>Tjänst > Huvudurl</b><br>En generell, inledande sökväg till de datakällor som tjänsten erbjuder.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('source:file','<b>Källa > Fil</b><br>Ange en datafil, med sökväg, som är åtkomlig på/från webbservern där Origo ligger.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:queryable','<b>Lager > Klickbar</b><br>Om "Klickbar" är satt till "f" kommer ingen information (featureinfo) visas när man klickar på lagrets objekt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:visible','<b>Lager > Synlig</b><br>Om "Synlig" är satt till "t" kommer lagret vara tänt när man laddar kartan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:exportable','<b>Lager > Exporterbar</b><br>Om "Exporterbar" är satt till "f" kommer lagret inte exporteras av exportverktyget. Inställningen hindrar dock inte åtkomst av bakomliggande tjänster (t ex WFS).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:opacity','<b>Lager > Opacitet</b><br>Ett decimaltal som anger hur genomskinligt lagret ska vara initialt. Värdet 1.00 anger att lagret ska vara helt ogenomskinligt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:swiper','<b>Lager > Swiper-lager</b><br>Om "Swiper-lager" är satt till "t" kommer lagret att visas i swiper-verktygets lagerlista. Om "Swiper-lager" är satt till "under" kommer lagret användas som det bakomliggande lagret i swiper-verktyget.<br><a href="https://github.com/SigtunaGIS/swiper-plugin/blob/main/README.md" target="_blank">Se Swiper-plugin</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:icon','<b>Lager > Ikon</b><br>En bild som visas bredvid lagret i lagerträdet. Om ingen ikon anges skapas en automatiskt. Ange en bildfil, med sökväg, som är åtkomlig på/från webbservern där Origo ligger.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:icon_extended','<b>Lager > Utfälld ikon</b><br>En bild som visas i lagerträdet när man klickar på utökad information för lagret. Om ingen ikon anges skapas en automatiskt. Ange en bildfil, med sökväg, som är åtkomlig på/från webbservern där Origo ligger.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:minscale','<b>Lager > Minskala</b><br>Minsta skala då lagret är synligt (frivilligt). Lager som ligger utanför sitt skalintervall gråmarkeras i legenden.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:maxscale','<b>Lager > Maxskala</b><br>Största skala då lagret är synligt (frivilligt). Lager som ligger utanför sitt skalintervall gråmarkeras i legenden.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:attribution','<b>Lager > Tillskrivning</b><br>Cypyright-text eller liknande. Visas i sidfoten, samt vid utskrift, när lagret är tänt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:contact','<b>Lager > Kontakt</b><br>Välj en fördefinierad kontakt för lagret eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:origin','<b>Lager > Ursprungskälla</b><br>Välj en fördefinierad ursprungskälla för lagret eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:update','<b>Lager > Uppdatering</b><br>Välj en fördefinierad uppdateringstyp för lagret eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:history','<b>Lager > Tillkomsthistorik</b><br>Ett informativt fält där man kan skriva in information om hur lagret skapades.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:updated','<b>Lager > Uppdaterad (åååå-mm-dd)</b><br>Ett informativt fält där man kan ange när lagret senast uppdaterades. Fältet måste ajourhållas manuellt. (För Qgis Server-<u>källor</u> får man alltid upp korrekt uppdaterad-information när man klickar på "Info"-knappen).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:resources','<b>Lager > Resurser</b><br>Ett informativt textfält där man kan skriva in de resurser som använts för att bygga upp lagret, som t ex. rasterfiler, datafiler, databastabeller eller externa tjänster.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:web','<b>Lager > Webbsida</b><br>En länk till en webbsida som t ex. innehåller mer information rörande lagret eller dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:tables','<b>Lager > Tabeller</b><br>Ett automatgenererat fält med en lista på de databastabeller som bygger upp lagret. Fältet visas endast för lager som kommer från en Qgis Server-tjänst och uppdateras när man klickar på Uppdatera-knappen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:show_iconext','<b>Lager > Visa utfälld ikon</b><br>Om fältet "Utfälld ikon" inte är ifyllt kommer bilden som visas när man klickar på mer information om lagret i lagerträdet att automatgenereras. Om man sätter "Visa utfälld ikon" till "f" kommer den vanliga, enkla ikonen att visas även i utfällt läge.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:exports','<b>Lager > Exportlager</b><br>En kommaseparerad lista med lager-idn för de lager som ska ingå vid export med exportverktyget. Frivilligt');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:thematicstyling','<b>Lager > Regelbaserad visning</b><br>Om "Regelbaserad visning" är satt till "t" kommer det vara möjligt att fälla ut ikonen bredvid lagernamnet och sedan tända/släcka det aktuella lagrets olika symboler. Alternativet "Regelbaserad visning" visas endast om "Visa ikon" är satt till "f".');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('database:database_id','<b>Databas > Id</b><br>Namnet på en Postgresql-databas. Används som primärnyckel av verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('database:connectionstring','<b>Databas > Anslutningssträng</b><br>Anslutningssträng som används av PHP för att ansluta mot Postgresql-databasen.<br><a href="https://www.php.net/manual/en/function.pg-connect.php" target="_blank">Se pg_connect</a>');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('database:abstract','<b>Databas > Beskrivning</b><br>En informativ text för administratörer som beskriver databasen och dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('database:info','<b>Databas > Info</b><br>Fält för administrativ information, rörande databasen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:schema_id','<b>Schema > Id</b><br>Ett unikt id som används som primärnyckel av verktyget och som består av ett databas-id och ett schema-namn, formaterat enligt nedan:<br>databas-id.schema-namn (t ex. geodata.public)');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:abstract','<b>Schema > Beskrivning</b><br>En informativ text för administratörer som beskriver schemat och dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:keywords','<b>Schema > Nyckelord</b><br>En kommaseparerad lista av nyckelord som är associerade med det aktuella schemat. Nyckelorden används för att gruppera scheman i det administrativa verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:contact','<b>Schema > Kontakt</b><br>Välj en fördefinierad kontakt för schemat eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:origin','<b>Schema > Ursprungskälla</b><br>Välj en fördefinierad ursprungskälla för schemat eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:update','<b>Schema > Uppdatering</b><br>Välj en fördefinierad uppdateringstyp för schemat eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:updated','<b>Schema > Uppdaterad (åååå-mm-dd)</b><br>Ett informativt fält där man kan ange när schemat senast uppdaterades. Fältet måste ajourhållas manuellt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('schema:info','<b>Schema > Info</b><br>Fält för administrativ information, rörande schemat, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:table_id','<b>Tabell > Id</b><br>Ett unikt id som används som primärnyckel av verktyget och som består av ett schema-id (inklusive databas-id) och ett tabell-namn, formaterat enligt nedan:<br>schema-id.tabell-namn (t ex. geodata.public.table1)');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:abstract','<b>Tabell > Beskrivning</b><br>En informativ text för administratörer som beskriver tabellen och dess innehåll.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:keywords','<b>Tabell > Nyckelord</b><br>En kommaseparerad lista av nyckelord som är associerade med den aktuella tabellen. Nyckelorden används för att gruppera tabeller i det administrativa verktyget.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:contact','<b>Tabell > Kontakt</b><br>Välj en fördefinierad kontakt för tabellen eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:origin','<b>Tabell > Ursprungskälla</b><br>Välj en fördefinierad ursprungskälla för tabellen eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:updated','<b>Tabell > Uppdaterad</b><br>Ett readonly-fält som visar när tabellen senast uppdaterades i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:update','<b>Tabell > Uppdatering</b><br>Välj en fördefinierad uppdateringstyp för tabellen eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:history','<b>Tabell > Tillkomsthistorik</b><br>Ett informativt fält där man kan skriva in information om hur tabellen skapades.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('table:info','<b>Tabell > Info</b><br>Fält för administrativ information, rörande tabellen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:contact_id','<b>Kontakt > Id</b><br>Ett unikt id som används som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:name','<b>Kontakt > Namn</b><br>Kontaktens namn.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:web','<b>Kontakt > Webbsida</b><br>Adress till eventuell webbsida för kontakten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:email','<b>Kontakt > E-mail</b><br>Eventuell e-postadress till kontakten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:abstract','<b>Kontakt > Beskrivning</b><br>En informativ text för administratörer som beskriver kontakten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('contact:info','<b>Kontakt > Info</b><br>Fält för administrativ information, rörande kontakten, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:origin_id','<b>Ursprungskälla > Id</b><br>Ett unikt id som används som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:name','<b>Ursprungskälla > Namn</b><br>Ursprungskällans namn.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:web','<b>Ursprungskälla > Webbsida</b><br>Adress till eventuell webbsida för ursprungskällan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:email','<b>Ursprungskälla > E-mail</b><br>Eventuell e-postadress till ursprungskällan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:abstract','<b>Ursprungskälla > Beskrivning</b><br>En informativ text för administratörer som beskriver ursprungskällan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('origin:info','<b>Ursprungskälla > Info</b><br>Fält för administrativ information, rörande ursprungskällan, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:update_id','<b>Uppdateringsrutin > Id</b><br>Ett unikt id som används som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:name','<b>Uppdateringsrutin > Namn</b><br>Ett namn på uppdateringsrutinen som även fungerar som en kortfattad beskrivning av densamma.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:interval','<b>Uppdateringsrutin > Intervall</b><br>Välj ett av de fördefinierade uppdateringsintervallen eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:method','<b>Uppdateringsrutin > Metod</b><br>Välj ett av de fördefinierade uppdateringsmetoderna eller lämna fältet tomt.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:abstract','<b>Uppdateringsrutin > Beskrivning</b><br>En informativ text för administratörer som beskriver uppdateringsrutinen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('update:info','<b>Uppdateringsrutin > Info</b><br>Fält för administrativ information, rörande uppdateringsrutinen, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('keyword:keyword_id','<b>Nyckelord > Id</b><br>Ett nyckelord som bland annat kan användas för att skapa en logisk gruppering av t ex. lager. Används även som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('keyword:abstract','<b>Nyckelord > Beskrivning</b><br>En informativ text för administratörer som beskriver nyckelordet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('keyword:info','<b>Nyckelord > Info</b><br>Fält för administrativ information, rörande nyckelordet, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:projection','<b>Proj4def > Projektion</b><br>En textsträng som utgör en proj4-projektionsdefinition. Olika projektionsdefinitioner finns tillgängliga på <a href="https://epsg.io/" target="_blank">epsg.io</a>.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('proj4def:alias','<b>Proj4def > Alias</b><br>Ett etablerat namn på projektionen som, om den är angiven, visas i kartan istället för den mer kryptiska EPSG-koden. t ex. SWEREF 99 TM (EPSG:3006).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('tilegrid:tilesize','<b>Tilegrid > Tile-storlek</b><br>Storleken på genererade tiles. Kan anges som en siffra (t ex. 512), eller som en array (t ex. [ 512,512 ]). Standardstorlek är [ 256,256 ].');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:img','<b>Sidfot > Logotyp</b><br>En bild som visas i sidfoten. Ange en bildfil, med sökväg, som är åtkomlig på/från webbservern där Origo ligger.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:url','<b>Sidfot > Url</b><br>En webblänk som öppnas i ett nytt fönster om man klickar på logotypen eller texten i sidfoten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('footer:text','<b>Sidfot > Text</b><br>En textsträng som skrivs ut i sidfoten.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:layertype','<b>Lager > WFS-typ</b><br>Välj hur WFS-lagret ska renderas: vector, cluster eller image. Om inget har valts renderas lagret som vektor (vector).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:editable','<b>Lager > Redigerbar</b><br>Sätt "Redigerbar" till "t" om lagret ska kunna redigeras i kartan. Kräver att kartan har editor-kontrollen och att källan stödjer WFS-T.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:allowededitoperations','<b>Lager > Redigeringsalt.</b><br>En kommaseparerad lista av redigeringsvertyg som ska vara tillgängliga för lagret. Möjliga värden är: updateAttributes , updateGeometry , create , delete. Om inget anges kommer samtliga redigeringsverktyg vara tillgängliga för lagret. Kräver att "Redigerbar" är satt till "t" för lagret.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:geometryname','<b>Lager > Geometrinamn</b><br>Namnet på attributet som innehåller geometrin (defaultvärde: geom).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('layer:geometrytype','<b>Lager > Geometrityp</b><br>Lagrets geometrityp.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:searchengineindexable','<b>Karta > Kan indexeras av sökmotorer</b><br>Om "Kan indexeras av sökmotorer" är satt till "t" kommer det genereras metadata i json-format (structured data), specifikt ämnad för sökmotorer såsom google och bing. Dessutom kommer det skapas en sitemap.xml-fil i samma mapp som index.html. För att detta ska vara meningsfullt behöver kartan vara fullt tillgänglig på internet och att webbservern har en robots.txt som länkar till sitemap-filen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:new_id','<b>Nyhet > Id</b><br>Ett unikt id som används som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:abstract','<b>Nyhet > Beskrivning</b><br>En kort text som beskriver nyheten och som visas i nyhetslistan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:text','<b>Nyhet > Text</b><br>Nyhetstext som visas när man klickar på tillhörande beskrivning i nyhetslistan. Kan innehålla html-kod.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:date','<b>Nyhet > Skapad</b><br>Tidpunkten då nyheten skapades. Bestämmer ordningen i nyhetslistan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:reads','<b>Nyhet > Läst av</b><br>En kommaseparerad lista av AD-användare som har klickat på nyhetens beskrivning i nyhetslistan. För användare i listan visas beskrivningen inte längre i fetstil.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:deletes','<b>Nyhet > Raderad av</b><br>En kommaseparerad lista av AD-användare som har klickat på nyhetens radera-ikon i nyhetslistan. För användare i listan visas nyheten inte längre i nyhetslistan.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('new:info','<b>Nyhet > Info</b><br>Fält för administrativ information, rörande nyheten, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:js','<b>Kontroll > JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument (placeras efter kartspecifik JS). Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:plugin_id','<b>Plugin > Id</b><br>Ett unikt id som används som primärnyckel i databasen.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:abstract','<b>Plugin > Beskrivning</b><br>En informativ text för administratörer som beskriver pluginet.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:css','<b>Plugin > CSS</b><br>CSS-kod som inkluderas direkt i kartans html-dokument (placeras efter kartspecifik CSS och CSS från kontroller).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:css_files','<b>Plugin > CSS-filer</b><br>En kommaseparerad lista med CSS-stilfiler som ska laddas av kartan (placeras efter kartspecifika CSS-filer). Filer som anges med en relativ sökväg (t ex. css/style.css) länkas in i kartans html-fil. Filer som anges med en absolut sökväg (t ex. http://kartor.kristianstad.se/origo/kristianstadskartan/css/style.css) kopieras in i sin helhet i kartans html-fil.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:info','<b>Plugin > Info</b><br>Fält för administrativ information, rörande pluginet, som inte passar in någon annanstans.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:js','<b>Plugin > JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument (placeras efter kartspecifik JS och JS från kontroller). Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.<br><br><b>Exempel för Swiper-plugin:</b><br>origo.on(''load'', function (viewer) {<br>&emsp;const swiper = Swiper({<br>&emsp;&emsp;origoConfig: ''../index.json'',<br>&emsp;&emsp;circleRadius: 100,<br>&emsp;&emsp;alwaysOnTop: true,<br>&emsp;&emsp;initialLayer: ''orto016'',<br>&emsp;&emsp;initialControl: ''swipe'',<br>&emsp;&emsp;showLayerListOnStart: true,<br>&emsp;&emsp;tooltips: {<br>&emsp;&emsp;&emsp;swiper: ''Jämför kartvyer'',<br>&emsp;&emsp;&emsp;swipeBetweenLayers: ''Jämför sida-sida'',<br>&emsp;&emsp;&emsp;circleSwipe: ''Jämför med kikhål'',<br>&emsp;&emsp;&emsp;layerList: ''Välj lager från lista''<br>&emsp;&emsp;}<br>&emsp;});<br>&emsp;viewer.addComponent(swiper);<br>});');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:js_files','<b>Plugin > JS-filer</b><br>En kommaseparerad lista med javascriptfiler som ska laddas av kartan (placeras efter kartspecifika JS-filer). Filer som anges med en relativ sökväg (t ex. js/origo.min.js) länkas in i kartans html-fil. Filer som anges med en absolut sökväg (t ex. http://kartor.kristianstad.se/origo/kristianstadskartan/js/origo.min.js) kopieras in i sin helhet i kartans html-fil.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:css','<b>Kontroll > CSS</b><br>CSS-kod som inkluderas direkt i kartans html-dokument (placeras efter kartspecifik CSS).');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('plugin:onload','<b>Plugin > Origo.on(load)-JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument inuti en origo.on(''load'', function (viewer) { }) som delas med karta och kontroller. Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.<br><br><b>Exempel för Swiper-plugin:</b><br>const swiper = Swiper({<br>&emsp;origoConfig: ''../index.json'',<br>&emsp;circleRadius: 100,<br>&emsp;alwaysOnTop: true,<br>&emsp;initialLayer: ''orto016'',<br>&emsp;initialControl: ''swipe'',<br>&emsp;showLayerListOnStart: true,<br>&emsp;tooltips: {<br>&emsp;&emsp;swiper: ''Jämför kartvyer'',<br>&emsp;&emsp;swipeBetweenLayers: ''Jämför sida-sida'',<br>&emsp;&emsp;circleSwipe: ''Jämför med kikhål'',<br>&emsp;&emsp;layerList: ''Välj lager från lista''<br>&emsp;}<br>});<br>viewer.addComponent(swiper);');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('control:onload','<b>Kontroll > Origo.on(load)-JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument inuti en origo.on(''load'', function (viewer) { }) som delas med karta och plugins. Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.');
INSERT INTO map_configs.helps(help_id,abstract) VALUES ('map:onload','<b>Karta > Origo.on(load)-JS</b><br>Javascript-kod som inkluderas direkt i kartans html-dokument inuti en origo.on(''load'', function (viewer) { }) som delas med kontroller och plugins. Förutom variabeln "origo" så är konstanterna "urlParams", "hashParams" samt funktionen "getUrlParam(param)" redan definierade.');
