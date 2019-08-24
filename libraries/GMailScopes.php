<?php
declare(strict_types=1);
defined('BASEPATH') OR exit('No direct script access allowed');

class GMailScopes {
  const FULL             = 'https://mail.google.com/';
  const READ_ONLY        = 'https://www.googleapis.com/auth/gmail.readonly';
  const LABELS           = 'https://www.googleapis.com/auth/gmail.labels';
  const SEND             = 'https://www.googleapis.com/auth/gmail.send';
  const COMPOSE          = 'https://www.googleapis.com/auth/gmail.compose';
  const INSERT           = 'https://www.googleapis.com/auth/gmail.insert';
  const MODIFY           = 'https://www.googleapis.com/auth/gmail.modify';
  const METADATA         = 'https://www.googleapis.com/auth/gmail.metadata';
  const BASIC_SETTINGS   = 'https://www.googleapis.com/auth/gmail.settings.basic';
  const SHARING_SETTINGS = 'https://www.googleapis.com/auth/gmail.settings.sharing';
}
