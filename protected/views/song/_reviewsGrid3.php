<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */


######################################################################
//using the search3() method for the 3rd usecase


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
		'name' => 'genre.name',
		'type' => 'raw',
		'header' => 'Genres',
		'value' => 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($genre, 'name'),
		//'filter' => CHtml::activedropDownList($genre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
	),
);

$dp = $review->search3();
$dp->pagination->pageSize = 5;
$grid = array(
	'id' => 'song-grid-3',
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);
echo CHtml::tag('h2', array(), 'Third usecase: Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);