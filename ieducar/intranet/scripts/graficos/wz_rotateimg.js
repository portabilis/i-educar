/* This notice must be untouched at all times.

wz_rotateimg.js    v. 0.5
The latest version is available at
http://www.walterzorn.com
or http://www.devira.com
or http://www.walterzorn.de

Copyright (c) 2003 Walter Zorn. All rights reserved.
Created 9. 10. 2003 by Walter Zorn (Web: http://www.walterzorn.com )
Last modified: 9. 10. 2003

Can rotate images on a webpage by arbitrary angles.


This program is free software;
you can redistribute it and/or modify it under the terms of the
GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License
at http://www.gnu.org/copyleft/gpl.html for more details.
*/


// PATH TO THE TRANSPARENT 1*1 PX IMAGE (required by NS 4 as spacer)
var spacer = 'transparentpixel.gif';




//window.onerror = new Function('return true;');


function rr_getPageXY(r_o)
{
	if (rr_n4)
	{
		r_o.x = r_o.img.x || 0;
		r_o.y = r_o.img.y || 0;
	}
	else
	{
		var r_p = r_o.img;
		r_o.x = r_o.y = 0;
		while (r_p)
		{
			r_o.x += parseInt(r_p.offsetLeft);
			r_o.y += parseInt(r_p.offsetTop);
			r_p = r_p.offsetParent || null;
		}
	}
};


function rr_getDiv(r_x)
{
	return (
		document.all? document.all[r_x]
		: rr_n4? document.layers[r_x]
		: document.getElementById? document.getElementById(r_x)
		: null
	);
}


function RRObj(r_o, r_ang)
{
	this.name = r_o;
	this.img = document.images[r_o];
	rr_getPageXY(this);
	this.w = this.img.width;
	this.h = this.img.height;
	this.angle = r_ang;
	this.htm = '';
	for (var r_i = this.h; r_i--;)
	{
		for (var r_j = this.w; r_j--;)
		{
			this.htm += '<div id="' + r_o + 'row' + r_i + 'col' + r_j + '"'+
				' style="position:absolute;'+
				'left:' + (this.x+r_j) + 'px;'+
				'top:' + (this.y+r_i) + 'px;'+
				'width:1px;height:1px;'+
				(rr_n4? 'clip:rect(0, 1px 1px 0);">' : 'overflow:hidden;">')+
				'<div style="position:absolute;'+
				'left:' + (-r_j) + 'px;'+
				'top:' + (-r_i) + 'px;">'+
				'<img src="' + this.img.src+ '" '+
				'width="' + this.w + '" height="' + this.h + '">'+
				'<\/div><\/div>';
		}
	}
}


RRObj.prototype.rotateTo = function(r_ang)
{
	this.angle = r_ang;
	var r_sin = Math.sin(r_ang = r_ang*Math.PI/180),
	r_cos = Math.cos(r_ang),
	r_cx = this.w>>1,
	r_cy = this.h>>1,
	r_o,
	r_z = 0;
	for (var r_i = this.h; r_i--;)
	{
		for (var r_j = this.w; r_j--;)
		{
			r_o = this.pxs[r_z++] || null;
			var r_x = r_j-r_cx,
			r_y = r_i-r_cy,
			r_xrot = Math.round(r_x*r_cos-r_y*r_sin+r_cx),
			r_yrot = Math.round(r_x*r_sin+r_y*r_cos+r_cy);
			if (rr_n4) r_o.moveTo(this.x+r_xrot, this.y+r_yrot);
			else if (r_o)
			{
				r_o.style.left = (this.x+r_xrot)+rr_px;
				r_o.style.top = (this.y+r_yrot)+rr_px;
			}
		}
	}
}


RRObj.prototype.swapImage = function(r_x)
{
	//this.nimg.src = r_x;
};


function SET_ROTATABLE()
{
	var r_a = SET_ROTATABLE.arguments, r_htm = '', r_o; 
	window.rr_n4 = !!document.layers;
	window.rr_px = (rr_n4 || !!(window.opera && !(r_o = document.documentElement || document.body) && !r_o.innerHTML))? '' : 'px';
	window.rots = new Array();
	for (var r_i = r_a.length-1; r_i > 0; r_i -= 2)
	{
		r_o = rots[rots.length] = rots[r_a[r_i-1]] = new RRObj(r_a[r_i-1], r_a[r_i]);
		r_htm += r_o.htm;
	    if (rr_n4) r_o.img.src = spacer;
	    else r_o.img.style.visibility = 'hidden';
	}
	document.write((rr_n4? '<div style="position:absolute;background:green;"><\/div>\n' : '') + r_htm);
	for (r_i = rots.length; r_i--;)
	{
		(r_o = rots[r_i]).pxs = new Array(r_o.w*r_o.h);
		for (var r_z = 0, r_j = r_o.h; r_j--;)
		{
			for (var r_k = r_o.w; r_k--; r_z++)
				r_o.pxs[r_z] = rr_getDiv(r_o.name + 'row' + r_j + 'col' + r_k);
		}
		r_o.rotateTo(r_o.angle);
	}
	

function temp()
{
}
}
