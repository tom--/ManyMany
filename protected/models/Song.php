<?php

/**
 * Table attributes:
 * @property string $id
 * @property string $name Song name.
 * @property string $artist
 * @property string $album
 *
 * Relation attributes:
 * @property Genre[] $genres
 * @property SongGenre[] $hasGenres Model for the genre join table.
 * @property Review[] $reviews
 * @property Reviewer[] $reviewers
 *
 * Virtual attributes:
 * @property array $genreNames Two arrays with keys 'pri' and 'sec', each a list of genre names
 */
class Song extends CActiveRecord {
	private $_genreNames;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'song';
	}

	public function rules() {
		return array(
			array('id, name, artist, album', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array(
			'hasGenres' => array(self::HAS_MANY, 'SongGenre', 'song_id'),
			'genres' => array(self::HAS_MANY, 'Genre', 'genre_id', 'through' => 'hasGenres'),
			'reviews' => array(self::HAS_MANY, 'Review', 'song_id'),
			'reviewers' => array(self::HAS_MANY, 'Reviewer', 'reviewer_id', 'through' => 'reviews'),
		);
	}

	public function getGenreNames() {
		if ($this->_genreNames === null) {
			$this->_genreNames = array('pri' => array(), 'sec' => array());
			/** @noinspection PhpUndefinedFieldInspection */
			$genres = $this->with('hasGenres', 'hasGenres.genre')->hasGenres;
			if ($genres) {
				foreach ($genres as $genre) {
					$this->_genreNames[$genre->is_primary ? 'pri' : 'sec'][] = $genre->genre->name;
				}
			}
		}
		return $this->_genreNames;
	}

	public function attributeLabels() {
		return array(
			'id' => 'Song ID',
			'name' => 'Title',
			'artist' => 'Artist',
			'album' => 'Album',
		);
	}
}