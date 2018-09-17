<?php

/*
 * (c) YOUR NAME <your@email.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

// If you don't to add a custom vendor folder, then use the simple class
// namespace HelloComposer;
namespace SmartPage\Dappurware;

class SayHello
{
		public $msg = null;
		public function __construct($text = "First init of SP."){
			$this->msg[] = $text;
			return $this->msg;
		}
    public function say($toSay = "Nothing given")
    {
			$this->msg[] = $toSay;
      return $this->msg;
    }

		public function read(){
			return $this->msg;
		}
}
