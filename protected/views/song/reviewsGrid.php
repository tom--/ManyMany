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

echo CHtml::tag('h2', array(), 'First usecase: Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);


######################################################################
//using the search2() method for the 2nd usecase
$columns2 = array(
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
	'columns' => $columns2,
);

echo CHtml::tag('h2', array(), 'Second usecase: Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);


######################################################################
//using the search3() method for the 3rd usecase
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