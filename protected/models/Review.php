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

	public function search() {
		$criteria = new CDbCriteria;
		
		//Selecting all data from song, because its a has one relation
		//Not selecting any data from genres, because its lazy loaded anyway by Song::genreNames
		$criteria->with = array('song', 'song.genres'=>array('select'=>false));
		$criteria->group = 't.reviewer_id, t.song_id';
		$criteria->together = true;

		$criteria->compare('t.reviewer_id', $this->reviewer_id, true);
		$criteria->compare('t.song_id', $this->song_id, true);
		$criteria->compare('t.review', $this->review, true);
		
		$criteria->compare('song.name', $this->searchSong->name, true);
		$criteria->compare('song.artist', $this->searchSong->artist, true);
		$criteria->compare('song.album', $this->searchSong->album, true);
		$criteria->compare('genres.id', $this->searchGenre->id, true);
		$criteria->compare('genres.name', $this->searchGenre->name, true);

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
}