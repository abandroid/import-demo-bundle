<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Import\Bundle\ImportDemoBundle\Loader;

use Endroid\Import\Loader\AbstractLoader;
use XmlIterator\XmlIterator;

class OfficeLoader extends AbstractLoader
{
    /** @var XmlIterator $iterator */
    protected $iterator;

    public function initialize(): void
    {
        $this->state['offices'] = [];

        $this->iterator = new XmlIterator(__DIR__.'/../Resources/data/office_data.xml', 'office');
        $this->iterator->rewind();
    }

    public function load(): void
    {
        if (!$this->iterator->valid()) {
            $this->deactivate();

            return;
        }

        $item = $this->iterator->current();
        $this->state['offices'][] = $item;
        $this->iterator->next();

        $this->importer->setActiveLoader(AddressLoader::class);
    }
}
