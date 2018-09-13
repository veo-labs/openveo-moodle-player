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

use core_form\filetypes_util;
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
     * Regular expression matching a Moodle media URL.
     *
     * @var string
     */
    protected $moodlemediaurlregex;

    /**
     * The list of extensions the player must look for in case of a Moodle media URL.
     *
     * @var array
     */
    protected $acceptedextensions;

    /**
     * An associative array storing the video id on OpenVeo corresponding to an URL. Only supported URLs are stored. Array keys are
     * URLs and values are OpenVeo video ids.
     *
     * @var array
     */
    protected static $urlscache = array();

    /**
     * Builds a new OpenVeo Media Player plugin.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $filetypesutil = new filetypes_util();
        $this->openveourl = new moodle_url(get_config('local_openveo_api', 'cdnurl'));

        // Get the list of accepted file extensions from accepted types defined in OpenVeo Repository.
        $acceptedtypes = $filetypesutil->normalize_file_types(get_config('openveo', 'supportedfiletypes'));
        $this->acceptedextensions = file_get_typegroup('extension', $acceptedtypes);

        // Build a regular expression to match Moodle media URLs.
        if (sizeof($this->acceptedextensions) > 0) {
            $this->moodlemediaurlregex = str_replace('.', '\.', implode('|', $this->acceptedextensions));
        }

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
     * - Moodle file URLs with one of the extensions defined in configuration
     *   (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.mp4). Only Moodle file URLs
     *   corresponding to Moodle files associated to an OpenVeo repository will be treated.
     *
     * @return array Array of keywords to add to the embeddable markers list
     */
    public function get_embeddable_markers() : array {
        return array_merge(array($this->openveourl->get_host()), $this->acceptedextensions);
    }

    /**
     * Gets the list of file extensions supported by this player.
     *
     * Used to list supported extensions in the administration interface.
     *
     * @return array List of extensions
     */
    public function get_supported_extensions() : array {
        return $this->acceptedextensions;
    }

    /**
     * Gets a single regular expression matching all URLs supported by this media player.
     *
     * Two kinds of URLs are supported by this media player:
     *
     * - OpenVeo URLs (e.g. https://openveo.test/publish/video/SycMFW--X)
     * - Moodle file URLs with one of the extensions defined in configuration
     *   (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.mp4). Only Moodle file URLs
     *   corresponding to Moodle files associated to an OpenVeo repository will be treated.
     *
     * @return string The regular expression
     */
    protected function get_regex() : string {
        if (!empty($this->moodlemediaurlregex)) {

            // Match both OpenVeo URLs and Moodle file URLs.
            return "/{$this->openveomediaurlregex}|(?:{$this->moodlemediaurlregex})$/";

        } else {

            // No video types configured.
            // Match only OpenVeo URLs.
            return "/{$this->openveomediaurlregex}$/";

        }
    }

    /**
     * Lists supported URLs.
     *
     * This is overriden from core_media_player_external as we need a more accurate verification mechanism than a regular expression.
     * OpenVeo player supports OpenVeo URLs and Moodle file URLs using the configured list of extensions. The thing is that the
     * OpenVeo player works with the same extensions as the other video players. Which means we can't base the verification on the
     * URL extension. To find if a Moodle file URL is supported by the OpenVeo Media Player, the corresponding Moodle file must be
     * associated to an OpenVeo Repository.
     *
     * @param array $urls The list of alternatives
     * @param array $options A list of options (see OPTION_* constants). May contain, among other options, the
     * original text being filtered
     */
    public function list_supported_urls(array $urls, array $options = array()) {

        // OpenVeo player only works with a single url (there is no fallback).
        if (count($urls) != 1) {
            return array();
        }

        $url = reset($urls);
        $fullurl = urldecode($url->out(false));

        if (array_key_exists($fullurl, self::$urlscache)) {

            // This URL has already been treated. We already have the corresponding OpenVeo video id. We know the URL supported.
            return array($url);

        }

        if (preg_match($this->get_regex(), $fullurl)) {

            // Find OpenVeo video id corresponding to the URL.
            $openveovideoid = $this->get_openveo_video_id($fullurl);
            if (isset($openveovideoid)) {
                self::$urlscache[$fullurl] = $openveovideoid;
                return array($url);
            }

        }

        return array();
    }

    /**
     * Gets OpenVeo video id corresponding to an OpenVeo URL or Moodle file URL.
     *
     * @param string $url The URL to analyze
     * @return string The OpenVeo video id or null if not found
     */
    protected function get_openveo_video_id(string $url) {
        $filestorage = get_file_storage();

        if (preg_match(
                '/pluginfile\.php\/' .
                '([^\/]+)\/' . // contextid
                '([^\/]+)\/' . // component
                '([^\/]+)\/' . // filearea
                '(.*)$/', // pathname + filename
                $url,
                $matches
            )
        ) {

            // URL is a Moodle URL
            // (e.g. https://moodle.url/pluginfile.php/contextid/component/filearea/itemid/path/to/video.mp4).
            // Find each part of the URL (contextid, component, filearea, itemid, pathname and filename). to be able
            // to retrieve the Moodle file and thus its associated OpenVeo media id.

            $contextid = $matches[1];
            $component = $matches[2];
            $filearea = $matches[3];

            if (!array_key_exists($component . '/' . $filearea, $this->filefields)) {

                // This file field is not in OpenVeo Media Player configuration thus it should be ignored.
                return null;

            }

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
                    '/(?:[^\/]+\/){' . $pathnameposition . '}((?:[^\/]+\/)*)(.*(?:' . $this->moodlemediaurlregex . '))$/',
                    $matches[4],
                    $lastpartmatches2
            );
            $pathname = '/' . trim($lastpartmatches2[1], '/');
            $filename = $lastpartmatches2[2];

            // Find the Moodle file corresponding to the file with the contextid, component, filearea, itemid,
            // pathname and filename.
            $file = $filestorage->get_file($contextid, $component, $filearea, $itemid, $pathname, $filename);

            // Get OpenVeo media id.
            if (!empty($file)) {
                return ($file->get_repository_type() !== 'openveo') ? null : $file->get_source();
            }

        } else if (preg_match(
                '/draftfile\.php\/' .
                '([^\/]+)\/' . // contextid
                'user\/draft\/' .
                '([^\/]+)\/' . // draftid
                '(.*)$/', // pathname + filename
                $url,
                $matches
            )
        ) {

            // URL is a Moodle draft URL
            // (e.g. https://moodle.url/draftfile.php/contextid/user/draft/itemid/path/to/video.mp4).
            // find each part of the URL (contextid, itemid, pathname and filename).

            $contextid = $matches[1];
            $component = 'user';
            $filearea = 'draft';
            $itemid = $matches[2];

            // Find pathname and filename from URL.
            preg_match("/(.*)\/(.*{$this->moodlemediaurlregex})$/", "/$matches[3]", $lastpartmatches);
            $pathname = (!empty($lastpartmatches[1])) ? $lastpartmatches[1] : '/';
            $filename = $lastpartmatches[2];

            // Find the Moodle file corresponding to the file with the contextid, component, filearea, itemid,
            // pathname and filename.
            $file = $filestorage->get_file($contextid, $component, $filearea, $itemid, $pathname, $filename);

            // Get OpenVeo media id.
            if (!empty($file)) {
                return ($file->get_repository_type() !== 'openveo') ? null : unserialize($file->get_source())->source;
            }

        } else if (preg_match("/$this->openveomediaurlregex/", $url, $matches)) {

            // URL is already an OpenVeo media URL (e.g. https://openveo.test/publish/video/SycMFW--X).

            return $matches[1];

        }

        return null;
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
        $id = self::$urlscache[$fullurl];

        $media = new openveo_media($id, $width, $height, $openveourl, current_language(), $autoplay);
        $renderer = $PAGE->get_renderer('media_openveo');
        return $renderer->render($media);
    }

    /**
     * Gets the rank of this player regarding other players.
     *
     * As OpenVeo Media Player supports the same files as the videojs player it has to be prior to it.
     *
     * @return int Rank (higher is better)
     */
    public function get_rank() : int {
        return 2001;
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
