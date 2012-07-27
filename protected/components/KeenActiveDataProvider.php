<?php
/**
 * KeenActiveDataProvider implements a data provider based on ActiveRecord and is
 * extended from CActiveDataProvider.
 *
 * KeenActiveDataProvider provides data in terms of ActiveRecord objects. It uses 
 * the AR {@link CActiveRecord::findAll} method to retrieve the data from database. 
 * The {@link criteria} property can be used to specify various query options. If 
 * you add a 'with' option to the criteria, and the same relations are added to the 
 * 'withKeenLoading' option, they will be automatically set to select no columns. 
 * ie. array('author'=>array('select'=>false)
 * 
 * There will be a CDbCriteria->group set automatically, that groups the model
 * to its own primary keys.
 * 
 * The relation names you specify in the 'withKeenLoading' property of the 
 * configuration array will be loaded in a keen fashion. A seperate database
 * query will be done to pull the data of those specified related models.
 *
 * KeenActiveDataProvider may be used in the following way:
 * <pre>
 * $dataProvider=new KeenActiveDataProvider('Post', array(
 *     'criteria'=>array(
 *         'condition'=>'status=1',
 *         'order'=>'create_time DESC',
 *         'with'=>array('author'),
 *     ),
 *     'pagination'=>array(
 *         'pageSize'=>20,
 *     ),
 *     'withKeenLoading'=>array('categories'),
 * ));
 * // $dataProvider->getData() will return a list of Post objects with their related data
 * </pre>
 *
 * @property CDbCriteria $criteria The query criteria.
 * @property CSort $sort The sorting object. If this is false, it means the sorting is disabled.
 * @property mixed $withKeenLoading The relations specified here as a comma seperated string 
 * or array will be loaded in a keen fashion.
 *
 * @author yJeroen <http://www.yiiframework.com/forum/index.php/user/39877-yjeroen/>
 * @author tom[] <?>
 */
class KeenActiveDataProvider extends CActiveDataProvider {

	private $_withKeenLoading = array();
	private $_keenKeys = array();

	/*
	 * Specifies which related objects should be Keenly loaded.
	 * This method takes variable number of parameters. Each parameter specifies
	 * the name of a relation or child-relation. These parameters will be used in 
	 * the criteria for CActiveRecord::model()->findAllByAttributes($data, $criteria).
	 * 
	 * By default, the options specified in {@link relations()} will be used to do 
	 * relational query. In order to customize the options on the fly, we should 
	 * pass an array parameter to the withKeenLoading parameter of the DataProviders 
	 * configuration array. 
	 * For example,
	 * <pre>
	 * $dataProvider=new KeenActiveDataProvider('Post', array(
	 *     'criteria'=>array(
	 *        'condition'=>'status=1',
	 *        'with'=>array('author'),
	 *   ),
	 *   'pagination'=>array(
	 *     'pageSize'=>20,
	 *   ),
	 *   'withKeenLoading'=>array(
	 *     'author'=>array('select'=>'id, name'),
	 *     'comments'=>array('condition'=>'approved=1', 'order'=>'create_time'),
	 *   )
	 * ));
	 * </pre>
	 * 
	 * withKeenLoading can be set as a string with comma seperated relation names, 
	 * or an array. The array keys are relation names, and the array values are 
	 * the corresponding query options.
	 * 
	 * In some cases, you don't want all relations to be Keenly loaded in a single
	 * query because of data efficiency. In that case, you can seperate relations
	 * using a multidimensional array. (Arrays inside an array.) Each array will 
	 * be keenly loaded in a seperate query.
	 * Example:
	 * 'withKeenLoading'=>array( array('relationA','relationB),array('relationC') )
	 * 
	 * @param mixed the relational query criteria. This is used for fetching 
	 * related objects in a Keen loading fashion.
	 */
	public function setWithKeenLoading($value)
	{
		if(is_string($value))
			$this->_withKeenLoading=explode(',',$value);
		else
			$this->_withKeenLoading=(array)$value;
		$newWithKeen=array();
		foreach($this->_withKeenLoading as $k=>$v)
		{
			if(!(is_integer($k)&&is_array($v)))
			{
				unset($this->_withKeenLoading[$k]);
				$newWithKeen[$k] = $v;
			}
		}
		$this->_withKeenLoading[] = $newWithKeen;
	}
	
	/**
	 * Fetches the data from the persistent data storage.
	 * Additionally, calls KeenActiveDataProvider::afterFetch method
	 * @return array list of data items
	 */
	protected function fetchData() {
		if ($this->_withKeenLoading) {
			$this->_prepareKeenLoading();
		}
		$data = parent::fetchData();
		if ($data && $this->_withKeenLoading) {
			$data = $this->afterFetch($data);
		}
		return $data;
	}
	
	/*
	 * Sets each of the relations in the CDbriteria::with that have also been set 
	 * in KeenActiveDataProvider::$withKeenLoading, to the value of
	 * array('select'=>false), to not unnecessarily load data. The related
	 * data will be loaded in a Keen fashion.
	 */
	private function _prepareKeenLoading()
	{
		if(!empty($this->criteria->with))
		{
			$this->criteria->with=(array)$this->criteria->with;
			foreach((array)$this->criteria->with as $k=>$v)
			{
				foreach($this->_withKeenLoading as $groupedKeen)
				{
					foreach($groupedKeen as $keenKey=>$keenValue)
					{
						if(is_integer($k) && $v===$keenValue) {
							unset($this->criteria->with[$k]);
							$this->criteria->with[$v] = array('select'=>false);
						}
						elseif((is_integer($keenKey) && $k===$keenValue) || (is_string($keenKey) && $k===$keenKey)) {
							$this->criteria->with[$k] = array('select'=>false);
						}
					}
				}
			}
			$pkNames = (array)$this->model->tableSchema->primaryKey;
			foreach($pkNames as $k=>$v) {
				$pkNames[$k] = $this->model->tableAlias.'.'.$v;
			}
			$this->criteria->group = implode(',',$pkNames);
		}
	}

	/*
	 * Loads the primary keys and values of the found models in an array.
	 * @param array $data An array of models returned by CActiveDataProvider::fetchData()
	 * @return array The keys will be the column name of the primary key of the model
	 * and the value will be an array of the primary key values of the models that have 
	 * been loaded by CActiveDataProvider::fetchData()
	 */
	private function _loadKeys($data) {
		$pks = array();
		foreach((array)$this->model->tableSchema->primaryKey as $pkName)
		{
			foreach ($data as $dataItem) {
				$pks[$pkName][] = $dataItem->$pkName;
			}
		}
		return $pks;
	}

	/**
	 * Loads additional related data in bulk, instead of each model lazy loading its related data
	 * @param array $data An array of models returned by CActiveDataProvider::fetchData()
	 * @return array $data An array of models with related data Keenly loaded.
	 */
	protected function afterFetch($data) {
		
		$pks = $this->_loadKeys($data);
		foreach($this->_withKeenLoading as $keenGroup)
		{
			$relatedModels = $this->model->findAllByAttributes($pks,
								array('select'=>$this->criteria->group, 'with'=>$keenGroup)
			);
		}
		foreach($data as $model)
		{
			foreach($relatedModels as $relatedModel)
			{
				$same = false;
				foreach((array)$this->model->tableSchema->primaryKey as $pkName)
				{
					if($model->$pkName===$relatedModel->$pkName)
						$same = true;
				}
				if($same===true)
				{
					foreach($this->model->metaData->relations as $relation)
					{
						if($relatedModel->hasRelated($relation->name))
							$model->{$relation->name} = $relatedModel->{$relation->name};
					}
				}
				
			}
		}
		
		return $data;
	}
}