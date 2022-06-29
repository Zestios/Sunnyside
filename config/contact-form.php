<?php

//$config = ['prependSubject' => '['.time().'] '];
$request = Craft::$app->request;

if (
  !$request->getIsConsoleRequest() &&
  ($toEmail = $request->getValidatedBodyParam('toEmail')) !== null
) {

  $config['toEmail'] = $toEmail;
  $config['allowAttachments'] = true;
}

return $config;
