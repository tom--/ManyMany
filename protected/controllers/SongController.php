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
		$review = new Review('search');
		$review->unsetAttributes();
		$song = new Song('search');
		$song->unsetAttributes();
		$genre = new Genre('search');
		$genre->unsetAttributes();
		
		if (isset($_GET['Review']))
			$review->attributes = $_GET['Review'];
		if (isset($_GET['Song']))
			$song->attributes = $_GET['Song'];
		if (isset($_GET['Genre']))
			$genre->attributes = $_GET['Genre'];
		
		$review->searchSong = $song;
		$review->searchGenre = $genre;
		
		if(!isset($_GET['ajax']))
			$this->render('reviewsGrid', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
			));
		else
			$this->renderPartial('reviewsGrid', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
			));
	}

	public function actionAddReviews() {
		$crit = new CDbCriteria;
		$crit->order = 'id';
		$crit->limit = 1;
		$crit->offset = 0;

		$maxReviewer = Reviewer::model()->count() - 1;

		/** @var $song Song */
		while ($song = Song::model()->find($crit)) {
			$crit->offset += 1;
			if (mt_rand(0, 1) === 0) {
				continue;
			}
			$nreviews = mt_rand(1, 3);
			$reviewers = array();
			for ($i = 0; $i < $nreviews; $i += 1) {
				$review = new Review;
				$review->song_id = $song->id;
				$review->review = Lipsum::getLipsum(mt_rand(1, 3));
				do {
					$review->reviewer_id = Reviewer::model()->find(array(
						'order' => 'id',
						'limit' => 1,
						'offset' => mt_rand(0, $maxReviewer),
					))->id;
				} while (in_array($review->reviewer_id, $reviewers));
				$reviewers[] = $review->reviewer_id;
				$review->save();
			}
		}

		$this->redirect(array('reviews'));
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
