document.addEventListener("DOMContentLoaded",function(){function e(e,t){e.classList.add(t)}function t(e,t){e.classList.remove(t)}function n(e,t){window.scrollTo(e,t)}function l(){var t=document.getElementsByTagName("body")[0];e(t,"js")}function o(e){var t=document.getElementsByClassName(e);for(i=0;i<t.length;i++)t[i].onclick=function(e){var t=this.getAttribute("data-target");a(t,e)}}function a(n,l){var o=document.getElementById(n);o.classList.contains("js-visible")?(e(o,"js-hidden"),t(o,"js-visible"),t(j,"js-fx")):(t(o,"js-hidden"),e(o,"js-visible"),e(j,"js-fx")),v("content"),l.preventDefault()}function c(e,t,n){c=document.getElementById(e),c.classList.add("sticky"),stickyHeight=c.clientHeight+"px",document.getElementById(t).style.setProperty(n,stickyHeight)}function s(e){overlayElement=document.getElementById(e),overlayElement.style.top=stickyHeight}function d(n){var l="themeswitch.php?theme=",o=document.getElementsByTagName("html")[0];switchingElement=document.getElementById(n),switchingElement.onclick=function(){xmlhttp=new XMLHttpRequest,this.checked?(e(o,x),t(o,E),xmlhttp.open("GET",l+x,!0),xmlhttp.send(),localStorage.setItem("theme",x),console.log("localStorage Theme is: "+x)):(e(o,E),t(o,x),xmlhttp.open("GET","themeswitch.php?theme="+E,!0),xmlhttp.send(),localStorage.setItem("theme",E),console.log("localStorage Theme is: "+E))}}function m(e){elementContainer=document.getElementById(e),elementContainer.addEventListener("click",r,!1)}function r(e){e.target!==e.currentTarget&&(channelLink=e.target.getAttribute("href"),t(j,"js-fx"),u(channelLink),e.preventDefault()),e.stopPropagation()}function u(l){if(""===l){var o=localStorage.getItem("channel");if(null!==o)var l=o}document.getElementById(B).classList.remove("js-hidden"),renderFile="render-feeds.php",xmlhttp=new XMLHttpRequest,xmlhttp.open("GET",renderFile+l,!0),xmlhttp.send(),overlayContainer=document.getElementById("application-overlay"),e(overlayContainer,"js-hidden"),v("content"),xmlhttp.onreadystatechange=function(){if(4===xmlhttp.readyState&&xmlhttp.readyState){outputContainer=document.getElementById("content"),outputContainer.innerHTML=xmlhttp.response,document.getElementById(B).classList.add("js-hidden"),t(elementToFix,"js-fixed"),document.getElementById("application-overlay").classList.remove("js-visible"),n(0,0),localStorage.setItem("channel",l),f("#feed-items li");var e=g(),o=p("#"+e,"data-count");I(o),y(e),h(p("#feed-items li","id"))}}}function g(){return lastReadItemId=localStorage.getItem("lastReadItem"),null!==lastReadItemId||(lastReadItemId=p("#feed-items li","id")),lastReadItemId}function h(e){localStorage.setItem("lastReadItem",e)}function p(e,t){var n=document.querySelector(e).getAttribute(t);return n}function y(e){var t=document.getElementById(e).offsetTop,l=document.getElementById("application-header").clientHeight;n(0,t-l)}function f(e){var t=document.querySelectorAll(e);for(i=0;i<t.length;i++)t[i].onclick=function(e){h(this.id)}}function I(e){badge="#unread-items",badgeValue="#unread-items__count",e>0?(document.querySelector(badgeValue).textContent=e,document.querySelector(badge).classList.add("js-show"),console.log(e)):document.querySelector(badge).classList.add("js-hide")}function v(l){elementToFix=document.getElementById(l),scrollY=window.pageYOffset,elementToFix.classList.contains("js-fixed")?(t(elementToFix,"js-fixed"),elementToFix.style.top="",n(0,scrollYMem)):(e(elementToFix,"js-fixed"),elementToFix.style.top="-"+scrollY+"px",scrollYMem=scrollY)}var E="light",x="dark",B="application-loading",j=document.getElementsByTagName("body")[0],T=localStorage.getItem("theme"),L=document.getElementsByTagName("html")[0];null!==T&&(t(L,E),t(L,x),e(L,T),T===E&&(document.getElementById("theme-switcher").checked=!1),T===x&&(document.getElementById("theme-switcher").checked=!0)),l(),u(""),c("application-header","content","margin-top"),d("theme-switcher"),o("js-overlay-toggle"),s("application-overlay"),m("channels")});