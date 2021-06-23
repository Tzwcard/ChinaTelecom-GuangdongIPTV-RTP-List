<?php
function _curl_get($url) {

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  $ret = curl_exec($ch);
  if (curl_errno($ch)) {
      return NULL;
  }
  curl_close($ch);
  
  return $ret;
}

function _get_gzbn_broadcast_list() {
  $prog_list = 'https://channel.gztv.com/channelf/site/rest/tv-channel/getChannelAndProgramList';
  
  $prog_list_data = json_decode(_curl_get($prog_list), 1);
  $channels = [];

  foreach ($prog_list_data['data'] ?? [] as $v) {
    $channels[] = [
      'name' => $v['name'],
      'uuid' => $v['uuid'],
    ];
  }
  
  return $channels;
}

function _jump_gzbn_broadcast_m3u8($channel) {
  $video_page_api = 'https://channel.gztv.com/channelf/viewapi/player/channelVideo?id=%s&commentFrontUrl=https://comment.gztv.com/commentf';
  
  $video_page_url = sprintf($video_page_api, $channel['uuid']);
  $video_page_data = _curl_get($video_page_url);

  $m3u8_uri = NULL; $secondid = NULL;
  if (preg_match('/standardUrl=\'(.*)\';/', $video_page_data, $matches)) $m3u8_uri = $matches[1];
  if (preg_match('/secondId=\'(.*)\';/', $video_page_data, $matches)) $secondid = $matches[1];

  header("Location: " . $m3u8_uri);
}

// NEW API
function _get_gzbn_broadcast_list_newapi() {
  return [
    'general' => 'zhonghe',
    'news' => 'xinwen',
    'legal' => 'fazhi',
    'sport' => 'jingsai',
    'drama' => 'yingshi',
    'uhd' => 'shenghuo',
  ];
}

function _jump_gzbn_broadcast_m3u8_newapi($channelname) {
  $progapi = 'https://www.gztv.com/gztv/api/tv/';
  $apidata = _curl_get($progapi . $channelname);
  
  $apidata = json_decode($apidata, 1);
  if ($apidata && $apidata['code'] == 200) {
    header ("Location: " . $apidata ['data']);
    return ;
  }
  
  return ;

}

$all_channels = _get_gzbn_broadcast_list_newapi();
$channel_names = [
  'general' => '综合',
  'news' => '新闻',
  'drama' => '影视',
  'sport' => '竞赛',
  'legal' => '法治',
  'uhd' => '南国都市',
];
if (isset($_GET['get_channel']) && isset($all_channels[$_GET['get_channel']])) {
  $channel = $all_channels[$_GET['get_channel']];
  _jump_gzbn_broadcast_m3u8_newapi($channel);
  return ;
}

http_response_code(404);
die();
?>
