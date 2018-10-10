<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines french translations.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin name and help displayed in settings.
$string['pluginname'] = 'OpenVeo Player';
$string['pluginname_help'] = 'Lit les vidéos d\'OpenVeo Publish en utilisant l\'OpenVeo Player.';

// Privacy (GDPR).
$string['privacy:metadata'] = 'Le plugin OpenVeo Player n\'enregistre ni ne transmet de données personnelles.';

// Settings.
$string['settingsfilefieldslabel'] = 'Champs d\'ajout de fichiers';
$string['settingsfilefieldsdescription'] = 'La liste des champs de formulaire de type "editor" et "filemanager" permettant d\'ajouter des fichiers. Si une réfèrence vers une vidéo OpenVeo est ajoutée à partir d\'un champ de formulaire sans que le champ ne soit défini ici, le player OpenVeo n\'apparaîtra pas. Chaque ligne représente un champ avec quatre colonnes : le composant créant le champ (component), la zone du fichier (filearea), l\'identifiant (itemid) ou la position de l\'identifiant associé et la position du chemin relatif du fichier (pathname). Les colonnes sont séparées par des barres verticales. Plus d\'informations disponibles sur <a href="https://github.com/veo-labs/openveo-moodle-player" target="_blank">la page du plugin</a>.';
$string['settingssubmitlabel'] = 'Enregistrer les modifications';

// Errors.
$string['errormediaplayer'] = 'La vidéo n\'a pu être affichée.';

// Events.
$string['eventplayerdisplayfailed'] = 'Le media player OpenVeo n\'a pu être affiché.';
