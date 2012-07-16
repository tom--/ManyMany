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
$songIds = array();
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