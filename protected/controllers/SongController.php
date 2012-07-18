<?php

class SongController extends Controller {
	public $layout = '//layouts/column2';

	public function actionTesting() {
		$this->render('testing', array(
		));
	}

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Set up models with CGV search form input.
	 *
	 * @param CActiveRecord $model
	 */
	protected function setSearchInputs($model) {
		foreach (array('Reviewer', 'Review', 'Song', 'SongGenre', 'Genre') as $class) {
			if (get_class($model) === $class) {
				$model->unsetAttributes();
				if (isset($_GET[$class])) {
					$model->attributes = $_GET[$class];
				}
			} else {
				$prop = 'search' . $class;
				if (property_exists($model, $prop)) {
					$model->$prop = new $class('search');
					$model->$prop->unsetAttributes();
					if (isset($_GET[$class])) {
						$model->$prop->attributes = $_GET[$class];
					}
				}
			}
		}
	}

	/**
	 * Grid of all songs including genres column
	 */
	public function actionSongs() {
		$song = new Song('search');
		$this->setSearchInputs($song);
		$this->render('songGrid', array(
			'song' => $song,
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
			$this->render('reviewGrid', array(
				'review' => $review,
				'case' => isset($_GET['case']) ? $_GET['case'] : null,
			));
		} elseif (substr($_GET['ajax'], 0, -1) === 'review-grid-') {
			// For a CGridView ajax update request, render the grid partial.
			$this->renderPartial('_reviewGrid', array(
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
