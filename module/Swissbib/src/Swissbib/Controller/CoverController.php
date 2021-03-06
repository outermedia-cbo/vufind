<?php
/**
 * Cover Controller
 *
 * PHP Version 5
 *
 * Copyright (C) Villanova University 2011.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind2
 * @package  Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;
use Swissbib\Cover\Loader;
use VuFind\Controller\CoverController as VFCoverController;

/**
 * Generates covers for book entries
 *
 * @category VuFind2
 * @package  Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class CoverController extends VFCoverController
{
    /**
     * Get the cover loader object
     *
     * @return Loader
     */
    protected function getLoader()
    {
        // Construct object for loading cover images if it does not already exist:
        if (!$this->loader) {
            $cacheDir = $this->serviceLocator->get('VuFind\CacheManager')
                ->getCache('cover')->getOptions()->getCacheDir();
            $this->loader = new Loader(
                $this->getConfig(),
                $this->serviceLocator->get('VuFind\ContentCoversPluginManager'),
                $this->serviceLocator->get('VuFindTheme\ThemeInfo'),
                $this->serviceLocator->get('VuFind\Http')->createClient(),
                $cacheDir
            );
            \VuFind\ServiceManager\Initializer::initInstance(
                $this->loader, $this->serviceLocator
            );
        }
        return $this->loader;
    }
}
