<?php

$dp = new CActiveDataProvider('Song');
$dp->pagination = new CPagination;
$dp->pagination->currentPage = 1;
CVarDumper::dump($dp->data, 3, true);
CVarDumper::dump($dp, 3, true);
$dp->pagination->currentPage = 2;
CVarDumper::dump($dp->getData(true), 3, true);
CVarDumper::dump($dp, 3, true);
