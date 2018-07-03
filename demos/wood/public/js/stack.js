
var logh=20;
var loghPercent = 5;
extraleft=new Array();
extraleft[1]=6;
extraleft[2]=4;
extraleft[3]=5;
extraleft[4]=5;
extraright=10;
var saw=308;
var sah=247;
var sahPercent=130;
var btnHeight=55;
var stdir;
var stwidth=new Array();
var stmargin=new Array();
var sttype=new Array();
var ststart=new Array();
var stscore=0;
sts=0;
stwidth[0]=260; //a ronk maga!
ststart[0]=(stwidth[0]-270)*Math.random();
stmargin[0]=(saw-stwidth[0])/2; //ronk margoja!
sttype[0]=Math.floor(Math.random()*4)+1;
var stinterval;
var paddingtarget=0,paddingnow=0;
var gameisover=1;
var dnd=0;

function firstinit() {
    document.body.addEventListener("touchmove", function(e) {
        e.preventDefault();
    }, false);

    gamesplayed=getCookie('stack_gamesplayed');
    if(gamesplayed == '') {gamesplayed=0;}
    document.getElementById('gamesplayed').innerHTML=gamesplayed;

    theirbest=getCookie('stack_bestscore');
    if(theirbest == '') {theirbest=0;}
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    document.getElementById('yourbest').innerHTML=theirbest;
    imgpreload(new Array(imgUrl+'/dropshadow.png',imgUrl+'/11.png',imgUrl+'/12.png',imgUrl+'/13.png',imgUrl+'/21.png',imgUrl+'/22.png',imgUrl+'/23.png',imgUrl+'/31.png',imgUrl+'/32.png',imgUrl+'/33.png',imgUrl+'/41.png',imgUrl+'/42.png',imgUrl+'/43.png'));
    document.body.addEventListener("touchmove", function(e) {
        e.preventDefault();
    }, false);
}

function init() {
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var iw = ih = (iOS) ? screen.width:  window.innerWidth, ih = (iOS) ? screen.height -70:  window.innerHeight;
    ih = window.innerHeight;

    sah = ih;

    $('st_outerarea').style.height= ih+"px";
    $('st_outerarea').style.width= iw+"px";

    tmp=document.createElement('div')
    tmp.id='st_scrollarea';
    var imgUrl = httpDomain+"/public/img/";

    var wWidth = window.innerWidth,
        wHeight = window.innerHeight;

    tmp.style.backgroundImage='url('+imgUrl+'/bg1250.png)';
    tmp.style.backgroundPosition='bottom center';
    tmp.style.width='320px';
    tmp.style.height='100%';
    tmp.style.position='absolute';
    $('st_outerarea').appendChild(tmp);

    tmp=document.createElement('div');
    var imgUrl = httpDomain+"/public/img";
    tmp.style.backgroundImage='url('+imgUrl+'/fuhatul.png)';
    tmp.style.position='absolute';
    tmp.style.width='320px';
    tmp.style.height='87px';
    tmp.style.marginTop='130%';
    tmp.style.zIndex='10';
    $('st_scrollarea').appendChild(tmp);

    tmp=document.createElement('div');
    var imgUrl = httpDomain+"/public/img";
    tmp.style.backgroundImage='url('+imgUrl+'/fuelol.png)';
    tmp.style.position='absolute';
    tmp.style.width='311px';
    tmp.style.height='39px';
    // tmp.style.marginTop='130%';
    tmp.style.marginTop = (ih-logh)+"px";
    tmp.style.zIndex='20';
    $('st_scrollarea').appendChild(tmp);

    tmp2=Math.floor(Math.random()*4)+1;

    tmp=document.createElement('div');
    tmp.id='stp_0';
    tmp.style.position='absolute';
    tmp.style.height=logh+'px';
    // tmp.style.marginTop="130%";
    tmp.style.marginTop=sah-btnHeight-logh+'px';
    tmp.style.marginLeft=stmargin[0]-extraleft[sttype[0]]+7+'px';
    $('st_scrollarea').appendChild(tmp);

    tmp=document.createElement('div');
    tmp.style.position='absolute';
    tmp.style.cursor='pointer';
    if(typeof(tmp.ontouchstart) !== 'undefined') {
        tmp.ontouchstart=handleclick;
    } else {
        tmp.onmousedown=handleclick;
        tmp.onclick = function() {return false;}
    }
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    tmp.style.backgroundImage='url('+imgUrl+'/stackthelog.png)';
    tmp.style.backgroundPosition='center center';
    tmp.style.backgroundRepeat='no-repeat';
    tmp.style.width='298px';
    tmp.style.height='55px';
    tmp.style.padding='17px 11px 0 11px';
    tmp.style.zIndex='30';
    tmp.style.margin='0px';
    tmp.style.marginTop=ih-btnHeight+"px";
    $('st_outerarea').appendChild(tmp);


    showaronk('stp_0',sttype[0],ststart[0],stwidth[0]);

    //addshadow();
    console.log("add wood init");
    stack_init();
}

function stack_init() {

    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var iw = ih = (iOS) ? screen.width:  window.innerWidth, ih = (iOS) ? screen.height - 70:  window.innerHeight;
    ih = window.innerHeight;

    if(ih>400) {
        if(sts>10) {
            paddingtarget=(sts-10)*(logh-4);
            if(sts > 15) {
                $('st_scrollarea').removeChild($('stp_'+(sts-16)));
                if(sts % 2 == 0 && sts>16) {
                    try {
                        $('st_scrollarea').removeChild($('shd_'+(sts-16)));
                    }
                    catch(error) {
                        console.error(error);
                    }
                }
            }
        }
    } else {
        if(sts>3) {
            paddingtarget=(sts-3)*(logh-4);
            if(sts > 8) {
                $('st_scrollarea').removeChild($('stp_'+(sts-9)));
                if(sts % 2 == 0 && sts>9) {
                    try {
                        $('st_scrollarea').removeChild($('shd_'+(sts-9)));
                    }
                    catch(error) {
                        console.error(error);
                    }
                }
            }
        }
    }

    sts++;
    stwidth[sts]=stwidth[sts-1];
    ststart[sts]=(stwidth[sts]-270)*Math.random();
    stmargin[sts]=0; stdir=Math.log(sts+1)*2; $('level').innerHTML=Math.floor(sts/10)+1;
    sttype[sts]=Math.floor(Math.random()*4)+1;
    if(Math.random()>.5) {stdir*=-1; stmargin[sts]=saw-stwidth[sts];}
    tmp=document.createElement('div');
    tmp.id='stp_'+sts;
// tmp.style.backgroundColor='#ffeb00';
// tmp.style.overflow='hidden';
    tmp.style.position='absolute';
    tmp.style.height=logh+'px';
    tmp.style.width=extraleft[sttype[sts]]+stwidth[sts]+extraright/2+'px';
    // tmp.style.marginTop=sah-(sts+2)*(logh-4);
    // tmp.style.marginTop=(sahPercent-sts*(loghPercent)-3)+"%";
    tmp.style.marginTop=sah-(sts+2)*(logh-4)-btnHeight+"px";

    console.log(" log percent =="+(sahPercent-sts*(loghPercent)));
    tmp.style.marginLeft=stmargin[sts]-extraleft[sttype[sts]]+7+'px';
    $('st_scrollarea').appendChild(tmp);
    showaronk('stp_'+sts,sttype[sts],ststart[sts],stwidth[sts]);
    stinterval=setInterval('stack_dothemove()',50);
}

function stack_dothemove() {
    //if($('score').innerHTML < stscore) {
    // $('score').innerHTML=Math.floor((parseInt($('score').innerHTML)+stscore*3)/4);
    //}
    if(paddingtarget > paddingnow+1) {
        paddingnow=(paddingtarget+paddingnow)/2;
        console.log("add stack_dothemove");

        $('st_scrollarea').style.paddingTop=paddingnow+'px';
    }
    stmargin[sts]+=stdir;
    if(stmargin[sts] < 0) {stmargin[sts]=-stmargin[sts]; stdir*=-1;}
    else if(stmargin[sts]+stwidth[sts] > saw) {stmargin[sts]=saw-stwidth[sts]-stmargin[sts]-stwidth[sts]+ saw; stdir*=-1;}
    document.getElementById('stp_'+sts).style.marginLeft=stmargin[sts]-extraleft[sttype[sts]]+7+'px';
}

function handleclick() {
    if(dnd==1) {return false;}
    dnd=1;
    clearTimeout(stinterval);
    stwo=stwidth[sts];
    hcf='';
    if(stmargin[sts] < stmargin[sts-1]) {stwidth[sts]-=stmargin[sts-1]-stmargin[sts]; stmargin[sts]=stmargin[sts-1]; hcf='left';}
    if(stmargin[sts]+stwidth[sts] > stmargin[sts-1]+stwidth[sts-1]) {stwidth[sts]-= stmargin[sts]+stwidth[sts] - stmargin[sts-1]-stwidth[sts-1]; hcf='right';}
    if(stwidth[sts] <= 0) {hcf='all'}
    cutaronk('stp_'+sts,sttype[sts],ststart[sts],stwidth[sts],hcf);
    if(stwidth[sts] <= 0) {stwidth[sts]=0;}
    if(sts % 2 == 0 && stwidth[sts] > 0) {
        addshadow();
    }
    bon=Math.floor(stwidth[sts]/10*Math.log(sts+1));
    if(Math.abs(stwidth[sts] - stwidth[sts-1])<3 && stwidth[sts]>0) {
        bon*=2;
        $('combo').innerHTML=parseInt($('combo').innerHTML)+1;
        tmp=document.createElement('div');
        tmp.innerHTML='<div style="padding:5px"><b>漂亮 完美!</b></div>';
        tmp.id='box_perfect';
        tmp.style.zIndex='10';
        tmp.style.position='absolute';
        tmp.style.textAlign='center';
        tmp.style.width='150px';
        tmp.style.backgroundColor='#333333';
        tmp.style.marginLeft='85px';
        // tmp3=(sah-(sts+2)*(logh-4)-16);
        // tmp3 = (sahPercent-sts*(loghPercent));
        tmp3=(sah-(sts+2)*(logh-4)-16)-btnHeight;


        tmp.style.marginTop=tmp3+"px";
        $('st_scrollarea').appendChild(tmp);
        tmp2=new Array();
        tmp2[0]="";
        tmp2[1]="$('score_title').style.color='#ffffff'; $('box_perfect').style.marginTop='"+(tmp3-5)+"px';";
        tmp2[2]="$('box_perfect').style.marginTop='"+(tmp3-10)+"px';";
        tmp2[3]="$('box_perfect').style.marginTop='"+(tmp3-15)+"px';";
        tmp2[4]="$('st_scrollarea').removeChild($('box_perfect')); $('score_title').style.color=''";
        ttt=Math.random();
        stispace[ttt]=tmp2;
        setTI(ttt,50);
    }
    stscore+=bon;
    $('score').innerHTML=stscore;
    if(stscore>theirbest) {$('score').style.color='#ff0000';}
}

function addshadow() {
    console.log("addshadow")
    tmp=document.createElement('img');
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    tmp.src=imgUrl+'/dropshadow.png';
    tmp.id='shd_'+sts;
    tmp.style.zIndex='15';
    tmp.style.position='absolute';
    tmp.style.width=(extraleft[sttype[sts]]+stwidth[sts]+extraright/2)+'px';
    tmp.style.height='30px';
    tmp.style.marginLeft=(stmargin[sts]-extraleft[sttype[sts]]+7)+'px';
    // tmp.style.marginTop=(sah-(sts+1)*(logh-4)-1)+'px';
    // tmp.style.marginTop=(sahPercent-sts*(loghPercent))+'%';
    tmp.style.marginTop=sah-(sts+1)*(logh-4)-1-btnHeight+'px';

    $('st_scrollarea').appendChild(tmp);
}

function cutaronk(crid,crtype,crstart,crstays,crfrom) {
    //srfrom: left: bal oldalrol vag le, right: jobb oldalrol vag le
    croriw=parseInt(document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('div')[0].style.width);
    if(crfrom == 'right') {
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('div')[0].style.width=crstays+'px';
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('img')[0].style.marginLeft=extraleft[crtype]+crstays-extraright/2+'px';
        showaronk(crid,crtype,crstart-crstays,croriw-crstays);
        document.getElementById(crid).childNodes[1].style.marginLeft=crstays+'px';
        document.getElementById(crid).childNodes[1].style.zIndex='12';
    } else if(crfrom == 'left'){
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('div')[0].style.width=crstays+'px';
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('div')[0].style.marginLeft=extraleft[crtype]+croriw-crstays+1+'px';
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('div')[0].style.backgroundPosition=crstart+crstays-croriw+'px';
        document.getElementById(crid).getElementsByTagName('div')[0].getElementsByTagName('img')[1].style.marginLeft=croriw-crstays+'px';
        showaronk(crid,crtype,crstart,croriw-crstays);
        document.getElementById(crid).childNodes[1].style.zIndex=(sts % 2 == 1)?(14):(16);
    }

    if(crfrom !='') {
        tmp=new Array();
        if(crfrom == 'left' || crfrom == 'right') {
            tmp[0]="$('"+crid+"').style.marginTop='"+(sah-(sts+1)*(logh-4)-btnHeight)+"px';document.getElementById('"+crid+"').childNodes[1].style.marginTop='2px';  document.getElementById('"+crid+"').childNodes[0].style.zIndex="+((sts % 2)?(13):(16))+";";
            tmp[1]="$('"+crid+"').childNodes[1].style.opacity='.75'; $('"+crid+"').childNodes[1].style.marginTop='7px';";
            tmp[2]="$('"+crid+"').childNodes[1].style.opacity='.5';$('"+crid+"').childNodes[1].style.marginTop='12px';";
            tmp[3]="$('"+crid+"').childNodes[1].style.opacity='.25';$('"+crid+"').childNodes[1].style.marginTop='17px';";
            tmp[4]="$('"+crid+"').removeChild(document.getElementById('"+crid+"').childNodes[1]);"
        } else if(crfrom =='all') {
            tmp[0]="$('"+crid+"').childNodes[0].style.marginTop='2px';";
            tmp[1]="$('"+crid+"').childNodes[0].style.opacity='.75'; $('"+crid+"').childNodes[0].style.marginTop='7px';";
            tmp[2]="$('"+crid+"').childNodes[0].style.opacity='.5'; $('"+crid+"').childNodes[0].style.marginTop='12px';";
            tmp[3]="$('"+crid+"').childNodes[0].style.opacity='.25'; $('"+crid+"').childNodes[0].style.marginTop='17px';";
            tmp[4]="$('"+crid+"').removeChild(document.getElementById('"+crid+"').childNodes[0]);"
        }
        if(stwidth[sts] >0) { tmp[4]+="stack_init(); dnd=0;"; } else { tmp[4]+='gameover();' }
        ttt=Math.random();
        stispace[ttt]=tmp;
        setTI(ttt,50);
    } else {
        document.getElementById(crid).style.marginTop=(sah-(sts+1)*(logh-4)-btnHeight)+'px';  document.getElementById(crid).childNodes[0].style.zIndex=((sts % 2)?(13):(16));
        stack_init();
    }
}

function showaronk(srid,srtype,srstart,srsize) {
    var httpDomain = document.getElementById("http_domain").getAttribute("data");
    var imgUrl = httpDomain+"/public/img/";
    srt=imgUrl+srtype
    tmp=document.createElement('div');
    tmp.style.position='absolute';
    tmp.style.width=extraleft[srtype]+srsize+extraright/2+'px';
    tmp.style.height=logh+'px';
    tmp.style.zIndex=15;

    tmp2=document.createElement('div');
    tmp2.style.position='absolute';
    tmp2.style.width=srsize+'px';
    tmp2.style.height=logh+'px';
    tmp2.style.marginLeft=extraleft[srtype]+'px';
    tmp2.style.backgroundImage='url('+srt+'2.png)';
    tmp2.style.backgroundPosition=srstart+'px 0';
    tmp.appendChild(tmp2);

    tmp2=document.createElement('img');
    tmp2.src=srt+'3.png';
    tmp2.style.marginLeft=extraleft[srtype]+srsize-extraright/2;
    tmp2.style.position='absolute';
    tmp.appendChild(tmp2);

    tmp2=document.createElement('img');
    tmp2.src=srt+'1.png';
    tmp2.style.position='absolute';
    tmp.appendChild(tmp2);

    $(srid).appendChild(tmp);
}

function gameover() {
    gameisover=1;
    gamesplayed++;
    setCookie('stack_gamesplayed',gamesplayed);
    document.getElementById('gamesplayed').innerHTML=gamesplayed;
    document.getElementById("game_result").setAttribute("data", stscore);
    if(stscore>theirbest) {
        setCookie('stack_bestscore',stscore);
        document.getElementById('yourbest').innerHTML=stscore;

        theirbest=stscore;
        iShareGame('<h1 >游戏结束: '+stscore+'分</h1>厉害，你又破纪录了','restartgame();');
    } else {
        iShareGame('<h1>游戏结束: '+stscore+'分</h1>再来一次?','restartgame();');
    }
    //urchinTracker('/stack/gamesplayed/');
    return false;
}

function restartgame() {
    clearInterval(stinterval);
    gameisover=0;
    stwidth=new Array();
    stmargin=new Array();
    sttype=new Array();
    ststart=new Array();
    stscore=0;
    sts=0;
    stwidth[0]=260; //a ronk maga!
    ststart[0]=(stwidth[0]-270)*Math.random();
    stmargin[0]=(saw-stwidth[0])/2; //ronk margoja!
    sttype[0]=Math.floor(Math.random()*4)+1;
    paddingtarget=0,paddingnow=0;
    dnd=0;
    $('combo').innerHTML=0;
    $('level').innerHTML=1;
    $('score').innerHTML=stscore;
    $('score').style.color='#ffffff';
    $('st_outerarea').innerHTML='';
    init();
}

function pausegame() {
    clearTimeout(stinterval);
}

function resumegame() {
    if(gameisover == 0) {
        stinterval=setInterval('stack_dothemove()',50);
    }
}





//xinli001

var tit="0";
tit=stscore;
var tite="";

