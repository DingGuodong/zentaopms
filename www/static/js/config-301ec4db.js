var c=Object.defineProperty;var s=Object.getOwnPropertySymbols;var p=Object.prototype.hasOwnProperty,d=Object.prototype.propertyIsEnumerable;var r=(o,t,i)=>t in o?c(o,t,{enumerable:!0,configurable:!0,writable:!0,value:i}):o[t]=i,e=(o,t)=>{for(var i in t||(t={}))p.call(t,i)&&r(o,i,t[i]);if(s)for(var i of s(t))d.call(t,i)&&r(o,i,t[i]);return o};import{al as n}from"./index.js";import{e as f}from"./chartEditStore-286181fe.js";import{g as a}from"./index-24d355d9.js";import"./plugin-b4545888.js";import"./icon-fe12b3e9.js";import"./tables_list-71790294.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-618b90ad.js";import"./useTargetData.hook-18b4d332.js";var m=[["\u884C1\u52171","\u884C1\u52172","\u884C1\u52173"],["\u884C2\u52171","\u884C2\u52172","\u884C2\u52173"],["\u884C3\u52171","\u884C3\u52172","\u884C3\u52173"],["\u884C4\u52171","\u884C4\u52172","\u884C4\u52173"],["\u884C5\u52171","\u884C5\u52172","\u884C5\u52173"],["\u884C6\u52171","\u884C6\u52172","\u884C6\u52173"],["\u884C7\u52171","\u884C7\u52172","\u884C7\u52173"],["\u884C8\u52171","\u884C8\u52172","\u884C8\u52173"],["\u884C9\u52171","\u884C9\u52172","\u884C9\u52173"],["\u884C10\u52171","\u884C10\u52172","\u884C10\u52173"]];const g={header:["\u52171","\u52172","\u52173"],dataset:m,index:!0,columnWidth:[30,100,100],align:["center","right","right","right"],rowNum:5,waitTime:2,headerHeight:35,carousel:"single",headerBGC:"#00BAFF",oddRowBGC:"#003B51",evenRowBGC:"#0A2732"};class v extends f{constructor(t){super(),this.key=a.key,this.chartConfig=n(a),this.option=n(g);const{dataset:i,tableInfo:h,title:l}=t;this.chartConfig.title=l,this.option.dataset=i,this.option=e(e({},this.option),h),this.chartConfig.sourceID=t.sourceID,this.chartConfig.fields=t.fields}}export{v as default,g as option};
