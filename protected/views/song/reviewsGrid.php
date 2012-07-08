<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */


Yii::trace('First gridview search() usecase','<b>First gridview search() usecase</b>');
$this->renderPartial('_reviewsGrid1', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));

Yii::trace('Second gridview search2() usecase','<b>Second gridview search2() usecase</b>');
$this->renderPartial('_reviewsGrid2', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));

Yii::trace('Third gridview search3() usecase','<b>Third gridview search3() usecase</b>');
$this->renderPartial('_reviewsGrid3', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));