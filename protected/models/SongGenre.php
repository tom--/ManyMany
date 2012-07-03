<?php

/**
 * Table attributes:
 * @property string $song_id
 * @property string $genre_id
 * @property integer $is_primary
 *
 * Relation attributes:
 * @property Genre $genre
 * @property Song $song
 */
class SongGenre extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'song_genre';
	}

	public function rules() {
		return array();
	}

	public function relations() {
		return array(
			'genre' => array(self::BELONGS_TO, 'Genre', 'genre_id'),
			'song' => array(self::BELONGS_TO, 'Song', 'song_id'),
			'reviews' => array(self::HAS_MANY, 'Review', 'song_id', 'through' => 'song'),
			'reviewers' => array(self::HAS_MANY, 'Reviewer', 'reviewer_id', 'through' => 'review'),
		);
	}

	public function attributeLabels() {
		return array(
			'song_id' => 'Song ID',
			'genre_id' => 'Genre ID',
			'is_primary' => 'Primary',
		);
	}

	public function search() {
		$criteria = new CDbCriteria();

		$criteria->compare('song_id', $this->song_id, true);
		$criteria->compare('genre_id', $this->genre_id, true);
		$criteria->compare('is_primary', $this->is_primary);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}