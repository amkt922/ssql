<?php
/*
  Copyright 2013, amkt922 <amkt922@gmail.com>

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */

namespace SSql\Exception;

use SSql\Exception\SSqlException;

/**
 * @author amkt922
 */
class ForCommentConditionEmptyException extends SSqlException {

	/**
	 * constructor
	 * @param type $message
	 */
	public function __construct($message = '') {
		if (empty($message)) {
			$message = 'The condition in FOR comment is empty.';
			$message .= 'IF comment should be like /*FOR userList*/';
		}
		parent::__construct($message);
	}
}

