<?php
/**
 * Generated PHP model
 * @author David Spreekmeester | grrr.nl
 * @package Garp
 * @subpackage Spawn
 */
class Garp_Model_Spawn_Php_Model_Base extends Garp_Model_Spawn_Php_Model_Abstract {
	protected $_behaviorsThatRequireParams = array('Weighable');


	public function render() {
		$tableName 	= $this->getTableName();
		$model		= $this->getModel();
		
		$out = $this->_rl("<?php");
		$out .= $this->_rl('/* This file was generated by '. get_class() .' */');

		$out .= $this->_rl("class Model_Base_{$model->id} extends Garp_Model_Db {");

		/* Table */
		$out .= $this->_rl("protected \$_name = '{$tableName}';", 1, 2);

		/* Primary */
		$out .= $this->_rl("protected \$_primary = 'id';", 1, 2);

		/* This model's scheme, deducted from the combined Spawn model configurations. */
		$modelArray			= $this->_convertToArray($model);
		$modelArrayScript 	= Garp_Model_Spawn_Util::array2phpStatement($modelArray);
		$out .= $this->_rl("protected \$_configuration = " . $modelArrayScript .";", 1, 2);

		/* Joint view to include labels of singular relations */
		$jointViewProperty = $this->_renderJointViewProperty();
		$out.= $jointViewProperty;

		/* Default order */
		$defaultOrder = $this->_renderDefaultOrder();
		$out .= $defaultOrder;

		/* Relations */
		$relations = $this->_renderRelations();
		$out .= $relations;

		/* Default behaviors */
		$behaviors = $this->_renderBehaviors();
		$out .= $behaviors;
		
		if (!$this->getModel()->isTranslated()) {
			$out .= $this->_getRecordLabelSql();
		}

		$out .= $this->_rl('}', 0, 0);
		return $out;
	}

	protected function _renderBehaviors() {
		$behaviors = $this->getModel()->behaviors->getBehaviors();

		$out  = $this->_rl("public function init() {", 1);
		$out .= $this->_rl('parent::init();', 2);

		foreach ($behaviors as $behaviorName => $behavior) {
			$out .= $this->_renderBehaviorNeedingPhpModelObserver($behavior);
		}
		
		$out .= $this->_rl('}', 1, 2);

		return $out;
	}
	
	protected function _renderBehaviorNeedingPhpModelObserver(Garp_Model_Spawn_Behavior_Type_Abstract $behavior) {
		if (!$behavior->needsPhpModelObserver()) {
			return;
		}
		
		$behaviorOutput = $this->_renderBehavior($behavior);
		$out 			= $this->_rl($behaviorOutput, 2);
		
		return $out;
	}
	
	protected function _renderBehavior(Garp_Model_Spawn_Behavior_Type_Abstract $behavior) {
		$name	= $behavior->getName();
		$type	= $behavior->getType();
		$params = $name === 'Weighable' ?
			$behavior->getNonHabtmParams() :
			$behavior->getParams()
		;
		
		if (
			$params ||
			!in_array($name, $this->_behaviorsThatRequireParams)
		) {
			$paramsString = is_array($params) ?
				Garp_Model_Spawn_Util::array2phpStatement($params) :
				null
			;
			return "\$this->registerObserver(new Garp_Model_{$type}_{$name}({$paramsString}));";
		}
	}

	protected function _renderRelations() {
		$model 		= $this->getModel();
		$relations 	= $model->relations->getRelations();

		if (!count($relations)) {
			return;
		}
		
		/* Bindable */
		$out = $this->_renderBindable();

		/* ReferenceMap */
		$out .= $this->_renderReferenceMap();
		
		return $out;
	}
	
	protected function _renderBindable() {
		$model 		= $this->getModel();
		$relations 	= $model->relations->getRelations();

		$bindableModelNames = $this->_getBindableModelNames();
		$bindablesLines 	= array();

		foreach ($bindableModelNames as $modelName) {
			$bindableLines[] = $this->_rl("'Model_{$modelName}'", 2, 0);
		}
		
		$bindablesOutput = implode(",\n", $bindableLines);

		$out = $this->_rl('protected $_bindable = array(', 1);
		$out .= $this->_rl($bindablesOutput, 0);
		$out .= $this->_rl(');', 1, 2);

		return $out;
	}
	
	protected function _renderReferenceMap() {
		$relations 	= $this->getModel()->relations->getSingularRelations();
		$references = array();

		foreach ($relations as $relationName => $relation) {
			$references[] = $this->_renderReferenceMapEntry($relationName, $relation);
		}
		
		$referencesOutput = implode(",\n", $references);
		
		$out  = $this->_rl('protected $_referenceMap = array(', 1);
		$out .= $this->_rl($referencesOutput, 0);
		$out .= $this->_rl(');', 1, 3);
		
		return $out;
	}
	
	protected function _renderReferenceMapEntry($relationName, Garp_Model_Spawn_Relation $relation) {
		$entry = 
			  $this->_rl("'{$relationName}' => array(", 2)
			. $this->_rl("'refTableClass' => 'Model_{$relation->model}',", 3)
			. $this->_rl("'columns' => '{$relation->column}',", 3)
			. $this->_rl("'refColumns' => 'id'", 3)
			. $this->_rl(")", 2, 0)
		;
		
		return $entry;
	}
	
	protected function _getBindableModelNames() {
		$relations 	= $this->getModel()->relations->getRelations();
		$modelNames	= array();

		foreach ($relations as $relation) {
			$modelNames[] = $relation->model;
		}
		
		$modelNames = array_unique($modelNames);
		sort($modelNames);

		return $modelNames;
	}

	protected function _renderJointViewProperty() {
		$tableName 	= $this->getTableName();
		$prop		= "protected \$_jointView = '{$tableName}_joint';";
		$out		= $this->_rl($prop, 1, 2);
		
		return $out;
	}
	
	protected function _renderDefaultOrder() {
		$model 							= $this->getModel();
		$commaInOrder 					= strpos($model->order, ",") !== false;
		$noOpeningParenthesisInOrder	= strpos($model->order, "(") === false;
		$orderFieldNames				= explode(", ", $model->order);
		$orderFieldNamesStatement		= Garp_Model_Spawn_Util::array2phpStatement($orderFieldNames);

		$orderValueStatement = (
			$commaInOrder &&
			$noOpeningParenthesisInOrder
		) ?
			$orderFieldNamesStatement :
			"'{$model->order}'"
		;

		$orderStatement = "protected \$_defaultOrder = {$orderValueStatement};";
		$out 			= $this->_rl($orderStatement, 1, 2);

		return $out;
	}

	/**
	 * Compose the method to fetch composite columns as a string in a MySql query
	 * to use as a label to identify the record. These have to be columns in this table,
	 * to be able to be used flexibly in another query.
	 */
	protected function _getRecordLabelSql() {
		$tableName 				= $this->getTableName();
		$recordLabelFieldDefs 	= $this->_getRecordLabelFieldDefinitions();
		$labelColumnsListSql 	= implode(', ', $recordLabelFieldDefs);
		$glue 					= $this->_modelHasFirstAndLastNameListFields() ? ' ' : ', ';
		$sql 					= "CONVERT(CONCAT_WS('{$glue}', " . $labelColumnsListSql . ') USING utf8)';

		$out 	= $this->_rl("public function getRecordLabelSql(\$tableAlias = null) {", 1);
		$out 	.= $this->_rl("\$tableAlias = \$tableAlias ?: '{$tableName}';", 2);
		$out 	.= $this->_rl("return \"{$sql}\";", 2);
		$out 	.= $this->_rl('}', 1, 1);
		
		return $out;
	}
	
	protected function _modelHasFirstAndLastNameListFields() {
		$model = $this->getModel();

		try {
			return 
				$model->fields->getField('first_name') &&
				$model->fields->getField('last_name')
			;
		} catch (Exception $e) {}

		return false;
	}
	
	protected function _getRecordLabelFieldDefinitions() {
		$model			= $this->getModel();
		$listFieldNames = $model->fields->listFieldNames;
		$fieldDefs 		= array();

		foreach ($listFieldNames as $listFieldName) {
			try {
				$field = $model->fields->getField($listFieldName);
			} catch (Exception $e) {
				break;
			}

			if (
				!$field ||
				!$field->isSuitableAsLabel()
			) {
				break;
			}

			$fieldDefs[] = $this->_addFieldLabelDefinition($field->name);
		}

		if (!$fieldDefs) {
			$fieldDefs[] = $this->_addFieldLabelDefinition('id');
		}
		
		return $fieldDefs;
	}
	
	protected function _addFieldLabelDefinition($columnName) {
		return "IF(`{\$tableAlias}`.`{$columnName}` <> \\\"\\\", `{\$tableAlias}`.`{$columnName}`, NULL)";
	}
	
	/**
	 * Extracts the public properties from an iteratable object
	 * @param Mixed $obj 	At first, feed this a Garp_Model_Spawn_Model_Abstract, after which
	 * 						it calls itself with an array.
	 */
	protected function _convertToArray($obj) {
		$arr 	= array();
		$arrObj = is_object($obj) ? get_object_vars($obj) : $obj;

		foreach ($arrObj as $key => $val) {
			if (is_object($val)) {
				switch (get_class($val)) {
					case 'Garp_Model_Spawn_Relations':
						$val = $val->getRelations();
					break;
					case 'Garp_Model_Spawn_Behaviors':
						$val = $val->getBehaviors();
					break;
					case 'Garp_Model_Spawn_Fields':
						$val = $val->getFields();
				}

				$val = $this->_convertToArray($val);
			} elseif (is_array($val)) {
				$val = $this->_convertToArray($val);
			}
			$arr[$key] = $val;
		}

		return $arr;
	}
}