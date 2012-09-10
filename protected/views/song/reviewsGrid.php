<?php
/**
 * @var Review $review
 * @var Song $song
 * @var Genre $genre
 * @var Controller|CController $this
 */

Yii::app()->clientScript->registerScriptFile(
    'js/contentEditable.js'
)->registerScript(
    'gridedit',
    <<<JS
        $('body').editable();
        $('#content').on('change', 'td.review', function () {
            var td = $(this),
                ids = td.parent().children().first().text().split(',');
            $.ajax({
                data: {
                    ajax: true,
                    modelClass: 'Review',
                    songId: ids[0],
                    reviewerId: ids[1],
                    reviewText: td.html()
                }
            });
        });
JS
);

Yii::trace('First gridview search() usecase', '<b>First gridview search() usecase</b>');
$this->renderPartial(
    '_reviewsGrid1',
    array(
        'review' => $review,
        'song' => $song,
        'genre' => $genre,
    )
);

Yii::trace('Second gridview search2() usecase', '<b>Second gridview search2() usecase</b>');
$this->renderPartial(
    '_reviewsGrid2',
    array(
        'review' => $review,
        'song' => $song,
        'genre' => $genre,
    )
);

Yii::trace('Third gridview search3() usecase', '<b>Third gridview search3() usecase</b>');
$this->renderPartial(
    '_reviewsGrid3',
    array(
        'review' => $review,
        'song' => $song,
        'genre' => $genre,
    )
);