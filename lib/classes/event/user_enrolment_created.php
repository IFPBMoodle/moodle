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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User enrolment created event.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event class for when user is enrolled in a course.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string enrol: name of enrolment instance.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_enrolment_created extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'user_enrolments';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserenrolmentcreated', 'core_enrol');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' enrolled the user with id '$this->relateduserid' using the enrolment method " .
            "'{$this->other['enrol']}' in the course with id '$this->courseid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/enrol/users.php', array('id' => $this->courseid));
    }

    /**
     * Return name of the legacy event, which is replaced by this event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'user_enrolled';
    }

    /**
     * Return user_enrolled legacy event data.
     *
     * @return \stdClass
     */
    protected function get_legacy_eventdata() {
        $legacyeventdata = $this->get_record_snapshot('user_enrolments', $this->objectid);
        $legacyeventdata->enrol = $this->other['enrol'];
        $legacyeventdata->courseid = $this->courseid;
        return $legacyeventdata;
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'course', 'enrol', '../enrol/users.php?id=' . $this->courseid, $this->courseid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['enrol'])) {
            throw new \coding_exception('The \'enrol\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // User enrolments table is not mappable.
        return array('db' => 'user_enrolments', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        return false;
    }
}
