<?php
/**
 * @var $song Song
 */

$song->criteria = new CDbCriteria;

/*
 * Now take care here. This is confusing.
 *
 * The only model we have in the local context is $song. It is used for filter inputs.
 * But the method $song->search('song-search') returns a CADP for a SongGenre model.
 * So the names in CDataColumns need to be attributes of SongGenre while the
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
	$columns[] = array(
		'name' => 'song.reviews.review',
		'filter' => CHtml::activeTextField($song, 'review'),
	);
	$song->criteria->group = 'reviews.song_id';
	$song->criteria->with = array('song', 'song.reviews', 'genre');
	$song->criteria->together = true;
} else {
	$song->criteria->group = 'song.id';
	$song->criteria->with = array('song', 'genre');
}
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
