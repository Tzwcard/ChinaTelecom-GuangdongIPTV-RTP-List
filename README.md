# ChinaTelecom-GuangdongIPTV-RTP-List
广州电信广东IPTV列表（组播地址）

因为广东IPTV抓出来的RTSP地址全部都带用户验证消息，所以RTSP的地址就不贴出来了，只有IGMP组播地址。<br>
抓取时间2020-03-02 09:36:24。<br>
附带一份拿ffmpeg扫组播地址扫出来的额外的频道表，因为是对着截图手写，可能有不准确。<br>

增加一个index.php，把txt和m3u8放一起再随便一个php环境下打开能把完整列表通过网页显示出来，并且生成udpxy链接（自行填写host和port）。<br>
本来是想写成网页播放器的，但是目前找不到一个可以播放mpegts直播流的html5播放器（鼓捣了半天videojs播不出来，生成m3u再播放也一样，估计需要手动分析metadata再生成m3u），不过就算是半成品也可以直接把链接拖进播放器播放。
