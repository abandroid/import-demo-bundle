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

class AddressLoader extends AbstractLoader
{
    /** @var XmlIterator $iterator */
    protected $iterator;

    public function initialize(): void
    {
        $this->state['addresses'] = [];

        $this->iterator = new XmlIterator(__DIR__.'/../Resources/data/address_data.xml', 'address');
        $this->iterator->rewind();
    }

    public function load(): void
    {
        if (!$this->iterator->valid()) {
            $this->deactivate();

            return;
        }

        $item = $this->iterator->current();
        $this->state['addresses'][] = $item;
        $this->iterator->next();

        $this->importer->setActiveLoader(EmployeeLoader::class);
    }
}
