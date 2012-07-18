<?php

/**
 * This is the model class for table "review_comment".
 *
 * The followings are the available columns in table 'review_comment':
 * @property string $id
 * @property string $comment
 * @property string $review_reviewer_id
 * @property string $review_song_id
 *
 * The followings are the available model relations:
 * @property Review $reviewReviewer
 * @property Review $reviewSong
 */
class ReviewComment extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'review_comment';
	}

	public function rules() {
		return array(
			array('id, comment, review_reviewer_id, review_song_id', 'safe',
				'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'review' => array(self::BELONGS_TO, 'Review', array(
				'review_reviewer_id' => 'reviewer_id',
				'review_song_id' => 'song_id',
			)),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ReviewComment ID',
			'comment' => 'Comment',
			'review_reviewer_id' => 'Reviewer ID',
			'review_song_id' => 'Song ID',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('comment', $this->comment, true);
		$criteria->compare('review_reviewer_id', $this->review_reviewer_id, true);
		$criteria->compare('review_song_id', $this->review_song_id, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}