# OpenVeo Moodle Player

OpenVeo Moodle Player is a Moodle Media Player plugin which transforms video references, created using [OpenVeo Moodle Repository](https://github.com/veo-labs/openveo-moodle-repository), into an [OpenVeo Player](https://github.com/veo-labs/openveo-player).

# Getting Started

## Prerequisites

- Moodle version >=3.4.0
- [Openveo](https://github.com/veo-labs/openveo-core) >=5.1.1
- [Openveo Publish plugin](https://github.com/veo-labs/openveo-publish) >=7.1.0
- [OpenVeo Moodle API plugin](https://github.com/veo-labs/openveo-moodle-api) >=1.0.0
- Make sure OpenVeo Moodle API plugin is configured
- Make sure **Multimedia plugins** filter plugin is enabled (**Plugins > Filters > Manage filters > Multimedia plugins**)

## Installation

- Download zip file corresponding to the latest stable version of the OpenVeo Moodle Player plugin
- Unzip it and rename **openveo-moodle-player-\*** directory into **openveo**
- Move your **openveo** folder into **MOODLE_ROOT_PATH/media/player/** where MOODLE_ROOT_PATH is your Moodle installation folder
- In your Moodle site (as admin) go to **Site administration > Notifications**: you should get a message saying the plugin is installed
- In your Moodle site (as admin) go to **Site administration > Plugins > Media players > Manage media players**, activate the media player (**Enable**) and move it on top of the list of players
- In your Moodle site (as admin) go to **Site administration > Plugins > Media players > OpenVeo Player** and verify OpenVeo player settings

If you experience troubleshooting during installation, please refer to the [Moodle](https://docs.moodle.org) installation plugin documentation.

# Troubleshooting

## Video is playing but does not use OpenVeo Moodle Player

If the video is displayed and can be played using another player, it means the video is not an OpenVeo video. Video was not added using [OpenVeo Moodle Repository](https://github.com/veo-labs/openveo-moodle-repository). OpenVeo Moodle Player can only read videos imported using OpenVeo Moodle Repository.

## Video can't be played

Another player is loaded and can't play the video. Other players can't play OpenVeo videos. If video was added using the [OpenVeo Moodle Repository](https://github.com/veo-labs/openveo-moodle-repository) it could be a result of the priority of players. As administrator go to **Site administration > Plugins > Media players > Manage media players**. Check that OpenVeo Moodle Player is enabled and on top of all other players.

If the problem persists go to **Site administration > Plugins > Media players > OpenVeo Player** and check that the extension of the video is listed in the supported types. If not add it.

If the problem still persists, maybe you installed non-native plugins which make use of Moodle Form API with "editor" or "filemanager" fields, videos added through these plugins might not be known by OpenVeo Moodle Player. OpenVeo videos added through the OpenVeo Repository can be directly OpenVeo links or Moodle file references. In case of Moodle file references, OpenVeo Moodle Player transforms Moodle URLs (e.g. **[...]/pluginfile.php/contextid/component/filearea/itemid/path/to/the/video.mp4**) into a player. The fact is that a Moodle URL, sometimes, doesn't contain enough information to find the original file. To be able to find an original file we need its context (**contextid**), the plugin which holds it (**component**), the name of the area (**filearea**) it belongs to and an identifier (**itemid**) related to the area. The context, the plugin name and the area are always specified in Moodle URLs but not the identifier. This identifier is specific for each form field of type **editor** or **filemanager**, sometimes it is specified in the Moodle URL, sometimes it isn't. Consequently, for each usage of a form field of type **editor** or **filemanager**, OpenVeo Moodle Player needs to know where to get the identifier, is it in the Moodle URL? Is is hardcoded? If it is in the Moodle URL, where is it located?

OpenVeo Moodle Player knows about the identifiers used by the different fields of type **editor** and **filemanager** in a native Moodle installation but don't know about non-native plugins. Identifiers are listed in plugin configuration (**Site administration > Plugins > Media players > Manage media players > OpenVeo Player settings**). Each line corresponds to a field of type **editor** or **filemanager** within Moodle. Columns are separated by pipes just like in a CSV file. You can add other fields (from non-native plugins) here by adding new lines.

Columns are: **component|filearea|itemid|pathnameposition** with:

- **component**: The name of the plugin holding the file
- **filearea**: The area the file belongs to
- **itemid**: The identifier associated to the file. If it is hardcoded in the plugin you can add it here prefixed by **id** (e.g. **id5**), if it is dynamic you have to add its position in the URL prefixed by **pos** (e.g. **pos1**)
- **pathnameposition**: The position of the file path in the URL prefixed by **pos** (e.g. **pos2**)

Position indicates the position of a parameter in the URL. Position is the index of the parameter in the list of parameters. Positions start at 0 and correspond to the parameter right after the filearea. Position 1 will be the next parameter, position 2 the third parameter and so on...

[...]/component/filearea/**parameterInPos0**/**parameterInPos1**/**parameterInPos2**/**parameterInPos3**/**parameterInPos4**/file.mp4

**Nb:** Sadly you will have to read the source code of the plugin to find its fields of type **editor** and **filemanager**. Within the source code you will find the **component**, the **filearea** and the **itemid**. If you can't find the structure of the URL in the source code, you can refer to Moodle error events, OpenVeo Moodle Player will send an error event each time a video can't be displayed with the URL.

# Contributors

Maintainer: [Veo-Labs](http://www.veo-labs.com/)

# License

[GPL3](http://www.gnu.org/licenses/gpl.html)
