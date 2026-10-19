// ===== Settings you can change =====
const MAP_WIDTH = 2399;            // Updated to match your map size
const METRES_PER_PIXEL = 0.6;      
const WALK_METRES_PER_MIN = 80;    

// ===== Part A: find the page elements =====
const dotLayer   = document.getElementById('dotLayer');
const lineLayer  = document.getElementById('lineLayer');
const labelLayer = document.getElementById('labelLayer');
const fromBox    = document.getElementById('fromBox');
const toBox      = document.getElementById('toBox');
const message    = document.getElementById('message');

const cardEmpty   = document.getElementById('cardEmpty');
const cardContent = document.getElementById('cardContent');
const photoBox    = document.getElementById('photoBox');
const cardPhoto   = document.getElementById('cardPhoto');
const cardBadge   = document.getElementById('cardBadge');
const cardName    = document.getElementById('cardName');
const cardInfo    = document.getElementById('cardInfo');

let selectedId = '';    

// ===== Part B: look up a place by its id =====
function getPlace(id) {
  for (let i = 0; i < places.length; i++) {
    if (places[i].id === id) {
      return places[i];
    }
  }
  return null;
}

// ===== Part C: draw the dots =====
function drawDots() {
  let html = '';
  for (let i = 0; i < places.length; i++) {
    const p = places[i];
    if (p.type === 'junction') continue;
    
    html += '<circle class="dot ' + p.type + '" data-id="' + p.id + '"' +
            ' cx="' + p.x + '" cy="' + p.y + '" r="9"' +
            ' tabindex="0" role="button" aria-label="' + p.name + '">' +
            '<title>' + p.name + '</title></circle>';
  }
  dotLayer.innerHTML = html;
}

dotLayer.addEventListener('click', function (event) {
  const id = event.target.getAttribute('data-id');
  if (id) showCard(id);
});
dotLayer.addEventListener('keydown', function (event) {
  const id = event.target.getAttribute('data-id');
  if (id && event.key === 'Enter') showCard(id);
});

// ===== Part D: the photo card and the name tag =====
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
  const width = place.name.length * 8 + 24;
  let x = place.x - width / 2;
  x = Math.max(4, Math.min(MAP_WIDTH - width - 4, x));   
  const y = (place.y < 50) ? place.y + 16 : place.y - 44;

  labelLayer.innerHTML =
    '<g class="mapLabel">' +
    '<rect x="' + x + '" y="' + y + '" width="' + width + '" height="28" rx="14"></rect>' +
    '<text x="' + (x + width / 2) + '" y="' + (y + 19) + '">' + place.name + '</text>' +
    '</g>';
}

// ===== Part E: dropdowns and buttons =====
function fillDropdowns() {
  let list = '';
  for (let i = 0; i < places.length; i++) {
    if (places[i].type !== 'junction') {
      list += '<option value="' + places[i].id + '">' + places[i].name + '</option>';
    }
  }
  fromBox.innerHTML = '<option value="">Choose a place</option>' + list;
  toBox.innerHTML   = '<option value="">Choose a place</option>' + list;
}

function colourDots() {
  const dots = dotLayer.querySelectorAll('.dot');
  for (let i = 0; i < dots.length; i++) {
    const id = dots[i].getAttribute('data-id');
    dots[i].classList.toggle('start', id === fromBox.value);
    dots[i].classList.toggle('end', id === toBox.value);
  }
}

document.getElementById('setFromBtn').addEventListener('click', function () {
  fromBox.value = selectedId;
  colourDots();
});
document.getElementById('setToBtn').addEventListener('click', function () {
  toBox.value = selectedId;
  colourDots();
});

fromBox.addEventListener('change', colourDots);
toBox.addEventListener('change', colourDots);

document.getElementById('swapBtn').addEventListener('click', function () {
  const temp = fromBox.value;
  fromBox.value = toBox.value;
  toBox.value = temp;
  colourDots();
});

// ===== Part F: find the route =====
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

    if (lastId === endId) return route;

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

// ===== Part G: show the result =====
function drawLine(route) {
  let points = '';
  for (let i = 0; i < route.length; i++) {
    const p = getPlace(route[i]);
    points += p.x + ',' + p.y + ' ';
  }
  lineLayer.innerHTML =
    '<polyline class="routeCasing" pathLength="1" points="' + points + '"></polyline>' +
    '<polyline class="routeLine" pathLength="1" points="' + points + '"></polyline>';
}

function clearRoute() {
  lineLayer.innerHTML = '';
  message.textContent = '';
}

document.getElementById('routeForm').addEventListener('submit', function (event) {
  event.preventDefault();

  if (fromBox.value === '' || toBox.value === '') {
    message.textContent = 'Please choose a start and a destination.';
    return;
  }
  if (fromBox.value === toBox.value) {
    message.textContent = 'Start and destination must be different.';
    return;
  }

  const route = findRoute(fromBox.value, toBox.value);
  if (route === null) {
    clearRoute();
    message.textContent = 'No route found between these places.';
    return;
  }

  message.textContent = '';
  drawLine(route);
});

document.getElementById('clearBtn').addEventListener('click', function () {
  fromBox.value = '';
  toBox.value = '';
  clearRoute();
  colourDots();
});

// ===== Start the page =====
drawDots();
fillDropdowns();