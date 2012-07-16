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
		$this->afterFetch($data);
		return $data;		
	}
	
	/**
	 * Loads additional related data in bulk, instead of each model lazy loading its related data
	 */
	protected function afterFetch($data)
	{
		$pks = array(); 
		foreach ($data as $dataItem) {
			$pks[] = $dataItem->$this->model->tableSchema->primaryKey;
		}
		$pks = array_unique($pks);
		if ($pks) {
			// do stuff
			
			
		}
	}
}