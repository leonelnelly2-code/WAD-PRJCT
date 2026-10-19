// PLACES


const places = [

  // ----- Gates -----
  { id: 'gateWest',   name: 'Lower Campus Main Gate',  type: 'gate', x: 996,  y: 430,
    photo: 'images/gates/West gate.jpeg',   info: 'Access gate next to the Health and Science parking.' },
  { id: 'gateCentre', name: 'Lower Campus Small Gate', type: 'gate', x: 1590, y: 422,
    photo: 'images/gates/Centre gate.jpeg', info: 'Access gate between the Auditorium and the Library.' },
  { id: 'gateSmall',  name: 'Small Gate',              type: 'gate', x: 1930, y: 528,
    photo: 'images/gates/Small gate.jpeg',  info: 'Pedestrian entrance on Brahms Str.' },
  { id: 'gateMain',   name: 'Main Gate',               type: 'gate', x: 1944, y: 982,
    photo: 'images/gates/Main gate.jpeg',   info: 'Main entrance on Schubert Str.' },

  // ----- Buildings -----
  { id: 'health',    name: 'Health and Science Building', type: 'building', x: 1135, y: 452,
    photo: 'images/buildings/Health and Applied Sciences Building.jpeg', info: 'Health and science faculty.' },
  { id: 'basement',  name: 'Basement',            type: 'building', x: 1370, y: 366,
    photo: 'images/buildings/Engineering Basement.jpeg', info: 'Basement venue.' },
  { id: 'audit',     name: 'Auditorium Building', type: 'building', x: 1509, y: 396,
    photo: 'images/buildings/audit.jpeg',                info: 'Large lecture auditorium.' },
  { id: 'lib',       name: 'Library',             type: 'building', x: 1643, y: 285,
    photo: 'images/buildings/Library.jpeg',              info: 'Campus library.' },
  { id: 'office',    name: 'Office Building',     type: 'building', x: 1922, y: 622,
    photo: 'images/buildings/Office Building.jpeg',      info: 'FCI labs.' },
  { id: 'lecturer',  name: 'Lecture Building',    type: 'building', x: 2056, y: 648,
    photo: 'images/buildings/Lecture Building.jpeg',     info: 'Lecture rooms.' },
  { id: 'elisabeth', name: 'Elisabeth Haus',      type: 'building', x: 1919, y: 748,
    photo: 'images/buildings/Elisabeth Haus.jpeg',       info: 'Elisabeth Haus.' },          // TODO: real description
  { id: 'admin',     name: 'Admin Building',      type: 'building', x: 1908, y: 910,
    photo: 'images/buildings/Admin Building.jpeg',       info: 'Administration offices.' },
  { id: 'poly',      name: 'Poly Heights',        type: 'building', x: 2095, y: 963,
    photo: 'images/buildings/Poly Heights.jpeg',         info: 'Poly Heights.' },            // TODO: real description
  { id: 'it',        name: 'IT House',            type: 'building', x: 1813, y: 1074,
    photo: 'images/buildings/IT House.jpeg',             info: 'Information Technology.' },

  // ----- Walkways inside campus (never shown) -----
  { id: 'jWalkHealth',  name: 'Health walkway',      type: 'junction', x: 1150, y: 414 },
  { id: 'jWalkAudit',   name: 'Auditorium walkway',  type: 'junction', x: 1528, y: 379 },
  { id: 'jWalkLib',     name: 'Library walkway',     type: 'junction', x: 1603, y: 367 },
  { id: 'jWalkAzNorth', name: 'A-zone north',        type: 'junction', x: 1960, y: 610 },
  { id: 'jWalkAzSouth', name: 'A-zone south',        type: 'junction', x: 1920, y: 830 },
  { id: 'jWalkMain',    name: 'Main Gate walkway',   type: 'junction', x: 1890, y: 1000 },

  // ----- Road corners (never shown) -----
  { id: 'j1',  name: 'Brahms Str west',      type: 'junction', x: 1072, y: 544 },
  { id: 'j2',  name: 'Brahms Str centre',    type: 'junction', x: 1519, y: 470 },
  { id: 'j3',  name: 'Brahms / Weber',       type: 'junction', x: 1693, y: 463 },
  { id: 'jW',  name: 'Weber Str',            type: 'junction', x: 1612, y: 651 },
  { id: 'jS',  name: 'Weber / Schubert',     type: 'junction', x: 1573, y: 835 },
  { id: 'jS2', name: 'Schubert Str east',    type: 'junction', x: 1860, y: 981 }
];


const paths = [

  // West: Health and Science
  { from: 'health',      to: 'jWalkHealth',
    note: 'Leave the Health and Science building',
    back: 'Walk up to the Health and Science building' },
  { from: 'jWalkHealth', to: 'gateWest',
    note: 'Follow the walkway to the Lower Campus Main Gate',
    back: 'Follow the walkway towards the Health and Science building' },
  { from: 'gateWest',    to: 'j1',
    note: 'Go out through the gate onto Brahms Str',
    back: 'Go in through the Lower Campus Main Gate' },

  // Centre: Auditorium, Basement, Library
  { from: 'audit',       to: 'jWalkAudit',
    note: 'Leave the Auditorium building',
    back: 'Walk up to the Auditorium building' },
  { from: 'basement',    to: 'jWalkAudit',
    note: 'Leave the Basement',
    back: 'Walk over to the Basement' },
  { from: 'jWalkAudit',  to: 'gateCentre',
    note: 'Follow the walkway to the Lower Campus Small Gate',
    back: 'Follow the walkway towards the Auditorium' },
  { from: 'gateCentre',  to: 'j2',
    note: 'Go out through the gate onto Brahms Str',
    back: 'Go in through the Lower Campus Small Gate' },
  { from: 'lib',         to: 'jWalkLib',
    note: 'Leave the Library',
    back: 'Walk up to the Library' },
  { from: 'jWalkLib',    to: 'jWalkAudit',
    note: 'Walk down towards the Auditorium',
    back: 'Walk up towards the Library' },

  // East: A-zone
  { from: 'office',       to: 'jWalkAzNorth',
    note: 'Leave the Office building',
    back: 'Walk over to the Office building' },
  { from: 'lecturer',     to: 'jWalkAzNorth',
    note: 'Leave the Lecture building',
    back: 'Walk over to the Lecture building' },
  { from: 'jWalkAzNorth', to: 'gateSmall',
    note: 'Walk north to the Small Gate',
    back: 'Walk south into the A-zone' },
  { from: 'gateSmall',    to: 'j3',
    note: 'Go out through the Small Gate onto Brahms Str',
    back: 'Go in through the Small Gate' },
  { from: 'elisabeth',    to: 'jWalkAzSouth',
    note: 'Leave Elisabeth Haus',
    back: 'Walk over to Elisabeth Haus' },
  { from: 'jWalkAzNorth', to: 'jWalkAzSouth',
    note: 'Walk south through the A-zone',
    back: 'Walk north through the A-zone' },

  // South: Main Gate area
  { from: 'jWalkAzSouth', to: 'admin',
    note: 'Continue south to the Admin Building',
    back: 'Walk north from the Admin Building' },
  { from: 'admin',        to: 'jWalkMain',
    note: 'Walk down to the Main Gate area',
    back: 'Walk up to the Admin Building' },
  { from: 'it',           to: 'jWalkMain',
    note: 'Leave IT House',
    back: 'Walk over to IT House' },
  { from: 'poly',         to: 'jWalkMain',
    note: 'Leave Poly Heights',
    back: 'Walk over to Poly Heights' },
  { from: 'jWalkMain',    to: 'gateMain',
    note: 'Walk to the Main Gate',
    back: 'Walk in from the Main Gate' },
  { from: 'gateMain',     to: 'jS2',
    note: 'Go out through the Main Gate onto Schubert Str',
    back: 'Go in through the Main Gate' },

  // Road network
  { from: 'j1',  to: 'j2',
    note: 'Follow Brahms Str east',
    back: 'Follow Brahms Str west' },
  { from: 'j2',  to: 'j3',
    note: 'Keep following Brahms Str east',
    back: 'Keep following Brahms Str west' },
  { from: 'j3',  to: 'jW',
    note: 'Turn south onto Weber Str',
    back: 'Follow Weber Str north to Brahms Str' },
  { from: 'jW',  to: 'jS',
    note: 'Continue south on Weber Str',
    back: 'Continue north on Weber Str' },
  { from: 'jS',  to: 'jS2',
    note: 'Turn east onto Schubert Str',
    back: 'Follow Schubert Str west' }
];