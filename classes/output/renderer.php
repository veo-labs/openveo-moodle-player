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
 * Defines the renderer for the plugin.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace media_openveo\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use media_openveo\output\openveo_media;

/**
 * Defines the plugin renderer.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders an OpenVeo media using OpenVeo Player.
     *
     * @param openveo_media $media The OpenVeo media
     * @return string The computed HTML of the OpenVeo Player for the media
     */
    public function render_openveo_media(openveo_media $media) : string {
        $data = $media->export_for_template($this);
        return parent::render_from_template('media_openveo/player', $data);
    }

}
