//
// XWUZ standard code
// Having thoughts? Ask here: xwuz@tjp.hu
//
//  if (top.frames.length == 0 && navigator.userAgent.indexOf ('iPhone') == -1 && location.href.indexOf('i2.html') == -1 && location.href.indexOf('research') == -1) {
//   location.href='game.html';
//  }

 var ibox_active='game'; 
 var ibox_cur=0;
 var ibox_targets=Array();
 var ibox_scroll_timer;
 ibox_targets['game']=0;
 ibox_targets['highscore']=-320;
 ibox_targets['about']=-640;
 var now = new Date();
 var expire = new Date();
 expire.setTime(now.getTime() + 1000*60*60*24*90); //90 days
 var stispace=new Array();
 var myims;
 var gamesplayed;
 var theirbest;
console.log("ppp");

 function imgpreload(ims) {
  myims=new Array();
     var httpDomain = document.getElementById("http_domain").getAttribute("data");
     var imgUrl = httpDomain+"/public/img/";
     ims.push(imgUrl+'/b_yes.png');
  ims.push(imgUrl+'/b_no.png');
  ims.push(imgUrl+'/b_okay.png');
  for(i in ims) {//
// XWUZ standard code
// Having thoughts? Ask here: xwuz@tjp.hu
//
//  if (top.frames.length == 0 && navigator.userAgent.indexOf ('iPhone') == -1 && location.href.indexOf('i2.html') == -1 && location.href.indexOf('research') == -1) {
//   location.href='game.html';
//  }

      var ibox_active='game';
      var ibox_cur=0;
      var ibox_targets=Array();
      var ibox_scroll_timer;
      ibox_targets['game']=0;
      ibox_targets['highscore']=-320;
      ibox_targets['about']=-640;
      var now = new Date();
      var expire = new Date();
      expire.setTime(now.getTime() + 1000*60*60*24*90); //90 days
      var stispace=new Array();
      var myims;
      var gamesplayed;
      var theirbest;

      function imgpreload(ims) {
          myims=new Array();
          var httpDomain = document.getElementById("http_domain").getAttribute("data");
          var imgUrl = httpDomain+"/public/img/";
          ims.push(imgUrl+'/b_yes.png');
          ims.push(imgUrl+'/b_no.png');
          ims.push(imgUrl+'/b_okay.png');
          for(i in ims) {
              myims[i]=new Image();
              myims[i].src=ims[i];
          }
      }

      function menuswitch(obj) {
          var whereto=obj.href.split('#')[1];
          if(whereto=='game' && ibox_active == 'game') {pausegame();iconfirm('<h1>新游戏</h1>确定要<br />重新开始游戏吗?','restartgame();'); return false;}
          if(ibox_active == 'game' && whereto!='game') {pausegame();}
          mse='';
          if(ibox_active != 'game' && whereto=='game') {mse='resumegame();';}

          tmp=new Array('game','highscore','about');
          tmp2=new Array();
          tmp2['game']=0;
          tmp2['highscore']='-72px';
          tmp2['about']='-182px';
          for(i in tmp) {
              document.getElementById('button_'+tmp[i]).style.backgroundPosition=tmp2[tmp[i]]+' '+((tmp[i]==whereto)?(0):('-41px'));
          }

          if(whereto == ibox_active) {return false;} else {ibox_start=ibox_targets[ibox_active]; ibox_active=whereto; }
          for(i in ibox_targets) {
              document.getElementById('button_'+i).className=(i==whereto)?('active'):('');
          }
          obj.className='active';
          tmp2=new Array();
          tmp=ibox_start;
          for(i=0;i<=5;i++) {
              tmp=(tmp+ibox_targets[whereto])/2;
              tmp2.push('document.getElementById(\'container_scroller\').style.marginLeft=\''+tmp+'px\';');
          }
          tmp2.push('document.getElementById(\'container_scroller\').style.marginLeft=\''+ibox_targets[whereto]+'px\';');
          if(mse!='') {tmp2.push(mse);}
          ttt=Math.random();
          stispace[ttt]=tmp2;
          setTI(ttt,50);
          return false;
      }

      function idismiss() {
          document.getElementById('alertbox').style.display='none';
          resumegame();
      }


      function ialert(iquestion,idoit) {
          var httpDomain = document.getElementById("http_domain").getAttribute("data");
          var imgUrl = httpDomain+"/public/img/";
          document.getElementById('alert_content').innerHTML=iquestion+'<br style="clear:both" /><br /><a href="#" onclick="idismiss();eval(\''+idoit+'\'); return false;" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;"><img src='+imgUrl+'/b_okay.png alt="确定" /></a>';
          document.getElementById('alertbox').style.display='block';
      }

      function iconfirm(iquestion,idoit) {
          var httpDomain = document.getElementById("http_domain").getAttribute("data");
          var imgUrl = httpDomain+"/public/img/";
          document.getElementById('alert_content').innerHTML=iquestion+'<br style="clear:both" /><br /><a href="#" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;" onclick="idismiss();eval(\''+idoit+'\'); return false;"><img src=imgUrl+"/b_yes.png" alt="Yes" /></a><a href="#" ontouchstart="idismiss(); return false;" onclick="idismiss(); return false;"><img src="./i/b_no.png" alt="No" /></a>';
          document.getElementById('alertbox').style.display='block';
      }

      function getCookie(Name) {
          var search = Name + "="
          if (document.cookie.length > 0) {
              offset = document.cookie.indexOf(search)
              if (offset != -1) {
                  offset += search.length
                  end = document.cookie.indexOf(";", offset)
                  if (end == -1)
                      end = document.cookie.length;
                  return unescape(document.cookie.substring(offset, end))
              }
          }
          return('');
      }

      function setCookie(name, value) {
          document.cookie = name + "=" + escape(value) + ((expire == null) ? "" : ("; expires=" + expire.toGMTString())+';domain=lapp.xinli001.com;path=/;');
      }

      function setTI(todos,interval) {
          if(stispace[todos].length == 0) {delete stispace[todos];return false;}
          eval(stispace[todos].shift());
          setTimeout('setTI('+todos+','+interval+')',interval);
      }

      function $(id) {
          return document.getElementById(id);
      }

      myims[i]=new Image();
   myims[i].src=ims[i];
  }
 }

  function menuswitch(obj) {
   var whereto=obj.href.split('#')[1];
   if(whereto=='game' && ibox_active == 'game') {pausegame();iconfirm('<h1>新游戏</h1>确定要<br />重新开始游戏吗?','restartgame();'); return false;}
   if(ibox_active == 'game' && whereto!='game') {pausegame();}
   mse='';
   if(ibox_active != 'game' && whereto=='game') {mse='resumegame();';}

   tmp=new Array('game','highscore','about'); 
   tmp2=new Array();
   tmp2['game']=0;
   tmp2['highscore']='-72px';
   tmp2['about']='-182px';
   for(i in tmp) {
    document.getElementById('button_'+tmp[i]).style.backgroundPosition=tmp2[tmp[i]]+' '+((tmp[i]==whereto)?(0):('-41px'));
   }
   
   if(whereto == ibox_active) {return false;} else {ibox_start=ibox_targets[ibox_active]; ibox_active=whereto; }
   for(i in ibox_targets) {
    document.getElementById('button_'+i).className=(i==whereto)?('active'):('');
   }
   obj.className='active';
   tmp2=new Array();
   tmp=ibox_start;
   for(i=0;i<=5;i++) {
    tmp=(tmp+ibox_targets[whereto])/2;
    tmp2.push('document.getElementById(\'container_scroller\').style.marginLeft=\''+tmp+'px\';');
   }
   tmp2.push('document.getElementById(\'container_scroller\').style.marginLeft=\''+ibox_targets[whereto]+'px\';');
   if(mse!='') {tmp2.push(mse);}
   ttt=Math.random();
   stispace[ttt]=tmp2;
   setTI(ttt,50);
   return false;
  }

  function createXHR(){
     if(window.XMLHttpRequest){////iE7+ fairefox, opera, chrome, safari
        return new XMLHttpRequest();
     }else if(window.ActiveXObject) { ////ie6-
        return null;
     }
  }

  function iShare() {
      // document.getElementById('alertbox').style.display='none';
      // resumegame();
      console.log("iShare");
      sendDataToServer("share_game");
  }

  function sendDataToServer(pageType){
      var hrefType      = document.getElementById("href_type").getAttribute('data');
      var gameResult    = document.getElementById("game_result").getAttribute("data");
      var httpDomain    = document.getElementById("http_domain").getAttribute("data");
      var chatSessionId = document.getElementById("chat_session_id").getAttribute('data');
      var configName = document.getElementById("config_name").getAttribute('data');

      if (window.XMLHttpRequest) {////iE7+ fairefox, opera, chrome, safari
          console.log("XMLHttpRequest");

          var url = httpDomain + "/index.php";
          var data = {
              page_type: pageType,
              chat_session_id: chatSessionId,
              href_type: hrefType,
              game_result: gameResult,
              config_name:configName
          };

          var xmlHttp = new XMLHttpRequest();
          xmlHttp.open("post", url, true);///异步请求
          xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xmlHttp.send("game_data="+JSON.stringify(data));
          var response = xmlHttp.responseText;
          xmlHttp.onreadystatechange = function () {
              if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                  console.log("success");
              }
          }
          ////readyState
          // 0	UNSENT	Client has been created. open() not called yet.
          // 1	OPENED	open() has been called.
          // 2	HEADERS_RECEIVED	send() has been called, and headers and status are available.
          // 3	LOADING	Downloading; responseText holds partial data.
          // 4	DONE	The operation is complete.
      }
  }


function iNewGame() {
    document.getElementById('alertbox').style.display='none';
    resumegame();
}

function idismiss() {
    document.getElementById('alertbox').style.display='none';
    sendDataToServer("record_game");
    resumegame();
}
function ialert(iquestion,idoit) {
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var ih = (iOS) ? screen.height:  window.innerHeight;
    document.getElementById('alert_content').innerHTML=iquestion+'<br style="clear:both" /><br /><a href="#" onclick="idismiss();eval(\''+idoit+'\'); return false;" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;"><img src='+imgUrl+'/b_okay.png alt="确定" /></a>';

    if(ih>400) {
        document.getElementById('alert_content').style.marginTop=ih*0.2+"px";
    }
    document.getElementById('alertbox').style.display='block';
}


function iconfirm(iquestion,idoit) {
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    document.getElementById('alert_content').innerHTML=iquestion+'<br style="clear:both" /><br /><a href="#" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;" onclick="idismiss();eval(\''+idoit+'\'); return false;"><img src=\'+imgUrl+\'/b_yes.png" alt="Yes" /></a><a href="#" ontouchstart="idismiss(); return false;" onclick="idismiss(); return false;"><img src=\'+imgUrl+\'/b_no.png" alt="No" /></a>';
    document.getElementById('alertbox').style.display='block';
}

function iShareGame(iquestion,idoit) {
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    // document.getElementById('alert_content').innerHTML=iquestion+'<br style="clear:both" /><br /><a href="#" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;" onclick="idismiss();eval(\''+idoit+'\'); return false;"><img src=\'+imgUrl+\'/b_yes.png" alt="Yes" /></a><a href="#" ontouchstart="idismiss(); return false;" onclick="idismiss(); return false;"><img src=\'+imgUrl+\'/b_no.png" alt="No" /></a>';
    var hrefType = document.getElementById("href_type").getAttribute("data");
    var html =  iquestion+'<br style="clear:both" /><br /><button class="woodBtn" ontouchstart="idismiss();eval(\''+idoit+'\'); return false;" onclick="idismiss();eval(\''+idoit+'\'); return false;" >再来一局</button>';

    if(hrefType == "u2_msg" || hrefType == "group_msg") {
         html += '<button class="woodBtnShare" href="#" ontouchstart="iShare(); return false;" onclick="iShare(); return false;" >分享战绩</button>';
    }

    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var ih = (iOS) ? screen.height:  window.innerHeight;


    document.getElementById('alert_content').innerHTML= html;
    if(ih>400) {
        document.getElementById('alert_content').style.marginTop=ih*0.2+"px";
    } else {
        document.getElementById('alert_content').style.marginTop="20px";
    }
    document.getElementById('alertbox').style.display='block';
}



function getCookie(Name) {
  var search = Name + "="
  if (document.cookie.length > 0) {
   offset = document.cookie.indexOf(search)
   if (offset != -1) {
    offset += search.length
    end = document.cookie.indexOf(";", offset)
    if (end == -1)
     end = document.cookie.length;
    return unescape(document.cookie.substring(offset, end))
   }
  }
  return('');
 }

 function setCookie(name, value) {
  document.cookie = name + "=" + escape(value) + ((expire == null) ? "" : ("; expires=" + expire.toGMTString())+';domain=lapp.xinli001.com;path=/;');
 }

function setTI(todos,interval) {
 if(stispace[todos].length == 0) {delete stispace[todos];return false;}
 eval(stispace[todos].shift());
 setTimeout('setTI('+todos+','+interval+')',interval);
}

function $(id) {
 return document.getElementById(id);
}
