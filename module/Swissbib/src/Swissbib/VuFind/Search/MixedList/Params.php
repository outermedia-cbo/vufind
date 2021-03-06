<?php
/**
 * Mixed List aspect of the Search Multi-class (Params)
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Search_MixedList
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://swissbib.ch Main Site
 */
namespace Swissbib\VuFind\Search\MixedList;

use VuFind\Search\MixedList\Params as VuFindMixedListParams;

/**
 * Search Mixed List Parameters
 * We need this class simply because the AbstractFactory Mechanism for
 * MixedListResultsis looking up params and options for the MixedList in the same
 * namespace and for the Results of MixedList we need an extension
 *
 * @category VuFind
 * @package  Search_MixedList
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class Params extends VuFindMixedListParams
{
}
