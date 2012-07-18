<?php
/**
 * @var Song $song
 * @var Controller $this
 */

$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
	),
	array(
		'name' => 'name',
		'filter' => CHtml::activeTextField($song, 'name'),
	),
	array(
		'name' => 'artist',
		'filter' => CHtml::activeTextField($song, 'artist'),
	),
	array(
		'name' => 'album',
		'filter' => CHtml::activeTextField($song, 'album'),
	),
	array(
		'type' => 'raw',
		'name' => 'genres.name',
		'header' => 'Genres',
		'value' => 'Help::tags($data->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($song->searchGenre, 'name'),
	),
);

$dp = $song->search();
$dp->pagination->pageSize = 25;
$grid = array(
	'ajaxUpdate' => false,
	'id' => 'song-grid',
	'dataProvider' => $dp,
	'filter' => $song,
	'columns' => $columns,
);

// Load all SongGenre data before the data provider does.
// Get the song_ids of all the songs in this page of the grid.
//
// Why this chunk of code doesn't belong in the model's search method:
// $song->search(); does not get any data, it returns a data provider object, which
// is a configuration for getting data. The data isn't gotten until we use the
// data provider for providing data.
//
// Normally, it is a controller's job to use a model class to fetch data from a DB.
// The model provides the tools but it doesn't choose which rows to fetch and fetch
// them.
//
// What's happening in the following is: use the model classes to fetch a bunch of
// rows into models in the local variable $dpSongGenres. Then put those models into a
// differently structured local variable $dp, i.e. the CActiveDataProvider that the
// CGridView is going to use. That's classic controller business.
//
// But, if we use the Yii convention of configuring CGVs and CADPs in views, it
// can't go in the controller and has to fit here.
//
// So if this code should not be here, it probably belongs to the CADP. But how to
// put it there?


$songIds = array();
// The following line actually reads $_GET as follows:
//	reading $dp->data calls $this->getData():
//	  http://www.yiiframework.com/doc/api/1.1/CDataProvider#getData-detail
//	which call's $this->fetchData()
//	  http://www.yiiframework.com/doc/api/1.1/CActiveDataProvider#fetchData-detail
//	which calls $pagination->applyLimit($criteria):
//	  http://www.yiiframework.com/doc/api/1.1/CPagination#applyLimit-detail
//	which works its way back to $this->getCurrentPage()
//	  http://www.yiiframework.com/doc/api/1.1/CPagination#getCurrentPage-detail
//	which reads $_GET (because we haven't specified some other page control params.)
foreach ($dp->data as $song) {
	$songIds[] = $song->id;
}
$songIds = array_unique($songIds);
if ($songIds) {
	// Load all the SongGenres related to Songs in this page of the grid
	/** @var $dpSongGenres SongGenre[] */
	$dpSongGenres = SongGenre::model()->with('genre')->findAllByAttributes(
		array('song_id' => $songIds)
	);
	// Put the SongGenre's into the data provider
	if ($dpSongGenres) {
		$theSongGenres = array();
		foreach ($dpSongGenres as $i => $songGenre) {
			$theSongGenres[$songGenre->song_id][] = $songGenre;
		}
		foreach ($dp->data as $song) {
			if (isset($theSongGenres[$song->id])) {
				$song->hasGenres = $theSongGenres[$song->id];
			}
		}
	}
}


echo CHtml::tag('h1', array(), 'Songs');
$this->widget('zii.widgets.grid.CGridView', $grid);