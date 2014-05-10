<?php

class Af_FacebookFeedImages extends Plugin {

	private $host;

	function about() {
		return array(
			1.1,
			"Insert larger images in Facebook feeds.",
			"kuc"
		);
	}

	function api_version() {
		return 2;
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "://www.facebook.com/") !== FALSE) {
			if (strpos($article["plugin_data"], "facebookfeedimages,$owner_uid:") === FALSE) {
				if (strpos($article["content"], '_s.jpg"') !== FALSE) {
					$article["content"] = str_replace('_s.jpg"', '_n.jpg"', $article["content"]);
					$article["plugin_data"] = "facebookfeedimages,$owner_uid:" . $article["plugin_data"];
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}
		}

		return $article;
	}
}
