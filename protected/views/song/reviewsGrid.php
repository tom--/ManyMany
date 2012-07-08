<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */

$this->renderPartial('_reviewsGrid1', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));

$this->renderPartial('_reviewsGrid2', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));

$this->renderPartial('_reviewsGrid3', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
));