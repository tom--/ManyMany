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
	public $searchSong;
	public $searchGenre;
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

	/*
	 * search method to Lazy Load the has many relation
	 * Advantage: It enables you to do stuff with data from the join table SongGenre
	 */
	public function search() {
		$criteria = new CDbCriteria;
		
		/**
		 * Selecting all data from song, because its a has one relation
		 * Not selecting any data from genres, because its lazy loaded anyway by Song::genreNames
		 */
		$criteria->with = array('song', 'song.genres'=>array('select'=>false));
		$criteria->compare('genres.id', $this->searchGenre->id, true);
		$criteria->compare('genres.name', $this->searchGenre->name, true);
		
		$criteria->group = 't.reviewer_id, t.song_id';
		$criteria->together = true;

		$criteria->compare('t.reviewer_id', $this->reviewer_id, true);
		$criteria->compare('t.song_id', $this->song_id, true);
		$criteria->compare('t.review', $this->review, true);
		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);
		
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
        	'sort'=>array(
        		'defaultOrder'=>array(
        			'song_id'=>CSort::SORT_ASC,
        			),
        		'attributes'=>array(
        			'song.name'=>array(
        				'asc'=>'song.name',
        				'desc'=>'song.name DESC',
        			),
        			'song.artist'=>array(
        				'asc'=>'song.artist',
        				'desc'=>'song.artist DESC',
        			),
        			'song.album'=>array(
        				'asc'=>'song.album',
        				'desc'=>'song.album DESC',
        			),
        			'genres.name'=>array(
        				'asc'=>'genres.name',
        				'desc'=>'genres.name DESC',
        			),
        			'*',
        		),
        	),
		));
	}
	
	/*
	 * Search method to enable eager loading, using group_concat
	 * You need a public property ($allGenres) in the main Model (Review) for this
	 * 
	 * If you dont want the public property in this model, but in the related 
	 * model(where it *should* be actually*, then you need to set it there. However, 
	 * in that case you will need to do a custom $criteria->join, instead of custom 
	 * $criteria->select.
	 * 
	 * Disadvantage: You dont get 'raw' data back. You can't do things using the
	 * join table either.
	 * Advantage: Most efficient eager loading
	 */
	public function search2() {
		$criteria = new CDbCriteria;
	
		/**
		* Selecting all data from song, because its a has one relation
		* Not selecting any data from genres, because its loaded using group_concat
		*/
		$criteria->with = array('song', 'song.genres'=>array('select'=>false));
		$criteria->compare('genres.id', $this->searchGenre->id, true);
		$criteria->compare('genres.name', $this->searchGenre->name, true);
		$criteria->select = array(
			'GROUP_CONCAT(genres.name ORDER BY genres.name SEPARATOR \', \') AS allGenres',
			't.review',
			//PK's arent needed in here, they are automatically added.
		);
		
		
		$criteria->group = 't.reviewer_id, t.song_id';
		$criteria->together = true;
		
		$criteria->compare('t.reviewer_id', $this->reviewer_id, true);
		$criteria->compare('t.song_id', $this->song_id, true);
		$criteria->compare('t.review', $this->review, true);
		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);
	
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort'=>array(
				'defaultOrder'=>array(
					'song_id'=>CSort::SORT_ASC,
				),
				'attributes'=>array(
	        		'song.name'=>array(
						'asc'=>'song.name',
						'desc'=>'song.name DESC',
					),
					'song.artist'=>array(
						'asc'=>'song.artist',
	        			'desc'=>'song.artist DESC',
	        		),
					'song.album'=>array(
	        			'asc'=>'song.album',
	        			'desc'=>'song.album DESC',
	        		),
	        		'genres.name'=>array(
	        			'asc'=>'genres.name',
	        			'desc'=>'genres.name DESC',
					),
	        		'*',
				),
			),
		));
	}
	
	/*
	 * Search method for eager loading. But this needs a custom CActiveFinder
	 * So the Pager works and all of the data is returned.
	 * The custom CActiveFinder is loaded in the index.php using classMap.
	 */
	public function search3() {
		$criteria = new CDbCriteria;
	
		/**
		* Note, if you Eager Load, and someone searched for a genre, only THAT genre is shown.
		* If the Song has another Genre, its not shown.
		* Note that in this usecase, you compare to genre, not genres.
		*/
		$criteria->with = array('song', 'song.hasGenres', 'song.hasGenres.genre');
		$criteria->compare('genre.id', $this->searchGenre->id, true);
		$criteria->compare('genre.name', $this->searchGenre->name, true);
	
		$criteria->together = true;
	
		$criteria->compare('t.reviewer_id', $this->reviewer_id, true);
		$criteria->compare('t.song_id', $this->song_id, true);
		$criteria->compare('t.review', $this->review, true);
		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);
	
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort'=>array(
				'defaultOrder'=>array(
					'song_id'=>CSort::SORT_ASC,
				),
				'attributes'=>array(
					'song.name'=>array(
						'asc'=>'song.name',
						'desc'=>'song.name DESC',
					),
					'song.artist'=>array(
						'asc'=>'song.artist',
						'desc'=>'song.artist DESC',
					),
					'song.album'=>array(
			    		'asc'=>'song.album',
			    		'desc'=>'song.album DESC',
			    	),
					'genre.name'=>array(
						'asc'=>'genre.name',
			        	'desc'=>'genre.name DESC',
					),
			        '*',
				),
			),
		));
	}
	
}