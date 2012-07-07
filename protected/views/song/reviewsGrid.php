<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 */

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
		'type' => 'raw',
		'header' => 'Genres',
		'value' => 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($genre, 'name'),
// 		'filter' => CHtml::activedropDownList($genre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
	),

);


$dp = $review->search();
$dp->pagination->pageSize = 50;
$grid = array(
	'id' => 'song-grid',
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);

echo CHtml::tag('h1', array(), 'Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);
