<?php
/**
 * @var SongGenre $songGenre
 * @var Controller|CController $this
 */

$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
	),
	array(
		'name' => 'song.name',
		'filter' => CHtml::activeTextField($songGenre->searchSong, 'name'),
	),
	array(
		'name' => 'song.artist',
		'filter' => CHtml::activeTextField($songGenre->searchSong, 'artist'),
	),
	array(
		'name' => 'song.album',
		'filter' => CHtml::activeTextField($songGenre->searchSong, 'album'),
	),
	array(
		'type' => 'raw',
		'name' => 'genres.name',
		'header' => 'Genres',
		'value' => 'Help::tags($data->song->genreNames, "genres", true)',
		'filter' => CHtml::activeTextField($songGenre->searchGenre, 'name'),
	),
);

$dp = $songGenre->search();
$dp->pagination->pageSize = 25;
$grid = array(
	'ajaxUpdate' => false,
	'id' => 'song-grid',
	'dataProvider' => $dp,
	'filter' => $songGenre,
	'columns' => $columns,
);

// The next stuff is just to show, via the query log, that the $dp could get all
// the related data it needs in one go. But it doesn't it instead lazy loads the
// SongGenres and Genres for each song one at a time.
// I don't mind lazy loading. I do mind that much DB traffic.
$songIds = array();
foreach ($dp->data as $songGenre) {
	$songIds[] = $songGenre->song_id;
}
$songIds = array_unique($songIds);
if ($songIds) {
	$dpSongGenres = SongGenre::model()->with('genre')->findAllByAttributes(
		array('song_id' => $songIds)
	);
}
// End of "The next stuff"

echo CHtml::tag('h1', array(), 'Songs');
$this->widget('zii.widgets.grid.CGridView', $grid);