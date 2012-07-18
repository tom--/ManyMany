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
	/**
	 * @var Genre Used for CGV filter form inputs.
	 */
	public $searchGenre;

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

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->with = array('genres');

		$criteria->group = 't.id';

		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.artist', $this->artist, true);
		$criteria->compare('t.album', $this->album, true);

		if ($this->searchGenre->name) {
			preg_match_all('/\w[\w~^_@?+><+*&%$#!-]*/', $this->searchGenre->name, $genres);
			$genres = $genres[0];
			if ($genres) {
				$genre = array_shift($genres);
				$criteria->compare('genres.name', $genre, true);
				if ($genres) {
					foreach ($genres as $genre) {
						$criteria->compare('genres.name', $genre, true, 'or');
					}
				}
			}
		}

		$criteria->together = true;

		$sort = new CSort;
		$sort->defaultOrder = array('song_id' => CSort::SORT_ASC);
		$sort->attributes = array(
			'name' => array(
				'asc' => 't.name',
				'desc' => 't.name DESC',
			),
			'artist' => array(
				'asc' => 't.artist',
				'desc' => 't.artist DESC',
			),
			'album' => array(
				'asc' => 't.album',
				'desc' => 't.album DESC',
			),
			'genres.name' => array(
				'asc' => 'genres.name',
				'desc' => 'genres.name DESC',
			),
			'*',
		);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => $sort,
		));
	}
}