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
 * Class for loading/storing data purposes from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Class for loading/storing data purposes from the DB.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'dataprivacy_purpose';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The purpose name.',
            ),
            'description' => array(
                'type' => PARAM_RAW,
                'description' => 'The purpose description.',
                'null' => NULL_ALLOWED,
                'default' => '',
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'retentionperiod' => array(
                'type' => PARAM_INT,
                'description' => 'Retention period. Time to store the data since it is not used any more. In seconds.',
                'default' => '0',
            ),
            'protected' => array(
                'type' => PARAM_INT,
                'description' => 'Data retention with higher precedent over user\'s request to be forgotten.',
                'default' => '0',
            ),
        );
    }

    /**
     * Is this purpose used?.
     *
     * @return null
     */
    public function is_used() {

        if (\tool_dataprivacy\contextlevel::is_purpose_used($this->get('id')) ||
                \tool_dataprivacy\context_instance::is_purpose_used($this->get('id'))) {
            return true;
        }

        $pluginconfig = get_config('tool_dataprivacy');
        $levels = \context_helper::get_all_levels();
        foreach ($levels as $level => $classname) {

            list($purposevar, $unused) = tool_dataprivacy_var_names_from_context($classname);
            if (!empty($pluginconfig->{$purposevar}) && $pluginconfig->{$purposevar} == $this->get('id')) {
                return true;
            }
        }

        return false;
    }
}
