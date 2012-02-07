<?php

foreach ( $schema['tables'] AS $tableName => $tableDefinition ) {
	$db->table($tableName, $tableDefinition);
}
