SK-Twitter-Api v1.0
------------

A simple twitter search api client

How to use
-------------


- Create `config.ini` file

```
;
;  Twitter API Credentials
;
;  You can create your own by visiting undersigned url:
;
;  url: https://apps.twitter.com/
;
;  NOTE: PLEASE DO NOT SHARE IT WITH ANYONE
;
CONSUMER_KEY = <key>
CONSUMER_SECRET = <secret>

```

Usage
------

``` php
$sk_tweet = new SK_Tweets();

$search_filter = array(
	"q" => "#custserv",
	"retweet_count" => "1",
	"count" => 1

	);
$tweets = $sk_tweet->search( $search_filter);
echo $tweets->statuses[2]->retweet_count;
echo $tweets->statuses[0]->text;

```