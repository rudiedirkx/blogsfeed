<?php

foreach ( $schema AS $tableName => $tableDefinition ) {
	// New table?
	$created = $db->table($tableName, $tableDefinition);

	// Table existed
	if ( null === $created ) {
		// Retrieve columns
		$actualColumns = $db->columns($tableName);

		// Build SQL for missing columns
		$missingColumns = array();
		foreach ( $tableDefinition AS $columnName => $columnDefinition ) {
			is_int($columnName) && ($columnName = $columnDefinition) && ($columnDefinition = array());

			// Ensure its existance
			$db->column($tableName, $columnName, $columnDefinition);
		}
	}
}
