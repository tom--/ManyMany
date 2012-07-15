<?php

class SongController extends Controller {
	public $layout = '//layouts/column2';

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Set up models with CGV search form input.
	 *
	 * @param Review|SongGenre $model
	 */
	protected function setSearchInputs($model) {
		$model->unsetAttributes();
		if (isset($_GET['SongGenre'])) {
			$model->attributes = $_GET['SongGenre'];
		}

		$model->searchSong = new Song('search');
		$model->searchSong->unsetAttributes();
		if (isset($_GET['Song'])) {
			$model->searchSong->attributes = $_GET['Song'];
		}

		$model->searchGenre = new Genre('search');
		$model->searchGenre->unsetAttributes();
		if (isset($_GET['Genre'])) {
			$model->searchGenre->attributes = $_GET['Genre'];
		}
	}

	/**
	 * Grid of all songs including genres column
	 */
	public function actionSongs() {
		$songGenre = new SongGenre('search');
		$this->setSearchInputs($songGenre);
		$this->render('songsGrid', array(
			'songGenre' => $songGenre,
		));
	}

	/**
	 * Grid of all song reviews including genres column
	 */
	public function actionReviews() {
		// Filters in the grids involve three model types. Use one of each to hold
		// filter input values.
		$review = new Review('search');
		$this->setSearchInputs($review);

		if (!isset($_GET['ajax'])) {
			// Full page request. Use reviewsGrid. Unless case get param is set, it
			// will have all three grids inside.
			$this->render('reviewsGrid', array(
				'review' => $review,
				'case' => isset($_GET['case']) ? $_GET['case'] : null,
			));
		} elseif (substr($_GET['ajax'], 0, -1) === 'review-grid-') {
			// For a CGridView ajax update request, render the grid partial.
			$this->renderPartial('_reviewsGrid', array(
				'review' => $review,
				'case' => substr($_GET['ajax'], -1),
			));
		} else {
			throw new CHttpException(400);
		}
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
