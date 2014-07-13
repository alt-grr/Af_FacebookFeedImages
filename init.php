<?php

class Af_FacebookFeedImages extends Plugin {

	private $host;

	function about() {
		return array(
			1.2,
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

		if (strpos($article["link"], "://www.facebook.com/") === FALSE) {
			return $article;
		}

		if (strpos($article["plugin_data"], "facebookfeedimages,$owner_uid:") !== FALSE) {
			if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}

			return $article;
		}


		$doc = new DOMDocument();
		@$doc->loadHTML('<?xml encoding="UTF-8"?>' . $article["content"]);
		if (!$doc) {
			return $article;
		}

		$found_images = FALSE;
		$xpath = new DOMXPath($doc);
		$images = $xpath->query('//img');
		if (empty($images)) {
			return $article;
		}

		foreach ($images as $image) {
			$image_src = $image->getAttribute("src");
			if ((strpos($image_src, ".fbcdn.net/") !== FALSE || strpos($image_src, ".akamaihd.net/") !== FALSE)
				&& strpos($image_src, "/s130x130/") !== FALSE) {

				$image_src = str_replace('/s130x130/', '/', $image_src);
				$image->setAttribute("src", $image_src);
				$found_images = TRUE;
			}
		}

		if ($found_images) {
			$article["content"] = $doc->saveHTML();
			$article["plugin_data"] = "facebookfeedimages,$owner_uid:" . $article["plugin_data"];
		}

		return $article;
	}
}
