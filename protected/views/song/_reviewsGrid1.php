<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */

######################################################################
//using the search() method for the 1st usecase


$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
	),
	array(
		'name' => 'review',
	),
	array(
		'name' => 'song.name',
		'filter' => CHtml::activeTextField($song, 'name'),
	),
	array(
		'name' => 'song.artist',
		'filter' => CHtml::activeTextField($song, 'artist'),
	),
	array(
		'name' => 'song.album',
		'filter' => CHtml::activeTextField($song, 'album'),
	),
	array(
		'name' => 'genres.name',
		'type' => 'raw',
		'header' => 'Genres',
		'value' => 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($genre, 'name'),
		//'filter' => CHtml::activedropDownList($genre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
	),
);


$dp = $review->search();
$dp->pagination->pageSize = 5;
$grid = array(
	'id' => 'song-grid',
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);

//	lazy loading all SongGenre data at once
$songIds = array();
foreach ($dp->data as $review) {
	$songIds[] = $review->song_id;
}
$songIds = array_unique($songIds);
if ($songIds) {
	$dpSongGenres = SongGenre::model()->with('genre')->findAllByAttributes(
		array('song_id' => $songIds)
	);
}

//	putting the SongGenre's in the right place
foreach($dp->data as $review)
{
	$hasGenres = array();
	foreach($dpSongGenres as $songGenre)
	{
		if($songGenre->song_id===$review->song_id)
			$hasGenres[] = $songGenre;
	}
	$review->song->hasGenres = $hasGenres;
}


echo CHtml::tag('h2', array(), 'First usecase: Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);