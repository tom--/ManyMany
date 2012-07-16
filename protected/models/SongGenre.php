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
 * @property Review[] $reviews
 * @property Reviewer[] $reviewers
 */
class SongGenre extends CActiveRecord {
	/**
	 * @var Song Used for CGV filter form inputs.
	 */
	public $searchSong;
	/**
	 * @var Genre Used for CGV filter form inputs.
	 */
	public $searchGenre;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'song_genre';
	}

	public function rules() {
		return array(
			array('song_id, genre_id, is_primary', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array(
			'genre' => array(self::BELONGS_TO, 'Genre', 'genre_id'),
			'song' => array(self::BELONGS_TO, 'Song', 'song_id'),
			'reviews' => array(self::HAS_MANY, 'Review', 'song_id',
				'through' => 'song'),
			'reviewers' => array(self::HAS_MANY, 'Reviewer', 'reviewer_id',
				'through' => 'review'),
		);
	}

	public function attributeLabels() {
		return array(
			'song_id' => 'Song ID',
			'genre_id' => 'Genre ID',
			'is_primary' => 'Primary',
		);
	}
}