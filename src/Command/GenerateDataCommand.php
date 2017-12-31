<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Import\Bundle\ImportDemoBundle\Command;

use DOMDocument;
use DOMElement;
use Endroid\Import\Exception\LockException;
use Endroid\Import\ProgressHandler\ProgressBarProgressHandler;
use Endroid\Import\ProgressHandler\ProgressHandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class GenerateDataCommand extends Command
{
    /**
     * @var ProgressHandlerInterface
     */
    protected $progressHandler;

    /**
     * @var int
     */
    protected $count;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('endroid:import-demo:generate-data')
            ->addArgument('count', InputArgument::OPTIONAL, null, 100)
            ->setDescription('Generate demo data')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler($this->getName());
        if (!$lock->lock()) {
            throw new LockException('Lock could not be obtained');
        }

        $this->count = $input->getArgument('count');

        $this->progressHandler = new ProgressBarProgressHandler($input, $output);

        $this->generateGenericXml('address');
        $this->generateGenericXml('office');
        $this->generateGenericXml('employee');
        $this->generateGenericXml('product');
        $this->generateGenericXml('category');
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function generateGenericXml($name)
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $collection = $document->createElement($name.'s');
        $document->appendChild($collection);

        for ($n = 1; $n <= $this->count; ++$n) {
            $element = $document->createElement($name);
            $element->appendChild($document->createElement('id', $n));
            $element->appendChild($document->createElement('label', ucfirst($name).' '.$n));
            if (method_exists($this, 'add'.ucfirst($name).'Fields')) {
                $this->{'add'.ucfirst($name).'Fields'}($element, $document);
            }
            $collection->appendChild($element);
        }

        $xmlString = $document->saveXML();

        file_put_contents(__DIR__.'/../Resources/data/'.$name.'_data.xml', $xmlString);
    }

    /**
     * @param DOMElement  $element
     * @param DOMDocument $document
     */
    protected function addOfficeFields(DOMElement $element, DomDocument $document)
    {
        $element->appendChild($document->createElement('location_id', rand(1, $this->count)));
    }

    /**
     * @param DOMElement  $element
     * @param DOMDocument $document
     */
    protected function addEmployeeFields(DOMElement $element, DomDocument $document)
    {
        $element->appendChild($document->createElement('location_id', rand(1, $this->count)));
        $element->appendChild($document->createElement('office_id', rand(1, $this->count)));
    }
}
