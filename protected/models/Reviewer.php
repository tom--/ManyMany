<?php

/**
 * This is the model class for table "reviewer".
 *
 * The followings are the available columns in table 'reviewer':
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Song[] $songs
 * @property Review[] $reviews
 */
class Reviewer extends CActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'reviewer';
	}

	public function rules() {
		return array();
	}

	public function relations() {
		return array(
			'reviews' => array(self::HAS_MANY, 'Review', 'reviewer_id'),
			'songs' => array(self::HAS_MANY, 'Song', 'song_id', 'through' => 'reviews'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'reviewer ID',
			'name' => 'Reviewer name',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}