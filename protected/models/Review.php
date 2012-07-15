<?php

/**
 * Table attributes:
 * @property string $reviewer_id
 * @property string $song_id
 * @property string $review
 *
 * Relation attributes:
 * @property Reviewer $reviewer
 * @property Song $song
 */
class Review extends CActiveRecord {
	/**
	 * @var Song Used for CGV filter form inputs.
	 */
	public $searchSong;
	/**
	 * @var Genre Used for CGV filter form inputs.
	 */
	public $searchGenre;
	/**
	 * @var string The value of a group_concat over this Revew's Song's genre names.
	 * Set by the data provider configured by $this->search('2'), otherwise unused.
	 */
	public $allGenres;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'review';
	}

	public function rules() {
		return array(
			array('review', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array(
			'song' => array(self::BELONGS_TO, 'Song', 'song_id'),
			'reviewer' => array(self::BELONGS_TO, 'Reviewer', 'reviewer_id'),
		);
	}

	public function attributeLabels() {
		return array(
			'reviewer_id' => 'Reviewer ID',
			'song_id' => 'Song ID',
			'review' => 'Review',
		);
	}

	/**
	 * Generate a data provider given search criteria in $this.
	 *
	 * There are 3 use cases, 1, 2, and 3, demonstrating different handling of
	 * has_many and many_many relations in the CGV.
	 *
	 * 1. Lazy Load the has many relation.
	 *    Advantage: It enables you to do stuff with data from the join table SongGenre
	 *
	 * 2. Eager loading, using group_concat
	 *    You need a public property ($allGenres) in the main Model (Review) for the
	 *    group_concat value.
	 *
	 *    If you dont want the public property in this model, but in the related
	 *    model (where it *should* be actually*, then you need to set it there. However,
	 *    in that case you will need to do a custom $criteria->join, instead of custom
	 *    $criteria->select.
	 *
	 *    Disadvantage: You dont get 'raw' data back. You can't do things using the
	 *       join table either.
	 *    Advantage: Most efficient eager loading
	 *
	 * 3. Eager loading with a custom CActiveFinder.
	 *    So the Pager works and all of the data is returned.
	 *    The custom CActiveFinder is loaded in the index.php using classMap.
	 *
	 * @param string $case The use case, 1, 2, or 3.
	 * @return CActiveDataProvider
	 */
	public function search($case = '1') {
		$criteria = new CDbCriteria;

		if ($case === '1' || $case === '2') {
			/*
			 * Select all data from song because it is a has_one relation.
			 * Do not select any data from genres because:
			 *  - case 1: it is lazy loaded by Song::genreNames.
			 *  - case 2: it is loaded using group_concat.
			 */
			$criteria->with = array(
				'reviewer',
				'song',
				'song.genres' => array('select' => false),
			);

			if ($case === '2') {
				// The value allGenres ends up in the DP's models' allGenres properties.
				// It is only used for getting data, not for filter input.
				$criteria->select = array(
					'GROUP_CONCAT(genres.name ORDER BY genres.name SEPARATOR " ") AS allGenres',
					't.review',
					// PK's aren't needed in here, they are automatically added.
				);
			}

			$criteria->group = 't.reviewer_id, t.song_id';

			$criteria->compare('genres.id', $this->searchGenre->id, true);
			$criteria->compare('genres.name', $this->searchGenre->name, true);
		} elseif ($case === '3') {
			// If eager Loading and someone searched for a genre, only THAT genre is shown.
			// If the Song has other Genres, they are not shown: you compare to genre, not genres.
			$criteria->with = array(
				'reviewer',
				'song',
				'song.hasGenres',
				'song.hasGenres.genre',
			);
			$criteria->compare('genre.id', $this->searchGenre->id, true);
			$criteria->compare('genre.name', $this->searchGenre->name, true);
		}

		$criteria->together = true;

		$criteria->compare('t.reviewer_id', $this->reviewer_id, true);
		$criteria->compare('t.song_id', $this->song_id, true);
		$criteria->compare('t.review', $this->review, true);
		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => array(
					'song_id' => CSort::SORT_ASC,
				),
				'attributes' => array(
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
						'asc' => 'genres.name',
						'desc' => 'genres.name DESC',
					),
					'*',
				),
			),
		));
	}
}
