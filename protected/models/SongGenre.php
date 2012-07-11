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

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->with = array(
			'song',
			'genre' => array('select' => false),
		);

		$criteria->group = 't.song_id';

		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);

		if ($this->searchGenre->name) {
			preg_match_all('/\w[\w~^_@?+><+*&%$#!-]*/', $this->searchGenre->name, $genres);
			$genres = $genres[0];
			if ($genres) {
				$genre = array_shift($genres);
				$criteria->compare('genre.name', $genre, true);
				if ($genres) {
					foreach ($genres as $genre) {
						$criteria->compare('genre.name', $genre, true, 'or');
					}
				}
			}
		}

		$criteria->together = true;

		$sort = new CSort;
		$sort->defaultOrder = array('song_id' => CSort::SORT_ASC);
		$sort->attributes = array(
			'song.name' => array(
				'asc' => 'song.name',
				'desc' => 'song.name DESC',
			),
			'song.artist' => array(
				'asc' => 'song.artist',
				'desc' => 'song.artist DESC',
			),
			'song.album' => array(
				'asc' => 'song.album',
				'desc' => 'song.album DESC',
			),
			'genres.name' => array(
				'asc' => 'genre.name',
				'desc' => 'genre.name DESC',
			),
			'*',
		);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => $sort,
		));
	}
}