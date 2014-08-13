CHANGELOG 
=========

1.3.0
-----

## BC Breaks

 - now compatible with version 1.2.x of PIM CE
 - RemoveAction and MassRemoveAction are now called DeleteAction and MassDeleteAction
 - remove and mass_remove action types are now called delete and mass_delete
 - pim_customentity_remove route is now named pim_customentity_delete

1.2.0
-----

## Features

- Added events on the actions
- Added ACL support

# Bug fixes
 - Fixed oro titles

1.1.1
-----

## Bug fixes

- Fixed bug when displaying value for option with empty code

1.1.0
-----

### Features

- Strategies are replaced by actions
- Added abstract custom entities
- Added automatic configuration of custom datagrids

### BC Breaks

- Default configuration and strategy do not use quick create
- Removed unused datagrid_namespace configuration option
- Grid names are not suffixed by -grid anymore, and are the same as custom entity names

