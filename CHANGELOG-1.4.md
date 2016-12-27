1.4.0
-----

## New features
- Added global configuration options for entities (see documentation)
- Added new event for configuration option resolving
- Added manager registry
- Added save options for form actions
- Added quick export action


## BC Breaks
- Constructor for Action\AbstractAction has changed
- Constructor for Configuration\Configuration has changed
- Constructor for Configuration\Registry has changed
- Constructor for Manager\Manager has changed
- Removed all validation for abstract entities
- Changed signature of ManagerInterface::save()
