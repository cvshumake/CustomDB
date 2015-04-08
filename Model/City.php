<?php

class Model_City extends Model {

	const FIELD_NAME = 'Name';

	private static $metadata = array(
			Model::FIELD_ID => array(
				Model::METADATA_DATATYPE => Model::DATATYPE_INTEGER,
				Model::METADATA_NULLABLE => false,
				Model::METADATA_UNSIGNED => false,
			),
			self::FIELD_NAME => array(
				Model::METADATA_DATATYPE => Model::DATATYPE_STRING,
				Model::METADATA_NULLABLE => false,
				Model::METADATA_LENGTH => 35,
			),
		);
		
	public function getTableName() {
		return 'City';
	}

	public static function getMetadata() {
		return self::$metadata;
	}

}
