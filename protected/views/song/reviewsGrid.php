<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 * @var string $case
 */

echo CHtml::tag('h1', array(), 'Manage ' . $this->action->id);

if (!$case || $case === '1') {
	$this->renderPartial('_reviewsGrid', array(
		'review' => $review,
		'song' => $song,
		'genre' => $genre,
		'case' => '1',
	));
}

if (!$case || $case === '2') {
	$this->renderPartial('_reviewsGrid', array(
		'review' => $review,
		'song' => $song,
		'genre' => $genre,
		'case' => '2',
	));
}

if (!$case || $case === '3') {
	$this->renderPartial('_reviewsGrid', array(
		'review' => $review,
		'song' => $song,
		'genre' => $genre,
		'case' => '3',
	));
}
