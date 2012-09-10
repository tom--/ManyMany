<?php
/**
 * KeenActiveDataProvider implements a data provider based on ActiveRecord and is
 * extended from CActiveDataProvider.
 *
 * The {@link keenWith} property specifies the relations that the data provider (groups
 * and) loads in the "keen" manner, i.e. using a single query after selecting its
 * primary AR models but before returning the requested {@link data} to the caller.
 *
 * KeenActiveDataProvider groups models (see {@link CDbCriteria->group}) according to
 * the primary key(s) of its primary AR model class.
 *
 * Any relation specified in both the {@link with} and the {@link keenWith}
 * properties causes the data provider to not eagerly load columns from that
 * relation; as though array('relation' => array('select' => false)) were set.
 *
 * KeenActiveDataProvider may be used as follows:
 * <code>
 * <?php
 * $dataProvider=new KeenActiveDataProvider('Post', array(
 *     'criteria'=>array(
 *         'condition'=>'status=1',
 *         'order'=>'create_time DESC',
 *         'with'=>array('author'),
 *     ),
 *     'pagination'=>array(
 *         'pageSize'=>20,
 *     ),
 *     'keenWith'=>array('categories'),
 * ));
 * // $dataProvider->getData() will return a list of Post objects with their related data
 * ?>
 * </code>
 *
 * @property CDbCriteria $criteria The query criteria.
 * @property CSort $sort The sorting object. If this is false, it means the sorting is disabled.
 * @property mixed $keenWith The relations specified here as a comma seperated string
 * or array will be loaded in a keen fashion.
 *
 * @author yJeroen <http://www.yiiframework.com/forum/index.php/user/39877-yjeroen/>
 * @author tom[] <?>
 */
class KeenActiveDataProvider extends CActiveDataProvider {

	/**
	 * The relations that the data provider loads "keenly".
	 *
	 * This property spefifies one or relations in a maner similar to {@link CDbCriteria::$with}.
	 * It may be a string or an array. As a string it should be one or more relation names
	 * separated by commans. If an array, each element should be either a relation name
	 * string or a key => vlaue pair in which the key is the relation name and the
	 * value is an array of relation options (see {@link CActiveRecord::relations}). This
	 * final form allows the user to extend or override a relation's options on-the-fly.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $dataProvider = new KeenActiveDataProvider('Post', array(
	 *   'criteria' => array(
	 *     'condition' => 'status = 1',
	 *     'with' => array('author'),
	 *   ),
	 *   'pagination' => array(
	 *     'pageSize' => 20,
	 *   ),
	 *   'keenWith' => array(
	 *     'simpleRelationName',
	 *     'author' => array('select' => 'id, name'),
	 *     'comments' => array('condition' => 'approved=1', 'order' => 'create_time'),
	 *   )
	 * ));
	 * ?>
	 * </code>
	 *
	 * Finally, keenWith may be specified as a multi-dimensional array, in which
	 * case the data provider "keenly" loads related models in a number of queries,
	 * each corresponding to one element of the top level array, e.g.
	 * 'keenWith' => array(array('relationA', 'relationB), array('relationC')).
	 * This allows the user some optimization control.
	 *
	 * @param mixed $value sets the relations that will be loaded "keenly".
	 */
	public $keenWith;

	/**
	 * @var array Normalized internal version of the user supplied $keenWith config.
	 */
	private $_keenWith = array();

	/**
	 * Fetches the data from the persistent data storage.
	 *
	 * Additionally, calls KeenActiveDataProvider::afterFetch method
	 *
	 * @return array list of data items
	 */
	protected function fetchData() {
		if ($this->keenWith) {
			$with = $this->criteria->with;
			$this->_prepareKeenLoading();
		} else {
			$with = false;
		}
		$data = parent::fetchData();
		if ($data && $this->_keenWith) {
			$data = $this->afterFetch($data);
		}
		if ($with !== false) {
			$this->criteria->with = $with;
		}
		return $data;
	}

	/**
	 * @param $relation mixed
	 * @return bool
	 */
	private function _isMultiRelation($relation) {
		return (strpos($relation, '.') !== false
			|| (!$this->model->metaData->relations[$relation] instanceof CHasOneRelation
				&& !$this->model->metaData->relations[$relation] instanceof CBelongsToRelation));
	}

	/**
	 * Modify the DP's criteria to:
	 *  - disable eager loading of keen loading relations
	 *  - group by rows in the main model.
	 */
	private function _prepareKeenLoading() {
		// Normalize the keenWith input parameter.
		$this->_keenWith = is_string($this->keenWith)
			? preg_split('%\s*,\s*%', trim($this->keenWith))
			: (array) $this->keenWith;
		$newWithKeen = array();
		foreach ($this->_keenWith as $k => $v) {
			if (!is_integer($k) || !is_array($v)) {
				unset($this->_keenWith[$k]);
				$newWithKeen[$k] = $v;
			}
		}
		$this->_keenWith[] = $newWithKeen;

		if ($this->criteria->with) {
			$this->criteria->with = (array) $this->criteria->with;
			// Don't load relations in both $this->criteria->with and $this->keenWith.
			// Setting a relation's 'select' option false defeats normal CADP loading.
			foreach ($this->criteria->with as $k => $v) {
				if ((is_integer($k) && $this->_isMultiRelation($v))
					|| (!is_integer($k) && $this->_isMultiRelation($k))
				) {
					foreach ($this->_keenWith as $groupedKeen) {
						foreach ($groupedKeen as $keenKey => $keenValue) {
							if (is_integer($k) && $v === $keenValue) {
								unset($this->criteria->with[$k]);
								$this->criteria->with[$v] = array('select' => false);
							} elseif ((is_integer($keenKey) && $k === $keenValue)
								|| (is_string($keenKey) && $k === $keenKey)
							) {
								$this->criteria->with[$k] = array('select' => false);
							}
						}
					}
				} else {
					foreach ($this->_keenWith as $groupedKey => $groupedKeen) {
						foreach ($groupedKeen as $keenKey => $keenValue) {
							if ((is_integer($k) && $v === $keenValue)
								|| (is_integer($keenKey) && $k === $keenValue)
								|| (is_string($keenKey) && $k === $keenKey)
							) {
								unset($this->_keenWith[$groupedKey][$keenKey]);
							}
						}
					}
				}
			}

			// Set the CDbriteria::$group to the DP's model's PK
			$pkNames = (array) $this->model->tableSchema->primaryKey;
			foreach ($pkNames as $k => $v) {
				$pkNames[$k] = $this->model->tableAlias . '.' . $v;
			}
			$this->criteria->group = implode(',', $pkNames);
		}
	}

	/**
	 * Loads the primary keys and values of the found models in an array.
	 *
	 * @param array $data An array of models returned by CActiveDataProvider::fetchData()
	 * @return array The keys will be the column name of the primary key of the model
	 * and the value will be an array of the primary key values of the models that have
	 * been loaded by CActiveDataProvider::fetchData()
	 */
	private function _loadKeys($data) {
		$pks = array();
		foreach ((array) $this->model->tableSchema->primaryKey as $pkName) {
			foreach ($data as $dataItem) {
				$pks[$pkName][] = $dataItem->$pkName;
			}
		}
		return $pks;
	}

	/**
	 * Perform eager loading of related models into the data provider.
	 *
	 * @param array $data An array of models returned by CActiveDataProvider::fetchData()
	 * @return array $data An array of models with related data Keenly loaded.
	 */
	protected function afterFetch($data) {

		$pks = $this->_loadKeys($data);
		$relatedModels = array();
		if ($this->_keenWith) {
			foreach ($this->_keenWith as $keenGroup) {
				/** @var $relatedModels CActiveRecord[] */
				$relatedModels = $this->model->findAllByAttributes($pks,
					array('select' => $this->criteria->group, 'with' => $keenGroup)
				);
			}
			foreach ($data as $model) {
				foreach ($relatedModels as $relatedModel) {
					$same = false;
					foreach ((array) $this->model->tableSchema->primaryKey as $pkName) {
						if ($model->$pkName === $relatedModel->$pkName) {
							$same = true;
						}
					}
					if ($same === true) {
						foreach ($this->model->metaData->relations as $relation) {
							if ($relatedModel->hasRelated($relation->name)) {
								$model->{$relation->name} = $relatedModel->{$relation->name};
							}
						}
					}
				}
			}
		}

		return $data;
	}
}