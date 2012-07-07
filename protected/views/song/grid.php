<?php
/**
 * @var Song $song
 * @var Controller|CController $this
 */

$song->criteria = new CDbCriteria;

/*
 * Now take care here. This is confusing.
 *
 * The only model we have in the local context is $song. It is used for filter inputs,
 * to house a CDbCriteria object for use in its search() method.
 *
 * But the method $song->search() returns a CADP for a SongGenre model, not a Song
 * model, if the scenario is set to 'SongGenre'. Strange but dig in for the reasons.
 *
 * So the names in CDataColumns need to be attributes of the data provider's SongGenre
 * model while the names of the filter inputs are properties/attributes of $song.
 */
$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
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
		'filter' => CHtml::activeTextField($song, 'genre'),
	),
);

if ($this->action->id === 'reviews') {

	// This is the bit that doesn't work.
	// To produce a table of song reviews... How??
	$columns[] = array(
		'name' => 'song.reviews.review',
		'filter' => CHtml::activeTextField($song, 'review'),
	);
	$song->criteria->group = 'reviews.song_id, reviews.reviewer_id';
	$song->criteria->with = array('song', 'song.reviews', 'genre');
	$song->criteria->together = true;
} else {

	// For a table of songs, no problems.
	$song->criteria->group = 'song.id';
	$song->criteria->with = array('song', 'genre');
	$song->criteria->together = true;
}

// Run $song's search to get the CActiveDataProvider.
$dp = $song->search();
$dp->pagination->pageSize = 50;
$grid = array(
	'id' => 'song-grid',
	'dataProvider' => $dp,
	'filter' => $song,
	'columns' => $columns,
);

echo CHtml::tag('h1', array(), 'Manage ' . $this->action->id);
$this->widget('zii.widgets.grid.CGridView', $grid);
