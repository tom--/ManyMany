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
		$this->render('songsGrid', array(
			'song' => $song,
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
		elseif($_GET['ajax']==='song-grid')
			$this->renderPartial('_reviewsGrid1', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
			));
		elseif($_GET['ajax']==='song-grid-2')
			$this->renderPartial('_reviewsGrid2', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
			));
		elseif($_GET['ajax']==='song-grid-3')
			$this->renderPartial('_reviewsGrid3', array(
				'review' => $review,
				'song' => $song,
				'genre' => $genre,
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
