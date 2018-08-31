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
 * Defines an OpenVeo media.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace media_openveo\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use stdClass;
use renderer_base;

/**
 * Defines an OpenVeo media.
 *
 * An OpenVeo media is caracterised by an OpenVeo Publish URL and a size.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class openveo_media implements renderable, templatable {

    /**
     * The OpenVeo media id.
     *
     * @var string
     */
    protected $id;

    /**
     * The OpenVeo media width.
     *
     * @var int
     */
    protected $width;

    /**
     * The OpenVeo media height.
     *
     * @var int
     */
    protected $height;

    /**
     * The OpenVeo CDN base URL.
     *
     * @var string
     */
    protected $openveourl;

    /**
     * The language to use for this media.
     *
     * @var string
     */
    protected $language;

    /**
     * Indicates if media must automatically start on page load.
     *
     * @var bool
     */
    protected $autoplay;

    /**
     * Creates a new openveo_media.
     *
     * If, at least, id and openveourl are set the media should be displayed. Otherwise it will be an error message.
     *
     * @param string $id The OpenVeo media id
     * @param int $width The media width
     * @param int $height The media height
     * @param string $openveourl The OpenVeo CDN base URL
     * @param string $language The language to use for this media
     * @param bool $autoplay true if media must automatically start on page load, false otherwise
     */
    public function __construct(string $id = null, int $width = 768, int $height = 500, string $openveourl = null,
                                string $language = 'en', bool $autoplay = false) {
        $this->id = $id;
        $this->width = $width;
        $this->height = $height;
        $this->openveourl = $openveourl;
        $this->language = $language;
        $this->autoplay = $autoplay;
    }

    /**
     * Exports openveo_media data to be exposed to a template.
     *
     * @see templatable
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export
     * @return stdClass Data to expose to the template
     */
    public function export_for_template(renderer_base $output) : stdClass {
        $data = new stdClass();

        if (empty($this->openveourl) || empty($this->id)) {
            $data->error = true;
        }

        if (!empty($data->error)) {
            $data->errormessage = get_string('mediaplayererror', 'media_openveo');
        } else {
            $data->iframe = true;
            $data->width = $this->width;
            $data->height = $this->height;
            $data->url = "{$this->openveourl}/publish/video/{$this->id}?fullscreen&lang={$this->language}" .
                    (!empty($this->autoplay) ? '&auto-play' : '');
        }

        return $data;
    }

}
