<?php
class KeenActiveDataProvider extends CActiveDataProvider {
	/**
	 * You can specify in your DataProvider config which relations you want to load in a keen way.
	 * Example:
	 * $dataProvider=new KeenActiveDataProvider('Post', array(
	 *     'criteria'=>array(
	 *     'condition'=>'status=1',
	 *     'order'=>'create_time DESC',
	 *     'with'=>array('author'),
	 *   ),
	 *   'pagination'=>array(
	 *     'pageSize'=>20,
	 *   ),
	 *   'withKeenLoading'=>array('categories'),
	 * ));
	 * // $dataProvider->getData() will return a list of Post objects
	 * @var array list of relation names
	 */
	public $withKeenLoading = array();
	private $_keenKeys = array();

	/**
	 * Fetches the data from the persistent data storage.
	 * Additionally, calls KeenActiveDataProvider::afterFetch method
	 * @return array list of data items
	 */
	protected function fetchData() {
		$data = parent::fetchData();
		if ($data && $this->withKeenLoading) {
			$data = $this->afterFetch($data);
		}
		return $data;
	}

	private function _loadKey($attr) {
		if (!isset($this->_keenKeys[$attr]))
			$this->_keenKeys[$attr] = array();
		foreach ($this->getData() as $i => $data) {
			$this->_keenKeys[$attr] = $data->$attr;
		}
	}

	private function _loadKeys($attrs) {
		if (is_array($attrs)) {
			if ($attrs) {
				foreach ($attrs as $attr) {
					$this->_loadKey($attr);
				}
			}
		} elseif ($attrs) {
			$this->_loadKey($attrs);
		}
	}

	/**
	 * Loads additional related data in bulk, instead of each model lazy loading its related data
	 */
	protected function afterFetch($data) {
		foreach ($this->withKeenLoading as $relationName) {
			// if using hierarchical relation names, load the deep models using with()
			if (strpos($relationName, '.') === false) {
				$with = '';
			} else {
				$with = explode('.', $relationName);
				$relationName = array_shift($with);
			}
			$relation = $this->model->metaData->relations[$relationName];
			Yii::trace(CVarDumper::dumpAsString($relation),'<b>DebugTrace: $relation</b>');
			Yii::trace(CVarDumper::dumpAsString($this->model->metaData->tableSchema->primaryKey),'<b>DebugTrace: primaryKey</b>');
			$fk = $relation->foreignKey;
			$relatedAttrs = array();
			if ($relation instanceof CBelongsToRelation) {
				if (is_array($relation->foreignKey)) {
					foreach ($relation->foreignKey as $k => $v) {
						$self = is_string($k) ? $k : $v;
						$relatedAttrs[$self] = CActiveRecord::model($relation->className)->metaData->tableSchema->primaryKey;
					}
				} else {
					$self = $relation->foreignKey;
					$relatedAttrs[$self] = CActiveRecord::model($relation->className)->metaData->tableSchema->primaryKey;
				}
			} else {
				if (is_array($relation->foreignKey)) {
					foreach ($relation->foreignKey as $k => $v) {
						$self = is_string($k) ? $k : $this->model->metaData->tableSchema->primaryKey;
						if(isset($relatedAttrs[$self]))
							if(is_array($relatedAttrs[$self]))
								$relatedAttrs[$self][] = $v;
							else
								$relatedAttrs[$self] = array($relatedAttrs[$self], $v);
						else
							$relatedAttrs[$self] = $v;
					}
				} else {
					$self = $this->model->metaData->tableSchema->primaryKey;
					$relatedAttrs[$self] = $relation->foreignKey;
				}
			}
			Yii::trace(CVarDumper::dumpAsString($relatedAttrs),'<b>DebugTrace: $relatedAttrs</b>');
			Yii::app()->end();

			$this->_loadKeys($keyAttrs);

			// NIOT DONE FROM HERE DOWN

			// Load all the related data
			$relatedModels = CActiveRecord::model($relations[$relationName][1])
				->findAllByAttributes(
				array($relations[$relationName][2] => $pks),
				array('with' => $with)
			);
			// put the related data in the dataprovider
			if ($relatedModels) {
				$newRelatedData = array();
				foreach ($relatedModels as $i => $relatedModel) {
					$newRelatedData[$relatedModel->{$relations[$relationName][2]}][] =
						$relatedModel;
				}
				foreach ($data as $dataItem) {
					if (isset($newRelatedData[$dataItem->{$this->model->tableSchema->primaryKey}])) {
						$dataItem->$relationName =
							$newRelatedData[$dataItem->{$this->model->tableSchema->primaryKey}];
					}
				}
			}
		}

		return $data;
	}
}