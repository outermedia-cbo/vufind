<?php
/**
 * SolrMarcZeroFieldTest
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\RecordDriver;

/**
 * SolrMarcZeroFieldTest
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SolrMarcZeroFieldTest extends SolrMarcTestCase
{
    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->initialize('marc-zero-field-bug.json');
    }

    /**
     * TestZeroFieldInSubjectHeadings
     *
     * @return void
     */
    public function testZeroFieldInSubjectHeadings()
    {
        $subjectHeadings = $this->driver->getAllSubjectHeadings();

        // This item contains a zero field
        $testItem = $subjectHeadings[5];

        $this->assertEquals('Indianer', $testItem[0]);
        //		$this->assertEquals('(DE-588)4026718-0', $testItem['6500']);
    }
}
