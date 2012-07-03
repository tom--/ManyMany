<?php

class SongController extends Controller {
	public $layout = '//layouts/column2';

	public function filters() {
		return array(
		);
	}

	public function accessRules() {
	}

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	protected function searchSong($scenario = '') {
		$song = new Song($scenario);
		$song->unsetAttributes();
		if (isset($_GET['Song'])) {
			$song->attributes = $_GET['Song'];
		}
		return $song;
	}

	/**
	 * Grid of all songs including genres column
	 */
	public function actionSongs() {
		$this->render('grid', array(
			'song' => $this->searchSong('SongGenre'),
		));
	}

	/**
	 * Grid of all song reviews
	 */
	public function actionReviews() {
		$this->render('grid', array(
			'song' => $this->searchSong('SongGenre'),
		));
	}

	/*
	public function actionAddReviews() {
		$nSongs = Song::model()->count() - 1;
		$reviewers = Reviewer::model()->findAll();
		foreach ($reviewers as $reviewer) {
			$nreviews = mt_rand(0, 4);
			if ($nreviews) {
				for ($i = 0; $i < $nreviews; $i += 1) {
					$review = new Review;
					$review->reviewer_id = $reviewer->id;
					$review->review = Lipsum::getLipsum(mt_rand(1,3));
					$row = mt_rand(0, $nSongs);
					$review->song_id = Yii::app()->db->createCommand(
						"select id from song limit $row, 1"
					)->queryScalar();
					$review->save();
				}
			}
		}
		$this->redirect(array('admin'));
	}
	*/

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
