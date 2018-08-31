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
 * Defines english translations.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin name and help displayed in settings
$string['pluginname'] = 'OpenVeo Player';
$string['pluginname_help'] = 'Read videos from OpenVeo Publish using the OpenVeo Player.';

// Privacy (GDPR)
$string['privacy:metadata'] = 'The plugin OpenVeo Player does not store or transmit any personal data.';

// Settings
$string['settingsfilefieldslabel'] = 'File fields';
$string['settingsfilefieldsdescription'] = 'The list of fields of type "editor" and "filemanager" used to upload files. If a reference to an OpenVeo video is added from a field not defined in here, the OpenVeo player won\'t appear. Each line represents a field with four columns: the component holding the field (component), the file area (filearea), the identifier (itemid) or position of the identifier and the position of the file path. Columns are separated by pipes. More information available on <a href="https://github.com/veo-labs/openveo-moodle-player" target="_blank">plugin\'s page</a>.';

// Errors
$string['mediaplayererror'] = 'The video couln\'t be displayed.';

// Events
$string['eventplayerdisplayfailed'] = 'OpenVeo media player couldn\'t be displayed.';
