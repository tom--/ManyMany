<?php

/**
 * This is the model class for table "genre".
 *
 * The followings are the available columns in table 'genre':
 * @property string $id
 * @property string $parent_id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Genre $parent
 * @property Genre[] $genres
 * @property Song[] $songs
 */
class Genre extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'genre';
	}

	public function rules() {
		return array();
	}

	public function relations() {
		return array(
			'songs' => array(self::MANY_MANY, 'Song', 'song_genre(genre_id, song_id)'),
			'parent' => array(self::BELONGS_TO, 'Genre', 'parent_id'),
			'genres' => array(self::HAS_MANY, 'Genre', 'parent_id'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'Genre ID',
			'parent_id' => 'Parent ID',
			'name' => 'Genre',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('parent_id', $this->parent_id, true);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}