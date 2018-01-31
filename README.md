# SimpleTwitter
Simple PHP class for search on Twitter

## Usage
$twitterclass = new SimpleTwitter(<oauth_access_token>, <oauth_access_token_secret>, <consumer_key>, <consumer_secret>);

print_r ($twitterclass->query("Keywords",""));
