<?php

return [
  'order' => 'meta.date:desc page.filePath:desc',
  'itemsPerPage' => 5,

  'templateBasePath' => null,
  'templates' => [
    'content' => 'file:templates/singleItem.twig',
    'divider' => '<div style="height: 2em">&nbsp;</div>',
    'previous' => '<div style="float: left"><a href="{{ link }}">< newer</a></div>',
    'next' => '<div style="float: right;"><a href="{{ link }}">older ></a></div>'
  ],
];
