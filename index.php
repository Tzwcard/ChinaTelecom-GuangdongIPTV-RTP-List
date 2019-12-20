<?php
// header.php
  header("Content-type: text/html; charset=utf-8");
  
  $protocol = 'rtp';
  
  // produce udpxy server full host name
  $port = null;
  if (isset($_GET['port'])) {
    $port = intval($_GET['port']);
    if ($port === 0) unset($port);
  }
  $host = null;
  if (isset($_GET['host'])) {
    $host = $_GET['host'];
  }
  else
    unset($host);

  $host = explode(':', $host ?? $_SERVER["HTTP_HOST"]);
  $port = $port ?? ($host[1] ?? 80);
  $host = 'http://' . $host[0] . ':' . $port;
  
  // for generating m3u8
  if (isset($_GET['m3u8_rtp'])) {
    header("Content-type: application/text");
    header("Content-Disposition: attachment; filename=index.m3u8");
    header('Access-Control-Allow-Origin: *');
    
    echo "#EXTM3U\n";
    echo "#EXT-X-VERSION:3\n";
    echo "#EXT-X-STREAM-INF:PROGRAM-ID=1, BANDWIDTH=248268\n";
    echo $host . '/' . $protocol . '/' . str_replace(':', '+', $_GET['m3u8_rtp']) . "\n";
    echo "#EXT-X-ENDLIST\n";
    return ;
  }
  
  // generate json from ext txt
  if (isset($_GET['json_extra']) && intval($_GET['json_extra']) == 1) {
    $data = [];
    
    $handle = fopen("GuangdongIPTV_rtp_ext.txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
//            echo $line;
            $pos = strpos($line, ' ');
            $data[] = [
              'MultiCastURI' => trim(substr($line, 0, $pos)),
              'ChannelName' => trim(substr($line, $pos)),
              'UserChannelID' => '000000',
            ];
        }

        fclose($handle);
    }
    
    header('content-type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT);
//    file_put_contents('IPTV_ext.json', json_encode($data, JSON_PRETTY_PRINT));

    return;
  }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>IPTV</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://cdnjs.loli.net/ajax/libs/video.js/7.7.1/video-js.css" rel="stylesheet" />
    
    <script src="https://cdnjs.loli.net/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
    <script src="https://cdnjs.loli.net/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> 
    <script src="https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js"></script> 
    <script src="https://cdnjs.loli.net/ajax/libs/video.js/7.7.1/video.js"></script>
    <!--<script src="https://cdnjs.loli.net/ajax/libs/videojs-contrib-hls/5.15.0/videojs-contrib-hls.js"></script>-->

    <link href="https://fonts.loli.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.loli.net/icon?family=Material+Icons" rel="stylesheet">

    <meta name="msapplication-TileColor" content="#343a40">
    <meta name="theme-color" content="#343a40">
    <meta name="msapplication-navbutton-color" content="#343a40">
    <meta name="apple-mobile-web-app-status-bar-style" content="#343a40">
</head>

<body class="bg-dark">
<div class="container" style="font-family: 'Poppins', sans-serif;">
  <div class="card bg-secondary text-white mt-2">
    <div class="card-header text-center">
      <div class="btn-group float-left" id="all_channel-lists-btn">
        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Channel List
        </button>
        <div class="dropdown-menu" id="all-channel-lists">
          <div class="dropdown-divider"></div>
        </div>
      </div>
      <span id="header-text">IPTV</span>
    </div>
    <div class="card-body">
        <div class="html5-player-main">
        </div>
    </div>
    <div class="card-footer">
      <span class="float-right" id="modification-date"></span>
    </div>
  </div>
</div>
<script>
var channel_data = undefined;
var channel_data_custom = undefined;
var generate_time = undefined;
<?php /*
function get_data() {
  console.log('get_data() called');
  $.getJSON("IPTV.json", function(json){
  channel_data = json['data'];
  generate_time = json['time_gen'];
//    console.log(page_data);
  json = json['data'];
    for (var i = 0; i < json.length; i++) {
//        console.log(json[i]);

    }
  });
}
function update() {
  console.log('update() called');
    console.log('time_gen ' + generate_time);
    console.log('channeldata ' + channel_data);
  if (generate_time !== undefined) {
    $('#modification-date').html(generate_time);
  }
  else 
    $('#modification-date').html('0');
}
*/ ?>
var host = '<?php echo $host; ?>';
var protocol = '<?php echo $protocol; ?>';

function add_channel_info(cdata) {
  var link = '';
  var linktext = '';
  for (var i = 0; i < cdata.length; i++) {
    // button
    $('#all-channel-lists').append($('<a>',{
      'value': cdata[i]['MultiCastURI'],
      'class': "dropdown-item channel-button",
      'href': "#",
    }).html(channel_data[i]['UserChannelID'] + ' - ' + cdata[i]['ChannelName']));
    
    link = host + '/' + protocol + '/' + cdata[i]['MultiCastURI'];
    linktext = protocol + '://' + cdata[i]['MultiCastURI'];
    
    // table
    $('#tbody-insert').append(
        $('<tr>')
          .append($('<td>').text(cdata[i]['UserChannelID'] + ' - ' + cdata[i]['ChannelName']))
          .append($('<td>').html('<a href="' + link + '" target="_blank">' + linktext + '</a>'))
    );
    
    // table
  }
}

$(document).ready(function() {
  $.getJSON("IPTV.json", function(json){
    channel_data = json['data'];
    generate_time = json['time_gen'];

    if (generate_time !== undefined) {
      var dt = eval(generate_time * 1000);
      var myDate = new Date(dt);
      generate_time = myDate.toISOString();
      $('#modification-date').html(generate_time);
    }
    else 
      $('#modification-date').html('0');
      
    $('.html5-player-main').html('<table class="table table-sm table-striped table-dark table-borderless text-white"><thead><tr><th scope="col" style="width: 30%">Channel</th><th scope="col" style="width: 70%">URL</th></tr></thead><tbody id="tbody-insert"/></table>');

    if (channel_data !== undefined) {
      // videojs cannot play mpegts from udpxy(or generated m3u8), hide it for now
      $('#all_channel-lists-btn').hide();
      $('#all-channel-lists').html('');
      
      $('#header-text').html('IPTV (' + channel_data.length + ' RECORDS)');
      add_channel_info(channel_data);
    }
  });
  
//  $.getJSON("IPTV_ext.json", function(json){
  $.getJSON("?json_extra=1", function(json){
    channel_data_custom = json;

    if (channel_data_custom !== undefined) {
      $('#header-text').append(' + (' + channel_data_custom.length + ' RECORDS)');
      add_channel_info(channel_data_custom);
    }
  });

});
<?php
/*
$(document).on('click','.channel-button', function()  {
  // not used now
  return ;
  var rtp_addr = $(this).attr('value');

  if ($('#livestream-video').length === 0) {
//  $('.html5-player-main').html(host + '/' + protocol + '/' + $(this).attr('value'));
    $('.html5-player-main').html(
      $('<video-js id="livestream-video" class="vjs-default-skin" controls preload="auto" height="auto" width="auto" data-setup=\'{"liveui": true}\'/>').append(
        $('<source />', {
            'src': '?port=<?php echo $port; ?>&m3u8_rtp=' + rtp_addr,
            'type': 'application/x-mpegURL',
          }
        )
      )
    );
  }
  else {
    var player_old = videojs('livestream-video'); player_old.pause();
  }
  videojs.options.liveui=true;
  var player = videojs('livestream-video', {liveui: true});
  player.ready(function() {
    this.src({
      src: '?port=<?php echo $port; ?>&m3u8_rtp=' + rtp_addr,
      type: 'application/x-mpegURL',
    });
  });

});
*/
?>
</script>
<?php
// footer.php
?>
</body>
<!--<footer>Powered by Bootstrap 4</footer>-->
</html>
