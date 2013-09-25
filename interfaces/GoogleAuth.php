<?php

function openid_req_url($CONSUMER_KEY, $return_to, $scopes = false){
	if ($scopes == false){
		$scopes = array(
		  'https://www-opensocial.googleusercontent.com/api/people/'
		);
	}
	$openid_params = array(
	  'openid.ns'                => 'http://specs.openid.net/auth/2.0',
	  'openid.mode'              => 'checkid_setup',
	  'openid.claimed_id'        => 'http://specs.openid.net/auth/2.0/identifier_select',
	  'openid.identity'          => 'http://specs.openid.net/auth/2.0/identifier_select',
	  'openid.return_to'         => "http://".$CONSUMER_KEY.$return_to,
	  'openid.realm'             => "http://".$CONSUMER_KEY,
	  'openid.ns.ext1'           => 'http://openid.net/srv/ax/1.0',
	  'openid.ext1.mode'         => 'fetch_request',
	  'openid.ext1.type.email'   => 'http://axschema.org/contact/email',
	  'openid.ext1.type.first'   => 'http://axschema.org/namePerson/first',
	  'openid.ext1.type.last'    => 'http://axschema.org/namePerson/last',
	  'openid.ext1.type.country' => 'http://axschema.org/contact/country/home',
	  'openid.ext1.type.lang'    => 'http://axschema.org/pref/language',
	  'openid.ext1.required'     => 'email,first,last,country,lang',
	  'openid.ns.oauth'          => 'http://specs.openid.net/extensions/oauth/1.0',
	  'openid.oauth.consumer'    => $CONSUMER_KEY,
	  'openid.oauth.scope'       => implode(' ', $scopes),
	  'openid.ui.icon'           => 'true',
	  'openid.ns.ui'             => 'http://specs.openid.net/extensions/ui/1.0'
	);
	$uri = '';
	foreach ($openid_params as $key => $param) {
		$uri .= $key . '=' . urlencode($param) . '&';
	}
	return 'https://www.google.com/accounts/o8/ud?'.rtrim($uri, '&');
}

?>