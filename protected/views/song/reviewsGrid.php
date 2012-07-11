<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 * @var string $case
 */

echo CHtml::tag('h1', array(), 'Manage ' . $this->action->id);

foreach (array('1', '2', '3') as $case) {
	if (!$case || $case === $case) {
		$this->renderPartial('_reviewsGrid', array(
			'review' => $review,
			'case' => $case,
		));
	}
}
