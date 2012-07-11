<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 * @var $case string 1 2 or 3.
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
		'type' => 'raw',
		'name' => $case === '3' ? 'genre.name' : 'genres.name',
		'header' => $case === '2' ? 'allGenres' : 'Genres',
		'value' => $case === '2'
			? 'Help::tags($data->allGenres, "genres", true)'
			: 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($genre, 'name'),
	),
);

$dp = $review->search($case);
$dp->pagination->pageSize = 5;
$grid = array(
	'ajaxUpdate' => false,
	'id' => 'song-grid-' . $case,
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);

echo CHtml::tag('h2', array(), 'Case ' . $case);
$this->widget('zii.widgets.grid.CGridView', $grid);