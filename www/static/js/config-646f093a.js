var p=Object.defineProperty,d=Object.defineProperties;var l=Object.getOwnPropertyDescriptors;var r=Object.getOwnPropertySymbols;var m=Object.prototype.hasOwnProperty,g=Object.prototype.propertyIsEnumerable;var e=(o,t,i)=>t in o?p(o,t,{enumerable:!0,configurable:!0,writable:!0,value:i}):o[t]=i,n=(o,t)=>{for(var i in t||(t={}))m.call(t,i)&&e(o,i,t[i]);if(r)for(var i of r(t))g.call(t,i)&&e(o,i,t[i]);return o},f=(o,t)=>d(o,l(t));import{al as a,ax as h}from"./index.js";import{e as c}from"./chartEditStore-1f8105d2.js";import{H as s}from"./index-5c8ab160.js";import"./plugin-9e9b27b9.js";import"./icon-0617f5fe.js";import"./chartLayoutStore-0f501f0a.js";import"./table_scrollboard-3667f4b7.js";/* empty css                                                                */import"./SettingItemBox-aedd53df.js";import"./CollapseItem-05ae1c53.js";import"./useTargetData.hook-65515aeb.js";const u={text:"",icon:"",textSize:30,textColor:"#ffffff",textWeight:"bold",placement:"left-top",distance:8,hint:"\u8FD9\u662F\u63D0\u793A\u6587\u672C",width:0,height:0,paddingX:16,paddingY:8,borderWidth:1,borderStyle:"solid",borderColor:"#1a77a5",borderRadius:6,color:"#ffffff",textAlign:"left",fontWeight:"normal",backgroundColor:"rgba(89, 196, 230, .2)",fontSize:24};class H extends c{constructor(){super(...arguments),this.key=s.key,this.chartConfig=a(s),this.option=a(u),this.attr=f(n({},h),{w:36,h:36,zIndex:1})}}export{H as default,u as option};
