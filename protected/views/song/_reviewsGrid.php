<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 * @var $case string 1 2 or 3.
 */

$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
	),
	array(
		'name' => 'song.name',
		'filter' => CHtml::activeTextField($review->searchSong, 'name'),
	),
	array(
		'name' => 'song.artist',
		'filter' => CHtml::activeTextField($review->searchSong, 'artist'),
	),
	array(
		'type' => 'raw',
		'name' => $case === '3' ? 'genre.name' : 'genres.name',
		'header' => $case === '2' ? 'allGenres' : 'Genres',
		'value' => $case === '2'
			? 'Help::tags($data->allGenres, "genres", true)'
			: 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($review->searchGenre, 'name'),
	),
	array(
		'name' => 'review',
	),
	array(
		'name' => 'reviewer.name',
	),
);

$dp = $review->search($case);
$dp->pagination->pageSize = 5;
$grid = array(
	'id' => 'song-grid-' . $case,
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);
if (isset($_GET['case'])) {
	// Disablign ajaxUpdate allows viewing query logs in the web log route
	$grid['ajaxUpdate'] = false;
}

echo CHtml::tag('h2', array(), 'Case ' . $case);
$this->widget('zii.widgets.grid.CGridView', $grid);