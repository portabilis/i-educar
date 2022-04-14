var array_menu = new Array();
var array_id = new Array();
var dentro = 0;
var div_mostrar = "menu_suspenso";

var chars = new Array( 	"Ã", "Â", "Á", "À", "Ä", 	"É", "Ê", "È", "Ë", 	"Í", "Ì", "Ï", "Î", 	"Ô", "Õ", "Ó", "Ò", "Ö", 	"Ú", "Ù", "Û", "Ü", 	"Ý",  		"Ñ", 	"Ç",
						"ã", "â", "á", "à", "ä", 	"é", "ê", "è", "ë", 	"í", "ì", "ï", "î", 	"ô", "õ", "ó", "ò", "ö", 	"ú", "ù", "û", "ü", 	"ý",  		"ñ", 	"ç"
);
var troca = new Array( 	"&Atilde;", "&Acirc;", "&Aacute;", "&Agrave;", "&Auml;", 	"&Eacute;", "&Ecirc;", "&Egrave;", "&Euml;", 	"&Iacute;", "&Igrave;", "&Iuml;", "&Icirc;", 	"&Ocirc;", "&Otilde;", "&Oacute;", "&Ograve;", "&Ouml;", 	"&Uacute;", "&Ugrave;", "&Ucirc;", "&Uuml;", 	"&Yacute;", 	"&Ntilde;", 	"&Ccedil;",
						"&atilde;", "&acirc;", "&aacute;", "&agrave;", "&auml;", 	"&eacute;", "&ecirc;", "&egrave;", "&euml;", 	"&iacute;", "&igrave;", "&iuml;", "&icirc;", 	"&ocirc;", "&otilde;", "&oacute;", "&ograve;", "&ouml;", 	"&uacute;", "&ugrave;", "&ucirc;", "&uuml;", 	"&yacute;", 	"&ntilde;", 	"&ccedil;"
);

// checagem de browser
function cm_bwcheck()
{
	this.ver=navigator.appVersion;
	this.agent=navigator.userAgent.toLowerCase();
	this.dom=document.getElementById?1:0;
	this.op5=(this.agent.indexOf("opera 5")>-1 || this.agent.indexOf("opera/5")>-1) && window.opera;
	this.op6=(this.agent.indexOf("opera 6")>-1 || this.agent.indexOf("opera/6")>-1) && window.opera;
	this.ie5 = (this.agent.indexOf("msie 5")>-1 && !this.op5 && !this.op6);
	this.ie55 = (this.ie5 && this.agent.indexOf("msie 5.5")>-1);
	this.ie6 = (this.agent.indexOf("msie 6")>-1 && !this.op5 && !this.op6);
	this.ie4=(this.agent.indexOf("msie")>-1 && document.all &&!this.op5 &&!this.op6 &&!this.ie5&&!this.ie6);
	this.ie = (this.ie4 || this.ie5 || this.ie6);
	this.mac=(this.agent.indexOf("mac")>-1);
	this.ns6=(this.agent.indexOf("gecko")>-1 || window.sidebar);
	this.ns4=(!this.dom && document.layers)?1:0;
	this.bw=(this.ie6 || this.ie5 || this.ie4 || this.ns4 || this.ns6 || this.op5 || this.op6);
	this.usedom= this.ns6;
	this.reuse = this.ie||this.usedom;
	this.px=this.dom&&!this.op5?"px":"";
	return this;
}
var bw;
/*Variable declaration*/
var cmpage,cm_eventlayer=0,cm_eventlayerE=0;
/*Crossbrowser objects functions*/

function cm_message(txt)
{
	alert(txt);
	return false;
}

function cm_makeObj(obj,nest,o)
{
	if(bw.usedom&&o)
	{
		this.evnt=o;
	}
	else
	{
		if( ! nest )
		{
			nest = '';
		}
		else
		{
			nest = 'document.layers.'+nest+'.';
		}
		this.evnt=bw.dom? document.getElementById(obj): bw.ie4?document.all[obj]:bw.ns4?eval(nest+"document.layers." +obj):0;
	}
	if(!this.evnt)
	{
		return cm_message('The layer does not exist ('+obj+')'
		+'- \nIf your using Netscape please check the nesting of your tags (on the entire page)\nNest:'+nest);
	}
	this.css=bw.dom||bw.ie4?this.evnt.style:this.evnt;
	this.ok=0;
	this.ref=bw.dom||bw.ie4?document:this.css.document;
	this.obj = obj + "Object";
	eval(this.obj + "=this");
	this.x=0;
	this.y=0;
	this.w=0;
	this.h=0;
	this.vis=0;
	return this
}

cm_makeObj.prototype.moveIt = function(x,y)
{
	this.x=x;
	this.y=y;
	this.css.left=x+bw.px;
	this.css.top=y+bw.px
}

cm_makeObj.prototype.showIt = function(o)
{
	this.css.visibility="visible";
	this.css.display="block";
	this.vis=1;
	if(bw.op5&&this.arr)
	{
		this.arr.showIt();
	}
}

cm_makeObj.prototype.hideIt = function(no)
{
	this.css.visibility="hidden";
	this.vis=0;
}

cm_makeObj.prototype.clipTo = function(t,r,b,l,setwidth)
{
	this.w=r;
	this.h=b;
	if(bw.ns4)
	{
		this.css.clip.top=t;
		this.css.clip.right=r;
		this.css.clip.bottom=b;
		this.css.clip.left=l;
	}
	else
	{
		if(t<0)t=0;
		if(r<0)r=0;
		if(b<0)b=0;
		if(b<0)b=0;
		this.css.clip="rect("+t+bw.px+","+r+bw.px+","+b+bw.px+","+l+bw.px+")";
		if(setwidth)
		{
			if(bw.op5||bw.op6)
			{
				this.css.pixelWidth=r;
				this.css.pixelHeight=b;
			}
			else
			{
				this.css.width=r+bw.px; this.css.height=b+bw.px;
			}
		}
	}
}

function cm_active(on,h)
{
	if(this.o.arr)on?this.o.arr.hideIt():bw.op5?this.o.arr.showIt():this.o.arr.css.visibility="inherit"

	if(bw.reuse||bw.usedom)
	{
		if(!this.img2)
		{
			this.o.evnt.className=on?this.cl2:this.cl;
		}
		else
		{
			document.images["img"+this.name].src=on?this.img2.src:this.img1.src;
		}

		if(on && bw.ns6)
		{
			this.o.hideIt();
			this.o.css.visibility='inherit';
		}
	}
	else
	{
		if(!this.img2)
		{
			if(on)
			{
				this.o.over.showIt();
			}
			else
			{
				this.o.over.hideIt();
			}
		}
		else
		{
			this.o.ref.images["img"+this.name].src=on?this.img2.src:this.img1.src;
		}
	}
	this.isactive=on?1:0;
}
/***Pageobject **/
function cm_page()
{
	this.x=0;
	this.x2 = (!bw.ie) ? window.innerWidth : document.body.offsetWidth-30;
	this.y=0;
	this.orgy=this.y2= (!bw.ie)?window.innerHeight:document.body.offsetHeight-6;
	this.x50=this.x2/2;
	this.y50=this.y2/2;
	return this
}
/***check positions**/
function cm_cp(num,w,minus)
{
	if(num)
	{
		if(num.toString().indexOf("%")!=-1)
		{
			var t = w?cmpage.x2:cmpage.y2;
			num=parseInt((t*parseFloat(num)/100));
			if(minus)
			{
				num-=minus;
			}
		}
		else
		{
			num=eval(num);
		}
	}
	else
	{
		num=0;
	}
	return num
}
/**Level object**/
function cm_makeLevel()
{
	var c=this, a=arguments;
	c.width=a[0]||null;
	c.height=a[1]||null;
	c.regClass=a[2]||null;
	c.overClass=a[3]||null;
	c.borderX=a[4]||null;
	c.borderY=a[5]||null;
	c.borderClass=a[6]||null;
	c.rows=a[7]>-1?a[7]:null;
	c.align=a[8]||null;
	c.offsetX=a[9]||null;
	c.offsetY=a[10]||null;
	c.arrow=a[11]||null;
	c.arrowWidth=a[12]||null;
	c.arrowHeight=a[13]||null;
	return c
}
/***Making the main menu object**/
function makeCM(name)
{
	var c=this;
	c.mc=0;
	c.name = name;
	c.id = name;
	c.m=new Array();
	c.level=new Array();
	c.l=new Array();
	c.tim=100;
	c.isresized=0;
	c.isover=0;
	c.zIndex=100;
	c.bar=0;
	c.z=0;
	c.totw=0;
	c.toth=0;
	c.maxw=0;
	c.maxh=0;
	cmpage = new cm_page();
}//events
makeCM.prototype.onshow=""; makeCM.prototype.onhide=""; makeCM.prototype.onconstruct="";
/***Creating layers**/

function cm_divCreate(id,cl,txt,w,c,app,ex,txt2)
{
	if(bw.usedom)
	{
		// adiciona conteudo das DIVs root
		var div=document.createElement("DIV");
		div.className=cl;
		div.id=id;

		if(txt)
		{
			div.innerHTML=txt;
		}
		if(app)
		{
			app.appendChild(div);
			return div;
		}
		if(w)
		{
			document.body.appendChild(div);
		}
		return div;
	}
	else
	{

		var dstr='<div id="'+id+'" class="'+cl+'"';
		if(ex&&bw.reuse)
		{
			dstr+=" "+ex;
		}
		dstr+=">"+txt;
		if(txt2)
		{
			dstr+=txt2;
		}
		if(c)
		{
			dstr+='</div>';
		}
		if(w)
		{
			document.write(dstr);
		}
		else
		{
			return dstr;
		}
	}
	return "";
}

/***Getting layer string for each menu**/
function cm_getLayerStr(m,app,name,fill,clb,arrow,ah,aw,root)
{
	var no=m.nolink,arrstr='',
	l=m.lev,str='',
	txt=m.txt,ev='',
	id=name + '_' + m.name,d1;
	if(app)
	{
		d1=app;
	}
	if((!bw.reuse||l==0) && !no)
	{
		ev=' onmouseover="'+name+'.showsub(\''+m.name+'\')"'
		+' onmouseout="'+name+'.mout(\''+m.name+'\')"' //Added 4.02
		+' onclick="'+name+'.onclck(\''+m.name+'\'); return false" ';
	}
	if(bw.reuse&&l!=0)
	{
		txt='';
	}
	if(l==0)
	{
		str+=d1=cm_divCreate(id+'_0',clb,'');
	}
	str+=m.d2=cm_divCreate(id,m.cl,txt,0,0,d1,ev);
	if(l==0&&bw.usedom)
	{
		m.d2.onclick=new Function(name+'.onclck("'+m.name+'")');
		m.d1=d1;
		m.d2.onmouseover=new Function(name+'.showsub("'+m.name+'")');
		m.d2.onmouseout=new Function(name+'.mout("'+m.name+'")'); //Added 4.02
	}
	if(!bw.reuse && !m.img1 && !no)
	{
		str+=cm_divCreate(id+'_1',m.cl2,txt,0,1);
		str+=cm_divCreate(id+'_3',"clCMAbs",'<a href="#" '+ev+'><img alt="" src="'+root+fill+'" width="'+m.w+'" height="'+m.h+'" border="0" /></a>',0,1);
	}
	str+='</div>';
	if(l==0)
	{
		if(arrow)
		{
			str+=m.d3=cm_divCreate(id+'_a','clCMAbs','<img alt="" height="'+aw+'" width="'+ah+'" src="'+root+arrow+'" />',0,1,d1);
		}
		str+="</div>";
	}
	str+="\n";
	if(!bw.reuse)
	{
		m.txt=null;
		m.d2=null;
		m.d3=null;
	}
	if(bw.usedom)
	{
		if(l==0)
		{
			document.body.appendChild(d1);
		}
		str='';
	}
	return str
}
/***get align num from text (better to evaluate numbers later)**/
function cm_checkalign(a)
{
	switch(a)
	{
		case "right": return 1; break;
		case "left": return 2; break;
		case "bottom": return 3; break;
		case "top": return 4; break;
		case "righttop": return 5; break;
		case "lefttop": return 6; break;
		case "bottomleft": return 7; break;
		case "topleft": return 8; break;
	}
	return null
}
/**Making each individual menu **/
makeCM.prototype.makeMenu=function(name,parent,txt,lnk,targ,w,h,img1,img2,cl,cl2,align,rows,nolink,onclick,onmouseover,onmouseout)
{
	var c = this;
	//c.largura = w;
	if(!name)
	{
		name = c.name+""+c.mc;
	}
	var p = parent!=""&&parent&&c.m[parent]?parent:0;
	if(c.mc==0)
	{
		var tmp=location.href;
		if(tmp.indexOf('file:')>-1||tmp.charAt(1)==':')
		{
			c.root=c.offlineRoot;
		}
		else
		{
			c.root=c.onlineRoot;
		}
		if(c.useBar)
		{
			if(!c.barBorderClass)
			{
				c.barBorderClass=c.barClass;
			}
			c.bar1 = cm_divCreate(c.name+'bbar_0',c.barClass,'',0,1);
			c.bar = cm_divCreate(c.name+'bbar',c.barBorderClass,'',1,1,0,0,c.bar1);
			if(bw.usedom)
			{
				c.bar.appendChild(c.bar1);
			}
		}
	}

	var create=1,img,arrow;
	var m = c.m[name] = new Object();
	m.name=name;
	m.subs=new Array();
	m.parent=p;
	m.arnum=0;
	m.arr=0;
	var l = m.lev = p?c.m[p].lev+1:0;
	c.mc++;
	m.hide=0;

	if(l>=c.l.length)
	{
		var p1,p2=0;
		if(l>=c.level.length)
		{
			p1=c.l[c.level.length-1];
		}
		else
		{
			p1=c.level[l];
		}
		c.l[l]=new Array();
		if(!p2)
		{
			p2=c.l[l-1];
		}
		if(l!=0)
		{
			if(isNaN(p1.align))
			{
				p1["align"]=cm_checkalign(p1.align);
			}
			for(i in p1)
			{
				if(i!="str"&&i!="m")
				{
					if(p1[i]==null)
					{
						c.l[l][i]=p2[i];
					}
					else
					{
						c.l[l][i]=p1[i];
					}
				}
			}
		}
		else
		{
			c.l[l]=c.level[0];
			c.l[l].align=cm_checkalign(c.l[l].align);
		}
		c.l[l]["str"]='';
		c.l[l].m=new Array();
		if(!c.l[l].borderClass)
		{
			c.l[l].borderClass=c.l[l].regClass;
		}
		c.l[l].app=0;
		c.l[l].max=0;
		c.l[l].arnum=0;
		c.l[l].o=new Array();
		c.l[l].arr=new Array();
		c.level[l]=p1=p2=null;
		if(l!=0)
		{
			c.l[l].str=c.l[l].app=cm_divCreate(c.name+ '_' +l+'_0',c.l[l].borderClass,'');
		}
	}
	if(p)
	{
		p = c.m[p];
		p.subs[p.subs.length]=name;
		if(p.subs.length==1&&c.l[l-1].arrow)
		{
			p.arr=1;
			if(p.parent)
			{
				c.m[p.parent].arnum++
				if(c.m[p.parent].arnum>c.l[l-1].arnum)
				{
					c.l[l-1].str+=c.l[l-1].arr[c.l[l-1].arnum]=cm_divCreate(c.name+ '_a' +(l-1)+'_'+c.l[l-1].arnum,'clCMAbs','<img height="'+c.l[l-1].arrowHeight+'" width="'+c.l[l-1].arrowWidth+'" src="'+c.root+c.l[l-1].arrow+'" alt="" />',0,1,c.l[l-1].app);
					c.l[l-1].arnum++;
				}
			}
		}
		if(bw.reuse)
		{
			if(p.subs.length>c.l[l].max)
			{
				c.l[l].max = p.subs.length;
			}
			else
			{
				create=0;
			}
		}
	}
	m.rows=rows>-1?rows:c.l[l].rows;
	m.w=cm_cp(w||c.l[l].width,1);
	m.h=cm_cp(h||c.l[l].height,0);
	m.txt=txt;
	m.lnk=lnk;
	if(align)
	{
		align=cm_checkalign(align);
	}
	m.align=align||c.l[l].align;
	m.cl=cl=cl||c.l[l].regClass;
	m.targ=targ;
	m.cl2=cl2||c.l[l].overClass;
	m.create=create;
	m.mover=onmouseover;
	m.out=onmouseout;
	m.onclck=onclick;
	m.active = cm_active;
	m.isactive=0;
	m.nolink=nolink;
	if(create)
	{
		c.l[l].m[c.l[l].m.length]=name;
	}
	if(img1)
	{
		m.img1 = new Image();
		m.img1.src=c.root+img1;
		if(!img2)
		{
			img2=img1;
		}
		m.img2 = new Image();
		m.img2.src=c.root+img2;
		m.cl="clCMAbs";
		m.txt='';
		if(!bw.reuse&&!nolink)
		{
			m.txt = '<a href="#" onmouseover="'+c.name+'.showsub(\''+name+'\')" onmouseout="'+c.name+'.mout(\''+name+'\')" onclick="'+c.name+'.onclck(\''+name+'\'); return false">';
		}
		m.txt+='<img alt="" src="'+c.root+img1+'" width="'+m.w+'" height="'+m.h+'" id="img'+m.name+'" '
		if(bw.dom&&!nolink)
		{
			m.txt+='style="cursor:pointer; cursor:hand"';
			if(!bw.reuse)
			{
				if(!bw.dom) m.txt+='name="img'+m.name+'"';
				m.txt+=' border="0"';
			}
			m.txt+=' />';
			if(!bw.reuse&&!nolink)
			{
				m.txt+='</a>';
			}
		}
	}
	else
	{
		m.img1=0;
		m.img2=0;
	}
	if(l==0||create)
	{
		c.l[l].str+=cm_getLayerStr(m,c.l[l].app,c.name,c.fillImg,c.l[l].borderClass,c.l[l].arrow,c.l[l].arrowWidth,c.l[l].arrowHeight,c.root);
	}
	if(l==0)
	{
		if(m.w>c.maxw)
		{
			c.maxw=m.w;
		}
		if(m.h>c.maxh)
		{
			c.maxh=m.h;
		}
		c.totw+=c.pxBetween+m.w+c.l[0].borderX;c.toth+=c.pxBetween+m.h+c.l[0].borderY;
	}
	if(lnk && !onmouseover)
	{
		m.mover="self.status='"+c.root+m.lnk+"'";
	}
}
/**Getting x/y coords for subs **/
makeCM.prototype.getcoords=function(m,bx,by,x,y,maxw,maxh,ox,oy)
{
	var a=m.align;
	x+=m.o.x;
	y+=m.o.y;
	switch(a)
	{
		case 1:  x+=m.w+bx; break;
		case 2:  x-=maxw+bx; break;
		case 3:  y+=m.h+by; break;
		case 4:  y-=maxh+by; break;
		case 5:  x-=maxw+bx; y-=maxh-m.h; break;
		case 6:  x+=m.w+bx; y-=maxh-m.h; break;
		case 7:  y+=m.h+by; x-=maxw-m.w; break;
		case 8:  y-=maxh+by; x-=maxw-m.w+bx; break;
	}
	m.subx=x + ox;
	m.suby=y + oy;
}
/**Showing sub elements**/
makeCM.prototype.showsub=function(el)
{
	entro = 1;
	for(var i = 0; i< document.getElementsByTagName("select").length; i++)
	{
		document.getElementsByTagName("select").item(i).style.visibility = "hidden";
	}

	baseLeft = DOM_ObjectPosition_getPageOffsetLeft(document.getElementById(div_mostrar));
	baseTop = DOM_ObjectPosition_getPageOffsetTop(document.getElementById(div_mostrar));

	var c=this, pm=c.m[el];
	if(!pm.b||(c.isresized&&pm.lev>0))
	{
		pm.b=c.l[pm.lev].b;
	}
	c.isover=1
	clearTimeout(c.tim);

	var ln=pm.subs.length,l=pm.lev+1;
	meuNivel = l;
	this.meuNivel = meuNivel;

	if(c.l[pm.lev].a==el&&l!=c.l.length)
	{
		if(c.l[pm.lev+1].a)
		{
			c.hidesub(l+1,el);
		}
		return
	}
	c.hidesub(l,el);
	if(pm.mover)
	{
		eval(pm.mover);
	}
	if(!pm.isactive)
	{
		pm.active(1);
	}
	c.l[pm.lev].a = el;
	if(ln==0)
	{
		return;
	}
	var b = c.l[l].b,
	bx=c.l[l].borderX,
	by=c.l[l].borderY,
	rows=pm.rows;
	var x=bx,
	y=by,
	maxw=0,
	maxh=0,
	cn=0;
	b.hideIt();
	for(var i=0;i<c.l[l].m.length;i++)
	{
		if(!bw.reuse)
		{
			m=c.m[c.l[l].m[i]];
		}
		else
		{
			m=c.m[c.m[el].subs[i]];
		}
		if(m && m.parent==el&&!m.hide)
		{
			if(!bw.reuse)
			{
				o=m.o;
			}
			else
			{
				o=m.o=c.l[l].o[i];
			}
			if(x!=o.x||y!=o.y)
			{
				//o.moveIt(x,y);
				o.moveIt(x,y);
			}
			nl=m.subs.length;
			if(bw.reuse)
			{
				if(o.w!=m.w || o.h!=m.h)
				{
					o.clipTo(0,m.w,m.h,0,1);
				}
				if(o.evnt.className!=m.cl)
				{
					m.isactive=0;
					o.evnt.className=m.cl;
					if(bw.ns6)
					{
						o.hideIt();
						o.css.visibility='inherit';
					} //NS6 bugfix
				}
				if(bw.ie6)
				{
					b.showIt();//IE6 bugfix (scrollbars)
				}
				o.evnt.innerHTML=m.txt;
				if(bw.ie6)
				{
					b.hideIt();
				}
				if(!m.nolink)
				{
					o.evnt.onmouseover=new Function(c.name+".showsub('"+m.name+"')");
					o.evnt.onmouseout=new Function(c.name+".mout('"+m.name+"')"); //Added 4.02
					o.evnt.onclick=new Function(c.name+".onclck('"+m.name+"')");
					if(o.oldcursor)
					{
						o.css.cursor=o.oldcursor;
						o.oldcursor=0;
					}
				}
				else
				{
					o.evnt.onmouseover='';
					o.evnt.onclick='';
					if(o.css.cursor=='')
					{
						o.oldcursor=bw.ns6?"pointer":"hand";
					}
					else
					{
						o.oldcursor=o.css.cursor;
						o.css.cursor="auto";
					}
				}
			}
			if(m.arr)
			{
				o.arr=c.l[l].arr[cn];
				o.arr.moveIt(x + m.w-c.l[l].arrowWidth-3,y+m.h/2-(c.l[l].arrowHeight/2));
				o.arr.css.visibility="inherit";
				cn++;
			}
			else
			{
				o.arr=0;
			}
			if(!rows)
			{
				y+=m.h+by;
				if(m.w>maxw)
				{
					maxw=m.w;
				}
				maxh=y;
			}
			else
			{
				x+=m.w+bx;
				if(m.h>maxh)
				{
					maxh=m.h;
				}
				maxw=x;
			}
			o.css.visibility="inherit";
			if(bw.op5||bw.op6)
			{
				o.showIt();
			}
		}
		else
		{
			o = c.m[c.l[l].m[i]].o;
			o.hideIt();
		}
	}
	if(!rows)
	{
		maxw+=bx*2;
	}
	else
	{
		maxh+=by*2;
	}
	b.clipTo(0,maxw,maxh,0,1);
	if(!pm.subx||!pm.suby||c.srollY>0||c.isresized)
	{
		c.getcoords(pm,c.l[l-1].borderX,c.l[l-1].borderY,pm.b.x,pm.b.y,maxw,maxh,c.l[l-1].offsetX,c.l[l-1].offsetY);
	}
	x=pm.subx;
	y=pm.suby;
	if(l==1)
	{
		b.moveIt(baseLeft + x, baseTop + y);
	}else
	{
		if( meuNivel > 1 )
		{
			//alert( 'movendo para: ' + x + ' : ' + y + ' posicao do pai:' + pm.b.x + " : " + pm.b.y + ' larg: ' + pm.b.w + ' nivel:' + meuNivel );
			x = pm.b.x + pm.b.w;
			y--;
		}
		b.moveIt(x, y);
	}
	if(c.onshow)
	{
		eval(c.onshow);
	}
	b.showIt();
}
/**Hide sub elements **/
makeCM.prototype.hidesub=function(l,el)
{

	var c = this,tmp,m,i,j
	if(!l)
	{
		if(!l)
		{
			l=1;
		}
	}
	for(i=l-1;i<c.l.length;i++)
	{
		if(i>0&&i>l-1)
		{
			c.l[i].b.hideIt();
		}
		if(c.l[i].a&&c.l[i].a!=el)
		{
			m=c.m[c.l[i].a];
			m.active(0,1);
			if(m.mout)
			{
				eval(m.mout);
			}
			c.l[i].a=0;
			if(i>0&&i>l-1)
			{
				if(bw.op5||bw.op6)
				{
					for(j=0;j<c.l[i].m.length;j++)
					{
						c.m[c.l[i].m[j]].o.hideIt();
					}
				}
			}
		}
		if(i>l)
		{
			for(j=0;j<c.l[i-1].arnum;j++)
			{
				c.l[i-1].arr[j].hideIt();
				if(bw.op6)
				{
					c.l[i-1].arr[j].moveIt(-1000,-1000);
				}
			}
		} //opera bug
	}
	if(!l&&c.onhide)
	{
		eval(c.onhide); //onhide event
	}
}
/***Make all menu div objects**/
makeCM.prototype.makeObjects=function(nowrite)
{
	var c = this,oc,name,bx,by,w,h,l,no,ar,id,nest
	if(!nowrite)
	{
		for(i=0;i<c.l.length;i++)
		{
			if(i!=0)
			{
				c.l[i].str+="</div>";
			}
			if(!bw.usedom)
			{
				document.write(c.l[i].str);
			}
			else if(i>0)
			{
				document.body.appendChild(c.l[i].app);
			}
			c.l[i].str=null; //Probably need this on frames version though
		}
	}
	c.z=c.zIndex+2;
	for(i=0;i<c.l.length;i++)
	{
		oc=0;
		if(i!=0)
		{
			bobj=c.l[i].b = new cm_makeObj(c.name + "_"+i+"_0","",c.l[i].app);
			bobj.css.zIndex=c.z;
			if(bw.dom)
			{
				bobj.css.overflow='hidden';
			}
		}
		bx=c.l[i].borderX;
		by=c.l[i].borderY;
		c.l[i].max=0;
		for(j=0;j<c.l[i].m.length;j++)
		{
			m = c.m[c.l[i].m[j]];
			name=m.name;
			w=m.w;
			h=m.h;
			l=m.lev;
			no=m.nolink;
			if(i>0)
			{
				m.b = bobj;
				nest=i;
			}
			else
			{
				m.b = new cm_makeObj(c.name + "_"+name+"_0","",m.d1); m.b.css.zIndex=c.z; m.b.clipTo(0,w+bx*2,h+by*2,0,1);
				nest=name;
			}
			id = c.name + "_"+name; nest=c.name + "_"+nest;
			if(m.create)
			{
				o=m.o=new cm_makeObj(id,nest+"_0",m.d2);
				o.z=o.css.zIndex=c.z+1;
				if(bw.reuse)
				{
					c.l[l].o[oc]=o;
					oc++;
				}
				if(l==0&&m.img1)
				{
					o.css.visibility='inherit';
				}
				if(bw.op5)
				{
					o.showIt();
				}
				o.arr=0;
			}
			if(!bw.reuse||l==0)
			{
				o.clipTo(0,w,h,0,1);
			}
			o.moveIt(bx,by);
			o.z=o.css.zIndex=c.z+2;
			if(j<c.l[i].arnum)
			{
				c.l[i].arr[j]=new cm_makeObj(c.name+"_a"+i+"_"+j,nest+"_0",nowrite?0:c.l[i].arr[j]);
				c.l[i].arr[j].css.zIndex=c.z+30+j;
			}
			else if(l==0&&m.arr==1)
			{
				o.arr=new cm_makeObj(id+"_a",nest+"_0",m.d3);
				o.arr.moveIt(bx+m.w-c.l[i].arrowWidth-3,by+m.h/2-(c.l[i].arrowHeight/2));
				o.arr.css.zIndex=c.z+30;
			}
			if(!no && !bw.reuse && !m.img1)
			{
				o.over=new cm_makeObj(c.name + "_"+name+"_1",nest+"_0"+".document.layers."+id)
				o.over.moveIt(0,0);
				o.over.hideIt();
				o.over.clipTo(0,w,h,0,1);
				o.over.css.zIndex=c.z+3;
				img=new cm_makeObj(c.name + "_"+name+"_3",nest+"_0"+".document.layers."+id);
				img.moveIt(0,0);
				img.css.visibility="inherit";
				img.css.zIndex=c.z+4;
				if(bw.op5)
				{
					img.showIt();
				}
			}
			c.z++;
		}
	}
}
/**Onmouseout**/  //Added 4.02
makeCM.prototype.mout = function()
{
	entro = 0;
	var c = this;
	clearTimeout(c.tim);
	c.isover = 0;
	c.tim = setTimeout("if(!"+c.name+".isover)"+c.name+".hidesub()",c.wait);
	setTimeout("enableSelect();",c.wait+100);
}

function enableSelect()
{
	if(entro != 1)
	{
		for(var i = 0; i< document.getElementsByTagName("select").length; i++)
		{
			//adicionado para corrigir problema de selects que são escondidos em outros scripts do sistema
			var sel = document.getElementsByTagName("select").item(i);
			while(sel.nodeName != "TR" && sel.parentNode.nodeName != "TABLE" && sel.parentNode.nodeName != "BODY")
				sel = sel.parentNode;

			if(getVisibility(sel))
				document.getElementsByTagName("select").item(i).style.visibility = "visible";
		}
	}
}


/**Constructing and initiating top items and bar**/
makeCM.prototype.construct=function(nowrite)
{
	var c=this;
	if(!c.l[0]||c.l[0].m.length==0)
	{
		return cm_message('No menus defined');
	}
	c.makeObjects(nowrite);
	cmpage = new cm_page();
	var mpa,o,maxw=c.maxw,maxh=c.maxh,i,totw=c.totw,toth=c.toth,m,px=c.pxBetween;
	var bx=c.l[0].borderX,by=c.l[0].borderY,x=c.fromLeft;
	y=c.fromTop,mp=c.menuPlacement,rows=c.rows;
	if(rows)
	{
		toth=maxh+by*2;
		totw=totw-px+bx;
	}
	else
	{
		totw=maxw+bx*2;
		toth=toth-px+by;
	}
	switch(mp)
	{
		case "center": x=cmpage.x2/2-totw/2;if(bw.ns4) x-=9; break;
		case "right": x=cmpage.x2-totw; break;
		case "bottom": case "bottomcenter": y=cmpage.y2-toth; if(mp=="bottomcenter") x=cmpage.x2/2-totw/2; break;
		default: if(mp.toString().indexOf(",")>-1) mpa=1; break;
	}
	for(i=0;i<c.l[0].m.length;i++)
	{
		m = c.m[c.l[0].m[i]];
		o = m.b;
		if(mpa)
		{
			rows?x=cm_cp(mp[i]):y=cm_cp(mp[i],0,0,1);
		}
		o.moveIt(x,y);
		o.showIt();
		if(m.arr)
		{
			m.o.arr.showIt();
		}
		o.oy=y;
		if(!mpa)
		{
			rows?x+=m.w+px+bx:y+=m.h+px+by;
		}
	}
	if(c.useBar)
	{ //Background-Bar
	bbx=c.barBorderX;
	bby=c.barBorderY;
	bar1=c.bar1= new cm_makeObj(c.name+'bbar_0',c.name+'bbar',nowrite?0:c.bar1);
	bar=c.bar= new cm_makeObj(c.name+'bbar','',nowrite?0:c.bar);
	bar.css.zIndex=c.zIndex+1;
	//bar.evnt.onmouseover=new Function(cm_eventlayerE)
	var barx=c.barX=="menu"?c.m[c.l[0].m[0]].b.x-bbx:cm_cp(c.barx,1);
	var bary=c.barY=="menu"?c.m[c.l[0].m[0]].b.y-bby:cm_cp(c.barY);
	var barw=c.barWidth=="menu"?totw:cm_cp(c.barWidth,1,bbx*2);
	var barh=c.barHeight=="menu"?toth:cm_cp(c.barHeight,0,bby*2);
	bar1.clipTo(0,barw,barh,0,1);
	bar1.moveIt(bbx,bby);
	bar1.showIt();
	bar.clipTo(0,barw+bbx*2,barh+bby*2,0,1);
	bar.moveIt(barx,bary);
	bar.showIt();
	}
	if(c.resizeCheck)
	{ //Window resize code.
		//bug no ie nao pode ser chamada esta funcao
		var isIE = (navigator.appName.indexOf("Microsoft")!= -1) ? 1:0
		if(!isIE)
			setTimeout('window.onresize=new Function("'+c.name+'.resized()")',100);

	c.resized=cm_resized;
	if(bw.op5||bw.op6)
	{
		document.onmousemove=new Function(c.name+".resized()");
	}
	}
	if(c.onconstruct)
	{
		eval(c.onconstruct); //onconstruct event
	}
	return true;
}
/**Capturing resize**/
var cm_inresize=0;
function cm_resized()
{

	if(cm_inresize)
	{
		return;
	}
	page2=new cm_page();
	var off=(bw.op6||bw.op5)?15:5;
	if(page2.x2<cmpage.x2-off || page2.y2<cmpage.orgy-off || page2.x2>cmpage.x2+off || page2.y2>cmpage.orgy+off)
	{
		if(bw.ie||bw.ns6)
		{
			cmpage=page2;
			this.isresized=1;
			if(onresize)
			{
				eval(this.onresize);

			}
			this.construct(1);
			if(this.onafterresize)
			{
				eval(this.onafterresize)
			}
		}
		else
		{
			cm_inresize=1;
			location.reload();
		}
	}

	setXY();
}
/**Onclick of an item**/
makeCM.prototype.onclck=function(m)
{
	m = this.m[m];
	if(m.onclck)
	{
		eval(m.onclck);
	}
	lnk=m.lnk;
	targ=m.targ;
	if(lnk)
	{
		if(lnk.indexOf("mailto")!=0 && lnk.indexOf("http")!=0)
		{
			lnk=this.root+lnk;
		}
		if(String(targ)=="undefined" || targ=="" || targ==0 || targ=="_self")
		{
			location.href=lnk;
		}
		else if(targ=="_blank")
		{
			window.open(lnk);
		}
		else if(targ=="_top" || targ=="window")
		{
			top.location.href=lnk;
		}
		else if(top[targ])
		{
			top[targ].location.href=lnk;
		}
		else if(parent[targ])
		{
			parent[targ].location.href=lnk;
		}
	}
	else
	{
		return false;
	}
}
// nome, cod,  cod_pai, link, imagem, target
function MontaMenu()
{
	bw=new cm_bwcheck();

	oCMenu=new makeCM("oCMenu");
	oCMenu.pxBetween=0;
	oCMenu.fromLeft=0;
	oCMenu.fromTop=0;
	oCMenu.rows=1;
	oCMenu.menuPlacement="left";
	oCMenu.offlineRoot="";
	oCMenu.onlineRoot="";
	oCMenu.resizeCheck=1;
	oCMenu.wait=500;
	oCMenu.fillImg="cm_fill.gif";
	oCMenu.zIndex=0;

	if(bw.ns4 || bw.op5 || bw.op6){
		oCMenu.onshow="document.layers?document.layers.formLayer.visibility='hidden':document.getElementById('formDiv').style.visibility='hidden';"
		oCMenu.onhide="document.layers?document.layers.formLayer.visibility='visible':document.getElementById('formDiv').style.visibility='visible';"
	}

	oCMenu.useBar=0;
	oCMenu.barWidth="760";
	oCMenu.barHeight="16";
	oCMenu.barClass="clBar";
	oCMenu.barX=30;
	oCMenu.barY=30;
	oCMenu.barBorderX=0;
	oCMenu.barBorderY=0;
	oCMenu.barBorderClass="";

	oCMenu.level[0]=new cm_makeLevel();
	oCMenu.level[0].width=80;
	oCMenu.level[0].height=40;
	oCMenu.level[0].regClass="clLevel0";
	oCMenu.level[0].overClass="clLevel0over";
	oCMenu.level[0].borderX=0;
	oCMenu.level[0].borderY=0;
	oCMenu.level[0].borderClass="clLevel0border";
	oCMenu.level[0].offsetX=0;
	oCMenu.level[0].offsetY=0;
	oCMenu.level[0].rows=0;
	oCMenu.level[0].arrow=0;
	oCMenu.level[0].arrowWidth=0;
	oCMenu.level[0].arrowHeight=0;
	oCMenu.level[0].align="bottom";

	oCMenu.level[1]=new cm_makeLevel()
	oCMenu.level[1].width=oCMenu.level[0].width-2
	oCMenu.level[1].height=30
	oCMenu.level[1].regClass="clLevel1"
	oCMenu.level[1].overClass="clLevel1over"
	oCMenu.level[1].borderX=1
	oCMenu.level[1].borderY=1
	oCMenu.level[1].align="right"
	oCMenu.level[1].offsetX=-(oCMenu.level[0].width-2)/2+30
	oCMenu.level[1].offsetY=0
	oCMenu.level[1].borderClass="clLevel1border"

	oCMenu.level[2]=new cm_makeLevel()
	//oCMenu.level[2].width=150
	oCMenu.level[2].height=30
	oCMenu.level[2].offsetX=0
	oCMenu.level[2].offsetY=0
	oCMenu.level[2].regClass="clLevel2"
	oCMenu.level[2].overClass="clLevel2over"
	oCMenu.level[2].borderClass="clLevel2border"

	var cod_menu_anterior = 0;
	var menu_formatado =  new Array();
	var menu_formatado_tmp =  new Array();

	var id_menu = 1;
	var nivel = 0;
	var nivel_pai = 0;

	for(var i = 0; i < array_menu.length; i++)
	{
		nivel = 0;
		nivel_pai = 0;
		var cod_menu_pai = array_menu[i][2];
		if(menu_formatado.length > 0)
		{
			if(!cod_menu_pai)
			{
				menu_formatado[menu_formatado.length] = new Array(id_menu,array_menu[i][0],array_menu[i][1],array_menu[i][2],array_menu[i][3],'',replace_all(array_menu[i][0],troca,chars).length*10,array_menu[i][4], array_menu[i][5], array_menu[i][6],array_menu[i][7]);
				id_menu++;
			}else
			{
				// Percorre Menu Formatado
				for(var j=0; j < menu_formatado.length; j++)
				{
					// Verifica se algum menu tem o cï¿½digo do pai do menu atual
					if(menu_formatado[j][2] == cod_menu_pai)
					{
						nivel_pai = menu_formatado[j][0];
						// caso encontre o pai do menu atual, porcorre array formatado procurando
						// pelo irmaos do menu atual para formar o nivel.
						var maior_nome = replace_all(array_menu[i][0],troca,chars).length*8+22;
						for(var k=0; k < menu_formatado.length; k++)
						{

							// Encontra irmao e incrementa o nivel
							if(menu_formatado[k][3] == cod_menu_pai)
							{
								nivel++;
								if(menu_formatado[k][6]> maior_nome)
								{
									maior_nome = menu_formatado[k][6];
								}
								menu_formatado[k][6] = maior_nome;
							}
						}
						nivel = menu_formatado[j][0]+"_"+nivel;
					}
				}
				if(nivel)
				{
					if(replace_all(array_menu[i][0],troca,chars).length*8+22 > maior_nome)
					{
						maior_nome = replace_all(array_menu[i][0],troca,chars).length*8+22;
					}
					menu_formatado[menu_formatado.length] = new Array(nivel,array_menu[i][0],array_menu[i][1],array_menu[i][2],array_menu[i][3],nivel_pai, maior_nome,array_menu[i][4], array_menu[i][5], array_menu[i][6],array_menu[i][7] );
				}
			}
		}else
		{
			menu_formatado[menu_formatado.length] = new Array(id_menu,array_menu[i][0],array_menu[i][1],array_menu[i][2],array_menu[i][3],'', replace_all(array_menu[i][0],troca,chars).length*10,array_menu[i][4], array_menu[i][5], array_menu[i][6],array_menu[i][7]);
			id_menu++;
		}

	}

	if(menu_formatado.length > 0)
	{
		for(var i = 0; i< menu_formatado.length; i++)
		{
			if(menu_formatado[i][5])
			{
				if(menu_formatado[i][7])
				{
					// sub-menus

					link = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\"><tr><td valign=\"middle\" width=\"18\"><a href='" + menu_formatado[i][8] + "'></a></td><td valign=\"middle\"><a href='" + menu_formatado[i][8] + "'>" + menu_formatado[i][1] + "</a></td></tr></table>";
					oCMenu.makeMenu(menu_formatado[i][0]+"_",menu_formatado[i][5]+"_", link, menu_formatado[i][8],menu_formatado[i][9],menu_formatado[i][6],'','','','','','','','',menu_formatado[i][10]);
				}
				else
				{
					link = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\"><tr><td valign=\"middle\" width=\"18\"><img src='/intranet/imagens/transp.gif' height='16' width='16' ></td><td valign=\"middle\"><a href='#'>" + menu_formatado[i][1] + "</a></td></tr></table>";
					oCMenu.makeMenu(menu_formatado[i][0]+"_",menu_formatado[i][5]+"_", link, menu_formatado[i][8],menu_formatado[i][9],menu_formatado[i][6],'','','','','','','','',menu_formatado[i][10]);
				}
			}else
			{
				// menu pai (root)
				if( menu_formatado[i][8] )
				{
					link = "<a href='" + menu_formatado[i][8] + "' class='menu_susp_root'>" + menu_formatado[i][1] + "</a>";
				}
				else
				{
					link = "<a href='#' class='menu_susp_root'>" + menu_formatado[i][1] + "</a>";
				}
				oCMenu.makeMenu(menu_formatado[i][0]+"_",'',link,menu_formatado[i][8], menu_formatado[i][9],menu_formatado[i][6],'','','','','','','','',menu_formatado[i][10]);
			}
		}
		oCMenu.construct();
	}
}

function replace_all(palavra, array_procura, array_substitui)
{
	var array_palavra = palavra.split(" ");
	var palavra_final="";
	var espaco = "";
	for(var j=0; j<array_palavra.length;j++)
	{
		palavra = array_palavra[j];
		for(var i = 0; i<array_procura.length;i++)
		{
			palavra = palavra.replace(array_procura[i],array_substitui[i]);
		}
		palavra_final = palavra_final+espaco+palavra;
		espaco = " ";
	}
	return palavra_final;
}
function setXY()
{

	document.getElementById('menu_suspenso').style.height = '40px';
	document.getElementById('menu_suspenso').style.backgroundColor = '#e9eff7';



	x = DOM_ObjectPosition_getPageOffsetLeft(document.getElementById('menu_suspenso'));
	y = DOM_ObjectPosition_getPageOffsetTop(document.getElementById('menu_suspenso'));


	for( i = 1; i <= array_id.length; i++ )
	{

		obj = document.getElementById('oCMenu_' + i + '__0');
		obj.style.left = ( obj.style.left.split('px')[0] * 1 ) + x + 'px';
		obj.style.top = ( obj.style.top.split('px')[0] * 1 ) + y + 'px';

	}
}