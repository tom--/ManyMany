<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */

######################################################################
//using the search2() method for the 2nd usecase


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
		'header' => 'allGenres',
		'value' => 'Help::tags($data->allGenres, "genres", true)',
		'filter' => CHtml::activeTextField($genre, 'name'),
	),
);


$dp = $review->search2();
$dp->pagination->pageSize = 5;
$grid = array(
	'id' => 'song-grid-2',
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);

echo CHtml::tag('h2', array(), 'Second usecase: Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);