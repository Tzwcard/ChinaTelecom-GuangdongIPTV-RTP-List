# ChinaTelecom-GuangdongIPTV-RTP-List
广州电信广东IPTV列表（组播地址）

因为广东IPTV抓出来的RTSP地址全部都带用户验证消息，所以RTSP的地址就不贴出来了，只有IGMP组播地址。<br>
抓取时间2021-02-19。<br>
附带一份拿ffmpeg扫组播地址扫出来的额外的频道表，因为是对着截图手写，可能有不准确。<br>

增加一个index.php，把txt和json放一起再随便一个php环境下打开能把完整列表通过网页显示出来，并且生成udpxy链接（自行填写host和port）。<br>
本来是想写成网页播放器的，但是目前找不到一个可以播放mpegts直播流的html5播放器（鼓捣了半天videojs播不出来，生成m3u再播放也一样，估计需要手动分析metadata再生成m3u），不过就算是半成品也可以直接把链接拖进播放器播放。

增加一个gzbn.php，可以直接调用GZBN的直播api拿m3u8地址。虽然上面的源1080的出来720，4K的出来1080，但是等广州台重新回IPTV上之前先顶住吧。<br>
用法：gzbn.php?get_channel=\<general|news|drama|sport|legal|uhd\>。

增加一个GuangdongIPTV_rtp_probe.txt，是ffprobe扫一遍两个组播IP段得出来的视频/音频流数据。<br>

增加一个epg.xml，是通过扫gdtv和cntv的epg api再整理出来的xmltv。其他没有的台也欢迎留言反馈下哪里能有可用的官方api来获取epg。<br>

增加GuangdongIPTV_rtp_{sd/hd/4k}.m3u，是根据ffprobe得出来的结果生成的sd/hd/4k信号播放列表。因为ffprobe有时候会获取不到流分辨率，这种情况下它会把4K或者高清台放到标清列表里，所以列表不一定准确，按实际播放的信号为准。<br>