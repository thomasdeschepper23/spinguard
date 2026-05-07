<?php
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit; }

/**
 * Centrale data voor de Nederland-kaart op /over-ons.
 * Coordinaten matchen de viewBox (0 0 612.54 723.62) van assets/netherlands.svg.
 *
 * Provincies: ISO codes uit de SVG (id="NL-XX").
 * Steden: vooraf-bepaalde lat/lon → SVG-coordinaten + label-positionering.
 */
return [
    'provinces' => [
        'NL-DR' => 'Drenthe',
        'NL-FL' => 'Flevoland',
        'NL-FR' => 'Friesland',
        'NL-GE' => 'Gelderland',
        'NL-GR' => 'Groningen',
        'NL-LI' => 'Limburg',
        'NL-NB' => 'Noord-Brabant',
        'NL-NH' => 'Noord-Holland',
        'NL-OV' => 'Overijssel',
        'NL-UT' => 'Utrecht',
        'NL-ZE' => 'Zeeland',
        'NL-ZH' => 'Zuid-Holland',
    ],
    'cities' => [
        'leeuwarden' => ['x' => 385, 'y' =>  93, 'name' => 'Leeuwarden', 'province' => 'NL-FR', 'anchor' => 'middle', 'ox' =>   0, 'oy' => -16],
        'groningen'  => ['x' => 508, 'y' =>  88, 'name' => 'Groningen',  'province' => 'NL-GR', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'zwolle'     => ['x' => 432, 'y' => 270, 'name' => 'Zwolle',     'province' => 'NL-OV', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'haarlem'    => ['x' => 203, 'y' => 304, 'name' => 'Haarlem',    'province' => 'NL-NH', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'amsterdam'  => ['x' => 244, 'y' => 307, 'name' => 'Amsterdam',  'province' => 'NL-NH', 'anchor' => 'end',    'ox' => -16, 'oy' =>  -6],
        'almere'     => ['x' => 295, 'y' => 307, 'name' => 'Almere',     'province' => 'NL-FL', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'apeldoorn'  => ['x' => 413, 'y' => 348, 'name' => 'Apeldoorn',  'province' => 'NL-GE', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'amersfoort' => ['x' => 322, 'y' => 361, 'name' => 'Amersfoort', 'province' => 'NL-UT', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'utrecht'    => ['x' => 279, 'y' => 379, 'name' => 'Utrecht',    'province' => 'NL-UT', 'anchor' => 'middle', 'ox' =>   0, 'oy' => -16],
        'den-haag'   => ['x' => 149, 'y' => 384, 'name' => 'Den Haag',   'province' => 'NL-ZH', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'arnhem'     => ['x' => 404, 'y' => 407, 'name' => 'Arnhem',     'province' => 'NL-GE', 'anchor' => 'start',  'ox' =>  16, 'oy' =>  -8],
        'rotterdam'  => ['x' => 177, 'y' => 422, 'name' => 'Rotterdam',  'province' => 'NL-ZH', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'nijmegen'   => ['x' => 398, 'y' => 443, 'name' => 'Nijmegen',   'province' => 'NL-GE', 'anchor' => 'start',  'ox' =>  24, 'oy' =>   6],
        'den-bosch'  => ['x' => 307, 'y' => 482, 'name' => 'Den Bosch',  'province' => 'NL-NB', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'breda'      => ['x' => 225, 'y' => 507, 'name' => 'Breda',      'province' => 'NL-NB', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'tilburg'    => ['x' => 274, 'y' => 515, 'name' => 'Tilburg',    'province' => 'NL-NB', 'anchor' => 'end',    'ox' => -16, 'oy' =>   6],
        'eindhoven'  => ['x' => 336, 'y' => 546, 'name' => 'Eindhoven',  'province' => 'NL-NB', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
        'maastricht' => ['x' => 369, 'y' => 698, 'name' => 'Maastricht', 'province' => 'NL-LI', 'anchor' => 'start',  'ox' =>  16, 'oy' =>   6],
    ],
    'defaults' => [
        'active_provinces' => ['NL-DR','NL-FL','NL-FR','NL-GE','NL-GR','NL-LI','NL-NB','NL-NH','NL-OV','NL-UT','NL-ZE','NL-ZH'],
        'active_cities'    => ['leeuwarden','groningen','zwolle','amsterdam','apeldoorn','utrecht','den-haag','arnhem','rotterdam','nijmegen','tilburg','eindhoven','maastricht'],
        'hq_city'          => 'nijmegen',
    ],
];
