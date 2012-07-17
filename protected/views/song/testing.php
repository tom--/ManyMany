<p>ტესტირება</p>

<p>Փորձարկում</p>

<p>Δοκιμές</p>

<p>परीक्षण</p>

<?php
$model = new Review;
CVarDumper::dump($model->metaData, 10, true);
echo CHtml::tag('hr', array('class' => ''));
$model = new Song;
CVarDumper::dump($model->metaData, 10, true);
