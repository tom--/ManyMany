<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 * @var string $case
 */

echo CHtml::tag('h1', array(), 'Manage ' . $this->action->id);

foreach (array('1', '2', '3') as $i) {
	if (!$case || $case === $i) {
		$this->renderPartial('_reviewGrid', array(
			'review' => $review,
			'case' => $i,
		));
	}
}
