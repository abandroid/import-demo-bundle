# Import Demo Bundle

The import covers most of the complexity that can be present in imports. The
following command will generate several CSV and XML files containing the demo
source data.

```
bin/console endroid:import-demo:generate-data
```

Now you can start importing the generated data.

```
bin/console endroid:import:demo-data
```

## Domain

The domain is about product factories and their employees: one factory has many
employees who work to create specific categories of products. Each factory or
employee has an address.

## Data source

The demo has several sources: the OfficeSoapClient provides calls to return all
product data for a specific office and the office address. We also have a client
that loads the CSV containing the employee data and addresses.

## Solutions

### CSV Data processing

The CSV data has multiple entities in one line: the employee and his address.
You can inspect the loaders to see how these work together to create separate
entities.

### Processing nested structures

The SoapClient returns a tree containing the offices, the product categories
and products per category.

### Filtering nested structures

The products have several logo's of which only the web variant should be imported.
This data is available in the logos section. Normally we need write a loop to
see what logo should be used. However, by combining the property access and
expression language components, we can retrieve this in one line.

```
$logoUrl = $accessor->get('logos.logo[variant=web].url', $productXml);
```

### Database flushes performance

The resulting entities are persisted and flushed to the database. However if we
would flush each change this would make the import very slow because of the overhead
incurred with each flush. On the other hand, waiting until we have all data will
consume all available memory. The flusher library takes care of this. Just call
flush after each change and the flusher will automatically learn the best moment
to flush. Don't forget to call finish at the end to make sure any pending flushes
are taken care of (otherwise you will get an error message).

### Insert or update

Often entities already exist or you have to be able to run the import multiple
times without creating new entities all the time when you run a sync every hour.
You can enforce this by injecting the repository class in the loader. When the
local ID differs from the remote ID it is recommended to store the source ID
with the entity to make sure it can be traced back later if needed.

### Locking mechanism

Running a single command in parallel with itself can result in duplicates, data
loss or errors. The import library has a locking mechanism enabled by default
to prevent a command from running in parallel with itself. You can test this
in the demo by opening two terminals at the same time and starting the command
above in both terminals. The second terminal should show a warning instead of
starting the import process again.

## Data sanitation

Offices in the CSV are matched by name with offices in the XML. You can use the
data sanitize bundle with the following configuration to see what entities were
imported and merge entities that represent the same office. All relations
will be maintained during the process.

```
endroid_data_sanitize:
    entities:
        office:
            class: Endroid\Import\Bundle\ImportDemoBundle\Entity\Office
            fields: [ 'id', 'name' ]
```
