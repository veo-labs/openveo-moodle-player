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
 * Defines OpenVeo Media Player plugin.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use media_openveo\output\openveo_media;
use media_openveo\event\player_display_failed;

/**
 * Defines the OpenVeo Media Player plugin as an external player.
 *
 * Extending core_media_player_external indicates, among other things, that the player accepts only one URL
 * (no alternative sources).
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_openveo_plugin extends core_media_player_external {

    /**
     * OpenVeo base URL retrieved from OpenVeo API plugin as a moodle_url.
     *
     * @var moodle_url
     */
    protected $openveourl;

    /**
     * Regular expression matching an OpenVeo media URL.
     *
     * @var string
     */
    protected $openveomediaurlregex;

    /**
     * The list of file fields within Moodle.
     *
     * Associative array with component/filearea as keys and information associative array as values
     *
     * @var array
     */
    protected $filefields = array();

    /**
     * Builds a new OpenVeo Media Player plugin.
     */
    public function __construct() {
        $this->openveourl = new moodle_url(get_config('local_openveo_api', 'cdnurl'));

        // Escape dots in OpenVeo host and use it to build a regular expression to match OpenVeo media URLs.
        $hostpattern = preg_replace('/\./', '\\.', $this->openveourl->get_host());
        $this->openveomediaurlregex = "(?:$hostpattern(?::[0-9]+)?\/publish\/video\/([^\/?&#]+))";

        // Build file fields from configuration.
        $filefields = preg_split('/\r\n|\r|\n/', get_config('media_openveo', 'filefields'));

        foreach ($filefields as $filefield) {
            $filefieldcolumns = explode('|', $filefield);

            if (isset($filefieldcolumns[0]) && isset($filefieldcolumns[1])) {
                $this->filefields[$filefieldcolumns[0] . '/' . $filefieldcolumns[1]] = array(
                    'itemid' => $filefieldcolumns[2],
                    'pathname' => $filefieldcolumns[3]
                );
            }
        }
    }

    /**
     * Gets keywords, included in the href attribute of a "a" tag, this player should match.
     *
     * Two kinds of URLs are supported by this media player:
     *
     * - OpenVeo URLs (e.g. https://openveo.test/publish/video/SycMFW--X)
     * - Moodle file URLs with .openveo extension
     *   (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.openveo)
     *
     * @return array Array of keywords to add to the embeddable markers list
     */
    public function get_embeddable_markers() : array {
        return array($this->openveourl->get_host(), '.openveo');
    }

    /**
     * Gets the list of file extensions supported by this player.
     *
     * Used to list supported extensions in the administration interface.
     *
     * @return array List of extensions
     */
    public function get_supported_extensions() : array {
        return array('.openveo');
    }

    /**
     * Gets a single regular expression matching all URLs supported by this media player.
     *
     * Two kinds of URLs are supported by this media player:
     *
     * - OpenVeo URLs (e.g. https://openveo.test/publish/video/SycMFW--X)
     * - Moodle file URLs with .openveo extension
     *   (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.openveo)
     *
     * @return string The regular expression
     */
    protected function get_regex() : string {

        // Match both OpenVeo URLs and Moodle file.
        return "/{$this->openveomediaurlregex}|(?:\.openveo$)/";

    }

    /**
     * Generates OpenVeo player code for the given URL.
     *
     * @param array $urls A list containing only the video URL (alternatives are not supported by OpenVeo Player)
     * @param string $name The name associated to the URL
     * @param int $width The expected video width
     * @param int $height The expected video height
     * @param array $options A list of options (see OPTION_* constants). May contain, among other options, the
     * original text being filtered
     * @return string The OpenVeo Player HTML code
     */
    public function embed_external(moodle_url $url, $name, $width, $height, $options) : string {
        global $PAGE;
        global $CFG;

        $filestorage = get_file_storage();
        $fullurl = urldecode($url->out(false));
        $openveourl = rtrim($this->openveourl->out_omit_querystring(), '/');

        if (empty($openveourl)) {

            // OpenVeo CDN URL is empty, OpenVeo API local plugin has not been configured yet.
            // Send an event and let openveo_media display an error message.

            $this->send_player_display_failed_event('OpenVeo API local plugin is not configured.');

        }

        // Get autoplay HTML attribute from original text.
        if (isset($options[core_media_manager::OPTION_ORIGINAL_TEXT])) {
            $originaltext = $options[core_media_manager::OPTION_ORIGINAL_TEXT];
            $autoplay = core_media_player_native::get_attribute($originaltext, 'autoplay');
        }

        $width = !empty($width) ? $width : $CFG->media_default_width;
        $height = !empty($height) ? $height : $CFG->media_default_height;
        $autoplay = isset($autoplay) ? true : false;
        $id = null;

        if (preg_match(
                '/pluginfile\.php\/' .
                '([^\/]+)\/' . // contextid
                '([^\/]+)\/' . // component
                '([^\/]+)\/' . // filearea
                '(.*)$/', // pathname + filename
                $fullurl,
                $matches
            )
        ) {

            // URL is a Moodle URL
            // (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.openveo).
            // Find each part of the URL (contextid, component, filearea, itemid, pathname and filename). to be able
            // to retrieve the Moodle file and thus its associated OpenVeo media id.

            $contextid = $matches[1];
            $component = $matches[2];
            $filearea = $matches[3];

            if (!array_key_exists($component . '/' . $filearea, $this->filefields)) {

                // This file field is not in OpenVeo Media Player configuration thus it should be ignored.

                $this->send_player_display_failed_event(
                        "File field with URL $fullurl is not supported by the OpenVeo Media Player. " .
                        "You might want to add it to the file fields configuration."
                );

                if (isset($options[core_media_manager::OPTION_ORIGINAL_TEXT])) {

                    // An original text exists, calls must come from a filter.
                    // Just return the original text without any modification.

                    return $options[core_media_manager::OPTION_ORIGINAL_TEXT];

                } else {

                    // No original text, caller expects a media player to play an URL.
                    // Just let openveo_media display the error message.

                }
            } else {

                // File field is supported, information about where to get itemid and pathname can be retrieved from
                // configuration.

                $filefield = $this->filefields[$component . '/' . $filearea];

                if (strpos($filefield['itemid'], 'id') !== false) {

                    // itemid is static and is not part of the URL.

                    // Pick itemid from configuration.
                    $itemid = str_replace('id', '', $filefield['itemid']);

                } else {

                    // itemid is dynamic and is part of the URL.

                    // Pick itemid position from configuration.
                    $itemidposition = intval(str_replace('pos', '', $filefield['itemid']));

                    // Find itemid from the URL using position.
                    preg_match('/(?:[^\/]+\/){' . $itemidposition . '}([^\/]+)/', "$matches[4]", $lastpartmatches);
                    $itemid = $lastpartmatches[1];

                }

                // Pick pathname position from configuration.
                $pathnameposition = intval(str_replace('pos', '', $filefield['pathname']));

                // Find pathname and filename from the URL using pathname position.
                preg_match(
                        '/(?:[^\/]+\/){' . $pathnameposition . '}((?:[^\/]+\/)*)(.*\.openveo)$/',
                        $matches[4],
                        $lastpartmatches2
                );
                $pathname = '/' . trim($lastpartmatches2[1], '/');
                $filename = $lastpartmatches2[2];

            }

            // Find the Moodle file corresponding to the file with the contextid, component, filearea, itemid,
            // pathname and filename.
            $file = $filestorage->get_file($contextid, $component, $filearea, $itemid, $pathname, $filename);

            // Get OpenVeo media id.
            if (!empty($file)) {
                $id = $file->get_source();
            }

        } else if (preg_match(
                '/draftfile\.php\/' .
                '([^\/]+)\/' . // contextid
                'user\/draft\/' .
                '([^\/]+)\/' . // draftid
                '(.*)$/', // pathname + filename
                $fullurl,
                $matches
            )
        ) {

            // URL is a Moodle draft URL
            // (e.g. https://moodle.url/draftfile.php/contextid/user/draft/itemid/path/to/video.openveo).
            // find each part of the URL (contextid, itemid, pathname and filename).

            $contextid = $matches[1];
            $component = 'user';
            $filearea = 'draft';
            $itemid = $matches[2];

            // Find pathname and filename from URL.
            preg_match("/(.*)\/(.*\.openveo)$/", "/$matches[3]", $lastpartmatches);
            $pathname = (!empty($lastpartmatches[1])) ? $lastpartmatches[1] : '/';
            $filename = $lastpartmatches[2];

            // Find the Moodle file corresponding to the file with the contextid, component, filearea, itemid,
            // pathname and filename.
            $file = $filestorage->get_file($contextid, $component, $filearea, $itemid, $pathname, $filename);

            // Get OpenVeo media id.
            if (!empty($file)) {
                $id = unserialize($file->get_source())->source;
            }

        } else if (preg_match("/$this->openveomediaurlregex/", $fullurl, $matches)) {

            // URL is already an OpenVeo media URL (e.g. https://openveo.test/publish/video/SycMFW--X).

            $id = $matches[1];

        }

        if (empty($id)) {

            // OpenVeo media id has not been found for this file reference.
            // Send an event and let openveo_media display an error.

            $this->send_player_display_failed_event(
                    "OpenVeo media id has not been found for the file reference: $fullurl."
            );

        }

        $media = new openveo_media($id, $width, $height, $openveourl, current_language(), $autoplay);
        $renderer = $PAGE->get_renderer('media_openveo');
        return $renderer->render($media);
    }

    /**
     * Gets the rank of this player regarding other players.
     *
     * As OpenVeo Media Player supports only .openveo files it does not have to be prior to other players.
     *
     * @return int Rank (higher is better)
     */
    public function get_rank() : int {
        return 0;
    }

    /**
     * Triggers a media_openveo\event\player_display_failed event with the given message.
     *
     * @param string $message The error message to send along with the event
     */
    private function send_player_display_failed_event(string $message) {
        global $PAGE;

        $event = player_display_failed::create(array(
            'context' => $PAGE->context,
            'other' => array(
                'message' => $message
            )
        ));
        $event->trigger();
    }

}
