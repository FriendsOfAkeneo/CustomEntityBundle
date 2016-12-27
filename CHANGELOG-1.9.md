1.9.0
-----

## New feature
- Generic import for basic reference data
- Generic export for basic reference data

## Improvements
- Compliance with Akeneo PIM entity API (Saver, Updater, Remover)

## BC Breaks:
- `Manager` class changes all its construct method parameters

- Move class from `Pim\Bundle\CustomEntityBundle\MassEditConnector\JobParameters\ConstraintCollectionProvider\QuickCsvExport` to `Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider\QuickCsvExport`
- Move class from `Pim\Bundle\CustomEntityBundle\MassEditConnector\JobParameters\DefaultValuesProvider\QuickCsvExport` to `Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider\QuickCsvExport`
- Move class from `Pim\Bundle\CustomEntityBundle\MassEditConnector\Reader\ReferenceDataReader` to `Pim\Bundle\CustomEntityBundle\Connector\Reader\Database\MassEditReferenceDataReader`

- Remove class `Pim\Bundle\CustomEntityBundle\MassEditConnector\Processor\ReferenceDataToFlatArrayProcessor`
- Remove class `Pim\Bundle\CustomEntityBundle\MassEditConnector\Writer\ReferenceDataWriter`

- Rename parameter `pim_custom_entity.mass_edit_connector.reader.reference_data.class` to `pim_custom_entity.reader.database.mass_edit_reference_data.class`
- Remove parameter `pim_custom_entity.mass_edit_connector.processor.reference_data_to_flat_array.class`
- Remove parameter `pim_custom_entity.mass_edit_connector.writer.reference_data.class`

- Rename service `pim_custom_entity.mass_edit_connector.reader.reference_data` to `pim_custom_entity.reader.database.mass_edit_reference_data`
- Rename service `pim_custom_entity.connector.job.job_parameters.default_values_provider.reference_data_quick_export` to `pim_custom_entity.job.job_parameters.default_values_provider.reference_data_quick_export`
- Rename service `pim_custom_entity.connector.job.job_parameters.constraint_collection_provider.reference_data_quick_export` to `pim_custom_entity.job.job_parameters.constraint_collection_provider.reference_data_quick_export`
- Remove service `pim_custom_entity.mass_edit_connector.processor.reference_data_to_flat_array`
- Remove service `pim_custom_entity.mass_edit_connector.processor.reference_data_writer`
