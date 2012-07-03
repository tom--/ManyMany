<?php
class Help extends CComponent {

	/**
	 * @var string PHP expression for the row number in a CGV
	 */
	public static $gridRowExp = '$this->grid->dataProvider->pagination->currentPage
		* $this->grid->dataProvider->pagination->pageSize + $row + 1';

	/**
	 * Recursively convert an array to html tags.
	 *
	 * @static
	 * @param array $elems Page elements to be converted to HTML tags
	 * @param null $baseClass Class attr of the top level enclosing tag
	 * @param bool $space Set true to put spaces between the tags
	 * @param string $tag The HTML tag to use
	 * @param bool $encode Set true to HTML encode the elements
	 * @param int $depth Depth counter
	 * @return string HTML tags containing the given elements
	 */
	public static function tags(
		$elems,
		$baseClass = null,
		$space = false,
		$tag = 'span',
		$encode = true,
		$depth = 0
	) {
		if ($depth === 0 && $baseClass) {
			return self::tags(
				array($baseClass => $elems), null, $space, $tag, $encode, 1
			)
				. ($space ? "\n" : '');
		} else {
			$tags = array();
			if ($elems) {
				foreach ($elems as $class => $item) {
					if ($space) {
						$tags[] = "\n" . str_repeat('  ', $depth);
					}
					$tags[] = CHtml::tag($tag,
						preg_match('/^[a-z][a-z_-]*$/', $class)
							? array('class' => $class)
							: array(),
						is_scalar($item)
							? ($encode ? CHtml::encode($item) : $item)
							: self::tags(
							$item, null, $space, $tag, $encode, $depth + 1
						)
							. ($space ? "\n" . str_repeat('  ', $depth) : '')
					);
				}
			}
			return implode($tags);
		}
	}

}