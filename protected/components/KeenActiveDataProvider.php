<?php
class KeenActiveDataProvider extends CActiveDataProvider
{
	/**
	 * You can specify in your DataProvider config which relations you want to load in a keen way.
	 * Example:
	 * $dataProvider=new KeenActiveDataProvider('Post', array(
	 *	 'criteria'=>array(
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
	
	/**
	 * Fetches the data from the persistent data storage.
	 * Additionally, calls KeenActiveDataProvider::afterFetch method
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		$data = parent::fetchData();
		$data = $this->afterFetch($data);
		return $data;		
	}
	
	/**
	 * Loads additional related data in bulk, instead of each model lazy loading its related data
	 */
	protected function afterFetch($data)
	{
		if(!empty($this->withKeenLoading))
		{
			$pks = array();
			foreach ($data as $dataItem) {
				$pks[] = $dataItem->{$this->model->tableSchema->primaryKey};
			}
			$pks = array_unique($pks);
			$relations = $this->model->relations();
			if ($pks) {
				foreach($this->withKeenLoading as $relationName)
				{
					//if using hierarchical relation names, load the deep models using with()
					if(strpos($relationName,'.')===false) {
						$with = '';
					}
					else {
						$with = explode('.',$relationName);
						$relationName = array_shift($with);
					}
					
					// Load all the related data
					$relatedModels = CActiveRecord::model($relations[$relationName][1])->findAllByAttributes(
						array($relations[$relationName][2] => $pks),
						array('with'=>$with)
					);
					// put the related data in the dataprovider
					if ($relatedModels) {
						$newRelatedData = array();
						foreach ($relatedModels as $i => $relatedModel) {
							$newRelatedData[$relatedModel->{$relations[$relationName][2]}][] = $relatedModel;
						}
						foreach ($data as $dataItem) {
							if (isset($newRelatedData[$dataItem->{$this->model->tableSchema->primaryKey}])) {
								$dataItem->$relationName = $newRelatedData[$dataItem->{$this->model->tableSchema->primaryKey}];
							}
						}
					}
				}
					
					
			}
		}

		return $data;
	}
}