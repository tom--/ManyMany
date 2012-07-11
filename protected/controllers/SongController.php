<?php

class SongController extends Controller {
	public $layout = '//layouts/column2';

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Grid of all songs including genres column
	 */
	public function actionSongs() {
		$song = new Song('search');
		$song->unsetAttributes();
		if (isset($_GET['Song'])) {
			$song->attributes = $_GET['Song'];
		}
		$this->render('grid', array(
			'song' => $song,
		));
	}

	/**
	 * Grid of all song reviews
	 */
	public function actionReviews() {
		// Filters in the grids involve three model types. Use one of each to hold
		// filter input values.
		$review = new Review('search');
		$review->unsetAttributes();
		if (isset($_GET['Review'])) {
			$review->attributes = $_GET['Review'];
		}

		$review->searchSong = new Song('search');
		$review->searchSong->unsetAttributes();
		if (isset($_GET['Song'])) {
			$review->searchSong->attributes = $_GET['Song'];
		}

		$review->searchGenre = new Genre('search');
		$review->searchGenre->unsetAttributes();
		if (isset($_GET['Genre'])) {
			$review->searchGenre->attributes = $_GET['Genre'];
		}

		if (!isset($_GET['ajax'])) {
			// Full page request. Use reviewsGrid. Unless case get param is set, it
			// will have all three grids inside.
			$view = 'reviewsGrid';
			$case = isset($_GET['case']) ? $_GET['case'] : null;
		} else if (substr($_GET['ajax'], 0, -1) === 'song-grid-') {
			// For a CGridView ajax update request, render the grid partial.
			$view = '_reviewsGrid';
			$case = substr($_GET['ajax'], -1);
		} else {
			throw new CHttpException(400);
		}
		$view = $case ? '_reviewsGrid' : 'reviewsGrid';
		$this->render($view, array(
			'review' => $review,
			'song' => $review->searchSong,
			'genre' => $review->searchGenre,
			'case' => $case,
		));
	}

	/**
	 * Returns a Song model given its primary key.
	 *
	 * @param integer $id the ID of the Song to be loaded
	 * @throws CHttpException If the Song model is not found.
	 * @return CActiveRecord
	 */
	public function loadModel($id) {
		$song = Song::model()->findByPk($id);
		if ($song === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $song;
	}

}
