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
$dp->pagination->pageSize = 25;
$grid = array(
	'id' => 'review-grid-' . $case,
	'dataProvider' => $dp,
	'filter' => $review,
	'columns' => $columns,
);
if (isset($_GET['case'])) {
	// Disablign ajaxUpdate allows viewing query logs in the web log route
	$grid['ajaxUpdate'] = false;
}

if ($case === '1') {
	// Load all SongGenre data before the data provider does.
	// Get the song_ids of all the songs in this page of the grid.
	$songIds = array();
	foreach ($dp->data as $review) {
		$songIds[] = $review->song_id;
	}
	$songIds = array_unique($songIds);
	if ($songIds) {
		// Load all the SongGenres related to Songs in this page of the grid
		$dpSongGenres = SongGenre::model()->with('genre')->findAllByAttributes(
			array('song_id' => $songIds)
		);
		if ($dpSongGenres) {
			// Put the SongGenre's into the data provider
			foreach ($dp->data as $review) {
				$hasGenres = array();
				foreach ($dpSongGenres as $songGenre) {
					if ($songGenre->song_id === $review->song_id) {
						$hasGenres[] = $songGenre;
					}
				}
				$review->song->hasGenres = $hasGenres;
			}
		}
	}
}

echo CHtml::tag('h2', array(), 'Case ' . $case);
$this->widget('zii.widgets.grid.CGridView', $grid);