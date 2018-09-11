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
 * Defines plugin settings page.
 *
 * @package media_openveo
 * @copyright 2018 Veo-labs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// File fields within Moodle.
// Defaults are file fields defined in a native Moodle installation.
//
// Columns are:
//     1. the name of the plugin holding the file in frankenstyle
//     2. the area the file belongs to
//     3. either the itemid prefixed by "id" or the position of the itemid in the URL prefixed by "pos"
//     4. position of the pathname in the URL prefixed by "pos"
//
// Position indicates the position of a parameter in the URL. Position is the index of the parameter in the list
// of parameters. Positions start at 0 and correspond to the parameter right after the filearea. Position 1
// will be the next parameter, position 2 the third parameter and so on...
// [...]/component/filearea/parameter0/parameter1/parameter2/parameter3/parameter4/file.openveo
//                            [pos0]     [pos1]     [pos2]     [pos3]      [pos4]     [pos5]
$settings->add(new admin_setting_configtextarea(
        'media_openveo/filefields',
        get_string('settingsfilefieldslabel', 'media_openveo'),
        get_string('settingsfilefieldsdescription', 'media_openveo'),

        // Prerequisites: Submission type has to be set to "online text" in course > assignmnent module > edition >
        //                submission types
        // Field location: course > assignment module > add submission
        // Display location: course > assignment module
        // User: Course editor for prerequisites and enrolled user for edition and display
        "assignsubmission_onlinetext|submissions_onlinetext|pos0|pos1\n" .

        // Field location: HTML block > edition > content
        // Display location: HTML block
        // User: Any user
        "block_html|content|id0|pos0\n" .

        // Field location: blog menu block > blog entry > edition > attachment
        // Display location: blog menu block > blog entry
        // User: Any user
        "blog|attachment|pos0|pos1\n" .

        // Field location: blog menu block > blog entry > edition > blog entry body
        // Display location: blog menu block > blog entry
        // User: Any user
        "blog|post|pos0|pos1\n" .

        // Field location: block calendar > date > new event > show more > description
        // Display location: block calendar > date
        // User: Any user
        "calendar|event_description|pos0|pos1\n" .

        // Field location: administration > users > accounts > cohorts > edition > description
        // Display location: administration > users > accounts > cohorts
        // User: Administrator
        "cohort|description|pos0|pos1\n" .

        // Prerequisites: Create a framework competency and a learning plan from administration > competencies
        // Field location: dashboard > learning plans > evidence of prior learning > add new evidence > files
        // Display location: dashboard > learning plans > evidence of prior learning
        // User: Administrator for the prerequisites and enrolled user for edition and display
        "core_competency|userevidence|pos0|pos1\n" .

        // Prerequisites: Authorize .openveo files in administration > appearance > courses > course summary files
        //                extensions
        // Field location: course > edition > course summary files
        // Display location: category holding the course
        // User: Course editor
        "course|overviewfiles|pos0|pos1\n" .

        // Field location: course > topic > edition > summary
        // Display location: course
        // User: Course editor
        "course|section|pos0|pos1\n" .

        // Field location: course > course summary
        // Display location: category holding the course
        // User: Course editor
        "course|summary|id0|pos0\n" .

        // Field location: administration > courses > manage courses and catagories > category > edition >
        //                 description
        // Display location: category
        // User: Administrator
        "coursecat|description|id0|pos0\n" .

        // Prerequisites: Enable outcomes in administration > advanced features > enable outcomes
        // Field location: course > outcomes > edit outcomes > add a new outcome > description
        // Display location: It doesn't seem to be displayed but it is added to outcomes CSV export
        // User: Administrator for prerequisites and course editor for edition and display
        "grade|outcome|pos0|pos1\n" .

        // Field location: administration > grades > scales > add a new scale > description
        // Display location: It doesn't seem to be displayed but it is added to outcomes CSV export
        // User: Administrator
        "grade|scale|pos0|pos1\n" .

        // Prerequisites: Set course grading method to "marking guide" in course > assignment module > edition >
        //                grade > grading method
        // Field location: course > assignment module > define marking guide > description
        // Display location: course > assignment module > advanced grading
        // User: Course editor
        "grading|description|pos0|pos1\n" .

        // Field location: course > users > groups > create group > group description
        // Display location: course > users > groups > add/remove users
        // User: Course editor
        "group|description|pos0|pos1\n" .

        // Field location: course > users > groups > groupings > create grouping > grouping description
        // Display location: course > users > groups > overview
        // User: Course editor
        "grouping|description|pos0|pos1\n" .

        // Field location: course > assignment module > edition > description
        // Display location: course > assignment module
        // User: Course editor
        "mod_assign|intro|id0|pos0\n" .

        // Field location: course > assignment module > edition > additional files
        // Display location: course > assignment module
        // User: Course editor
        "mod_assign|introattachment|pos0|pos1\n" .

        // Deprecated.
        "mod_assignment|intro|id0|pos0\n" .

        // Field location: course > book module > chapter > edition > content
        // Display location: course > book module and course > book module > chapter > print this chapter
        // User: Course editor
        "mod_book|chapter|pos0|pos1\n" .

        // Field location: course > book module > edition > description
        // Display location: course > book module > print book
        // User: Course editor
        "mod_book|intro|id0|pos0\n" .

        // Field location: course > chat module > edition > description
        // Display location: course > chat module
        // User: Course editor
        "mod_chat|intro|id0|pos0\n" .

        // Field location: course > choice module > edition > description
        // Display location: course > choice module
        // User: Course editor
        "mod_choice|intro|id0|pos0\n" .

        // Field location: course > database module > edition > description
        // Display location: course > database module
        // User: Course editor
        "mod_data|intro|id0|pos0\n" .

        // Field location: course > feedback module > edition > description
        // Display location: course > feedback module
        // User: Course editor
        "mod_feedback|intro|id0|pos0\n" .

        // Field location: course > feedback module > edit questions > add question label > contents
        // Display location: course > feedback module > edit questions
        // User: Course editor
        "mod_feedback|item|pos0|pos1\n" .

        // Field location: course > feedback module > edition > after submission > completion message
        // Display location: course > feedback module
        // User: Course editor
        "mod_feedback|page_after_submit|pos0|pos1\n" .

        // Field location: course > folder module > edition > files
        // Display location: course > folder module
        // User: Course editor
        "mod_folder|content|pos0|pos1\n" .

        // Field location: course > folder module > edition > description
        // Display location: course > folder module
        // User: Course editor
        "mod_folder|intro|id0|pos0\n" .

        // Field location: course > forum module > edition > description
        // Display location: course > forum module
        // User: Course editor
        "mod_forum|intro|id0|pos0\n" .

        // Field location: course > forum module > add a new discussion topic > message
        // Display location: course > forum module > topic
        // User: Course editor
        "mod_forum|post|pos0|pos1\n" .

        // Field location: course > glossary module > add new entry > attachment
        // Display location: course > glossary module
        // User: Course editor
        "mod_glossary|attachment|pos0|pos1\n" .

        // Field location: course > glossary module > add new entry > definition
        // Display location: course > glossary module
        // User: Course editor
        "mod_glossary|entry|post0|pos1\n" .

        // Field location: course > glossary module > edition > description
        // Display location: course > glossary module
        // User: Course editor
        "mod_glossary|intro|id0|pos0\n" .

        // Field location: course > imscp module > edition > description
        // Display location: It doesn't seem to be displayed
        // User: Course editor
        "mod_imscp|intro|id0|pos0\n" .

        // Field location: course > label module > edition > label text
        // Display location: course
        // User: Course editor
        "mod_label|intro|id0|pos0\n" .

        // Field location: course > lesson module > grade essays > click on the data of an essay > your comments
        // Display location: It doesn't seem to be displayed
        // User: Course editor
        "mod_lesson|essay_responses|pos0|pos1\n" .

        // Field location: course > lesson module > edition > description
        // Display location: It doesn't seem to be displayed
        // User: Course editor
        "mod_lesson|intro|id0|pos0\n" .

        // Field location: course > lesson module > edition > appearance > show more > linked media
        // Display location: course > lesson module > linked media block > click here to view
        // User: Course editor
        "mod_lesson|mediafile|id0|pos1\n" .

        // Field location: course > lesson module > edit > add a question page here > matching > matching pair 1 >
        //                 answer
        // Display location: course > lesson module > edit
        // User: Course editor
        "mod_lesson|page_answers|pos0|pos1\n" .

        // Field location: course > lesson module > edit > add a question page here > essay > page contents
        // Display location: course > lesson module > edit
        // User: Course editor
        "mod_lesson|page_contents|pos0|pos1\n" .

        // Field location: course > lesson module > edit > add a question page here > short answer > answer 1 >
        //                 response
        // Display location: course > lesson module > edit
        // User: Course editor
        "mod_lesson|page_responses|pos0|pos1\n" .

        // Field location: course > external tool module > edition > show more > activity description and check
        //                 "display description on course page"
        // Display location: course
        // User: Course editor
        "mod_lti|intro|id0|pos0\n" .

        // Field location: course > page module > edition > page content
        // Display location: course > page module
        // User: Course editor
        "mod_page|content|id0|pos1\n" .

        // Field location: course > page module > edition > description
        // Display location: course
        // User: Course editor
        "mod_page|intro|id0|pos0\n" .

        // Field location: course > quiz module > edition > description
        // Display location: course > quiz module
        // User: Course editor
        "mod_quiz|intro|id0|pos0\n" .

        // Field location: course > quiz module > edition > overall feedback > feedback
        // Display location: course > quiz module and submit quiz
        // User: Course editor for edition and enrolled user for display
        "mod_quiz|feedback|pos0|pos1\n" .

        // Field location: course > file module > edition > select files
        // Display location: course > file module
        // User: Course editor
        "mod_resource|content|id0|pos1\n" .

        // Field location: course > file module > edition > description
        // Display location: course > file module
        // User: Course editor
        "mod_resource|intro|id0|pos0\n" .

        // Field location: course > scorm module > edition > description
        // Display location: course > scorm module
        // User: Course editor
        "mod_scorm|intro|id0|pos0\n" .

        // Field location: course > survey module > edition > description
        // Display location: course > survey module
        // User: Course editor
        "mod_survey|intro|id0|pos0\n" .

        // Field location: course > URL module > edition > description
        // Display location: course > URL module
        // User: Course editor
        "mod_url|intro|id0|pos0\n" .

        // Field location: course > wiki module > edit > HTML format
        // Display location: course > wiki module
        // User: Course editor
        "mod_wiki|attachments|pos0|pos1\n" .

        // Field location: course > wiki module > edition > description
        // Display location: course > wiki module
        // User: Course editor
        "mod_wiki|intro|id0|pos0\n" .

        // Field location: course > workshop module > edition > feedback > conclusion
        // Display location: course > workshop module > close phase
        // User: Course editor
        "mod_workshop|conclusion|id0|pos0\n" .

        // Field location: course > workshop module > edition > submission settings > instructions for submission
        // Display location: course > workshop module > submission phase
        // User: Course editor
        "mod_workshop|instructauthors|id0|pos0\n" .

        // Field location: course > workshop module > edition > assessment settings > instructions for assessment
        // Display location: course > workshop module > assessment phase
        // User: Course editor
        "mod_workshop|instructreviewers|id0|pos0\n" .

        // Field location: course > workshop module > edition > description
        // Display location: course > workshop module > setup phase
        // User: Course editor
        "mod_workshop|intro|id0|pos0\n" .

        // Field location: course > workshop module > submission phase > start preparing your submission >
        //                 submission content
        // Display location: course > workshop module > submission phase > submission
        // User: Course editor
        "mod_workshop|submission_content|pos0|pos1\n" .

        // Field location: course > quiz > question bank > edition > general feedback
        // Display location: course > quiz module and submit quiz
        // User: Course editor for edition and enrolled user for display
        "question|generalfeedback|pos2|pos3\n" .

        // Field location: course > quiz > question bank > edition > question text
        // Display location: course > quiz module and submit quiz
        // User: Course editor for edition and enrolled user for display
        "question|questiontext|pos2|pos3\n" .

        // Field location: administration > appearance > manage tags > tag collection > collection > edition >
        //                 description
        // Display location: administration > appearance > manage tags > tag collection > collection
        // User: Administrator
        "tag|description|pos0|pos1\n" .

        // Field location: user > private files > files
        // Display location: private files block
        // User: Any user
        "user|private|pos0|pos1\n" .

        // Field location: user > profile > edition > description
        // Display location: user > profile
        // User: Any user
        "user|profile|id0|pos0\n" .

        // Prerequisites: Choose "accumulative grading" in course > workshop module > edition > gradings settings >
        //                grading strategy
        // Field location: course > workshop module > edit assessment form > description
        // Display location: course > workshop module > assessment phase > assess
        // User: Course editor
        "workshopform_accumulative|description|pos0|pos1\n" .

        // Prerequisites: Choose "comments" in course > workshop module > edition > gradings settings > grading
        //                strategy
        // Field location: course > workshop module > edit assessment form > description
        // Display location: course > workshop module > assessment phase > assess
        // User: Course editor
        "workshopform_comments|description|pos0|pos1\n" .

        // Prerequisites: Choose "number of errors" in course > workshop module > edition > gradings settings >
        //                grading strategy
        // Field location: course > workshop module > edit assessment form > description
        // Display location: course > workshop module > assessment phase > assess
        // User: Course editor
        "workshopform_numerrors|description|pos0|pos1\n" .

        // Prerequisites: Choose "rubric" in course > workshop module > edition > gradings settings > grading
        //                strategy
        // Field location: course > workshop module > edit assessment form > description
        // Display location: course > workshop module > assessment phase > assess
        // User: Course editor
        "workshopform_rubric|description|pos0|pos1",

        PARAM_RAW_TRIMMED
));
