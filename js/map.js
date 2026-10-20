const MAP_WIDTH = 2399;
const DOT_RADIUS = 26;
const METRES_PER_PIXEL = 0.2;
const WALK_METRES_PER_MIN = 80;

function el(id) { return document.getElementById(id); }

const dotLayer   = el('dotLayer');
const lineLayer  = el('lineLayer');
const labelLayer = el('labelLayer');
const fromBox    = el('fromBox');
const toBox      = el('toBox');
const message    = el('message');

const viaBox       = el('viaBox');
const stepsSection = el('stepsSection');
const stepsList    = el('stepsList');
const routeTitle   = el('routeTitle');
const chipSteps    = el('chipSteps');
const chipDist     = el('chipDist');
const chipTime     = el('chipTime');

const cardEmpty   = el('cardEmpty');
const cardContent = el('cardContent');
const photoBox    = el('photoBox');
const cardPhoto   = el('cardPhoto');
const cardBadge   = el('cardBadge');
const cardName    = el('cardName');
const cardInfo    = el('cardInfo');

let selectedId = '';

function viaValue() { return viaBox ? viaBox.value : ''; }

function getPlace(id) {
  for (let i = 0; i < places.length; i++) {
    if (places[i].id === id) {
      return places[i];
    }
  }
  return null;
}

function drawDots() {
  let html = '';
  for (let i = 0; i < places.length; i++) {
    const p = places[i];
    if (p.type === 'junction') { continue; }

    html += '<circle class="dot ' + p.type + '" data-id="' + p.id + '"' +
            ' cx="' + p.x + '" cy="' + p.y + '" r="' + DOT_RADIUS + '"' +
            ' tabindex="0" role="button" aria-label="' + p.name + '">' +
            '<title>' + p.name + '</title></circle>';
  }
  dotLayer.innerHTML = html;
}

dotLayer.addEventListener('click', function (event) {
  const id = event.target.getAttribute('data-id');
  if (id) { showCard(id); }
});
dotLayer.addEventListener('keydown', function (event) {
  const id = event.target.getAttribute('data-id');
  if (id && event.key === 'Enter') { showCard(id); }
});

function showCard(id) {
  const place = getPlace(id);
  selectedId = id;

  cardName.textContent = place.name;
  cardBadge.textContent = (place.type === 'gate') ? 'Gate' : 'Building';
  cardInfo.textContent = place.info;

  photoBox.classList.remove('missing', 'portrait');

  cardPhoto.onload = function () {
    photoBox.classList.toggle('portrait', cardPhoto.naturalHeight > cardPhoto.naturalWidth);
  };
  cardPhoto.onerror = function () {
    photoBox.classList.remove('portrait');
    photoBox.classList.add('missing');
  };

  if (place.photo) {
    cardPhoto.src = place.photo;
    cardPhoto.alt = 'Photo of ' + place.name;
  } else {
    photoBox.classList.add('missing');
  }

  cardEmpty.hidden = true;
  cardContent.hidden = false;

  markChosen(id);
  showLabel(place);
}

function markChosen(id) {
  const dots = dotLayer.querySelectorAll('.dot');
  for (let i = 0; i < dots.length; i++) {
    dots[i].classList.toggle('chosen', dots[i].getAttribute('data-id') === id);
  }
}

function showLabel(place) {
  const width = place.name.length * 24 + 70;
  const height = 80;
  let x = place.x - width / 2;
  x = Math.max(10, Math.min(MAP_WIDTH - width - 10, x));
  const y = (place.y < 160) ? place.y + 50 : place.y - 50 - height;

  labelLayer.innerHTML =
    '<g class="mapLabel">' +
    '<rect x="' + x + '" y="' + y + '" width="' + width + '" height="' + height + '" rx="40"></rect>' +
    '<text x="' + (x + width / 2) + '" y="' + (y + 54) + '">' + place.name + '</text>' +
    '</g>';
}

function fillDropdowns() {
  let list = '';
  for (let i = 0; i < places.length; i++) {
    if (places[i].type !== 'junction') {
      list += '<option value="' + places[i].id + '">' + places[i].name + '</option>';
    }
  }
  fromBox.innerHTML = '<option value="">Choose a place</option>' + list;
  toBox.innerHTML   = '<option value="">Choose a place</option>' + list;
  if (viaBox) {
    viaBox.innerHTML = '<option value="">No stop</option>' + list;
  }
}

function colourDots() {
  const dots = dotLayer.querySelectorAll('.dot');
  for (let i = 0; i < dots.length; i++) {
    const id = dots[i].getAttribute('data-id');
    dots[i].classList.toggle('start', id === fromBox.value);
    dots[i].classList.toggle('end', id === toBox.value);
    dots[i].classList.toggle('via', viaValue() !== '' && id === viaValue());
  }
}

el('setFromBtn').addEventListener('click', function () {
  fromBox.value = selectedId;
  colourDots();
});
el('setToBtn').addEventListener('click', function () {
  toBox.value = selectedId;
  colourDots();
});
if (el('setViaBtn') && viaBox) {
  el('setViaBtn').addEventListener('click', function () {
    viaBox.value = selectedId;
    colourDots();
  });
}

fromBox.addEventListener('change', colourDots);
toBox.addEventListener('change', colourDots);
if (viaBox) { viaBox.addEventListener('change', colourDots); }

if (el('swapBtn')) {
  el('swapBtn').addEventListener('click', function () {
    const temp = fromBox.value;
    fromBox.value = toBox.value;
    toBox.value = temp;
    colourDots();
  });
}

function findNeighbours(placeId) {
  const result = [];
  for (let i = 0; i < paths.length; i++) {
    if (paths[i].from === placeId) { result.push(paths[i].to); }
    if (paths[i].to === placeId)   { result.push(paths[i].from); }
  }
  return result;
}

function findRoute(startId, endId) {
  const queue = [[startId]];
  const visited = [startId];

  while (queue.length > 0) {
    const route = queue.shift();
    const lastId = route[route.length - 1];

    if (lastId === endId) { return route; }

    const neighbours = findNeighbours(lastId);
    for (let i = 0; i < neighbours.length; i++) {
      if (!visited.includes(neighbours[i])) {
        visited.push(neighbours[i]);
        queue.push(route.concat(neighbours[i]));
      }
    }
  }
  return null;
}

function findTrip(startId, viaId, endId) {
  if (viaId === '') { return findRoute(startId, endId); }

  const firstPart  = findRoute(startId, viaId);
  const secondPart = findRoute(viaId, endId);
  if (firstPart === null || secondPart === null) { return null; }

  return firstPart.concat(secondPart.slice(1));
}

function getNote(idA, idB) {
  for (let i = 0; i < paths.length; i++) {
    if (paths[i].from === idA && paths[i].to === idB) { return paths[i].note; }
    if (paths[i].from === idB && paths[i].to === idA) { return paths[i].back || paths[i].note; }
  }
  return '';
}

function getPathPoints(idA, idB) {
  for (let i = 0; i < paths.length; i++) {
    if (paths[i].from === idA && paths[i].to === idB) {
      return (paths[i].bend || []).slice();
    }
    if (paths[i].from === idB && paths[i].to === idA) {
      return (paths[i].bend || []).slice().reverse();
    }
  }
  return [];
}

function getRoutePoints(route) {
  const first = getPlace(route[0]);
  const points = [[first.x, first.y]];

  for (let i = 0; i < route.length - 1; i++) {
    const bends = getPathPoints(route[i], route[i + 1]);
    for (let j = 0; j < bends.length; j++) {
      points.push(bends[j]);
    }
    const next = getPlace(route[i + 1]);
    points.push([next.x, next.y]);
  }
  return points;
}

function getDistance(route) {
  const points = getRoutePoints(route);
  let pixels = 0;
  for (let i = 0; i < points.length - 1; i++) {
    const dx = points[i + 1][0] - points[i][0];
    const dy = points[i + 1][1] - points[i][1];
    pixels += Math.sqrt(dx * dx + dy * dy);
  }
  return Math.round(pixels * METRES_PER_PIXEL);
}

function drawLine(route) {
  const points = getRoutePoints(route);
  let text = '';
  for (let i = 0; i < points.length; i++) {
    text += points[i][0] + ',' + points[i][1] + ' ';
  }
  lineLayer.innerHTML =
    '<polyline class="routeCasing" pathLength="1" points="' + text + '"></polyline>' +
    '<polyline class="routeLine" pathLength="1" points="' + text + '"></polyline>';
}

function showSteps(route, viaId) {
  if (!stepsSection) { return; }

  const from = getPlace(route[0]);
  const to = getPlace(route[route.length - 1]);
  const metres = getDistance(route);
  const minutes = Math.max(1, Math.round(metres / WALK_METRES_PER_MIN));

  if (routeTitle) {
    routeTitle.textContent = from.name + ' to ' + to.name;
    if (viaId !== '') { routeTitle.textContent += ' via ' + getPlace(viaId).name; }
  }
  if (chipSteps) { chipSteps.textContent = (route.length - 1) + ' steps'; }
  if (chipDist)  { chipDist.textContent = 'About ' + metres + ' m'; }
  if (chipTime)  { chipTime.textContent = '~' + minutes + ' min walk'; }

  stepsList.innerHTML = '';
  for (let i = 0; i < route.length - 1; i++) {
    const item = document.createElement('li');
    item.textContent = getNote(route[i], route[i + 1]);
    stepsList.appendChild(item);

    if (viaId !== '' && route[i + 1] === viaId) {
      const stop = document.createElement('li');
      stop.className = 'stop';
      stop.textContent = 'Stop at ' + getPlace(viaId).name;
      stepsList.appendChild(stop);
    }
  }

  const arrive = document.createElement('li');
  arrive.className = 'arrive';
  arrive.textContent = 'You have arrived at ' + to.name;
  stepsList.appendChild(arrive);

  stepsSection.hidden = false;
  stepsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function clearRoute() {
  lineLayer.innerHTML = '';
  message.textContent = '';
  if (stepsList)    { stepsList.innerHTML = ''; }
  if (stepsSection) { stepsSection.hidden = true; }
}

el('routeForm').addEventListener('submit', function (event) {
  event.preventDefault();
  const viaId = viaValue();

  if (fromBox.value === '' || toBox.value === '') {
    message.textContent = 'Please choose a start and a destination.';
    return;
  }
  if (fromBox.value === toBox.value) {
    message.textContent = 'Start and destination must be different.';
    return;
  }
  if (viaId !== '' && (viaId === fromBox.value || viaId === toBox.value)) {
    message.textContent = 'The stop must be different from the start and the destination.';
    return;
  }

  const route = findTrip(fromBox.value, viaId, toBox.value);
  if (route === null) {
    clearRoute();
    message.textContent = 'No route found between these places.';
    return;
  }

  message.textContent = '';
  drawLine(route);
  showSteps(route, viaId);
});

el('clearBtn').addEventListener('click', function () {
  fromBox.value = '';
  toBox.value = '';
  if (viaBox) { viaBox.value = ''; }
  clearRoute();
  colourDots();
});

drawDots();
fillDropdowns();
