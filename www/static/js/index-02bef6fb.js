import{u as O}from"./chartEditStore-8e1937a4.js";import{j as U,d as H,v as V,aP as p,R as Y,r as u,o as h,m as F,e as t,w as o,f as n,F as M,A as N,p as d,t as P}from"./index.js";import{i as E}from"./icon-9275b84d.js";import"./plugin-ca831a4a.js";const R={class:"go-canvas-setting"},W=d("\u5BBD"),j=d("\u9AD8"),X=d("\u9002\u914D\u65B9\u5F0F"),q=H({__name:"index",setup(G){const c=O(),e=c.getEditCanvasConfig,m=c.getEditCanvas,k=V(0),{ScaleIcon:g,FitToScreenIcon:C,FitToWidthIcon:S}=E.carbon,{LockOpenOutlineIcon:A,LockClosedOutlineIcon:w}=E.ionicons5,x=[{key:p.FIT,title:"\u81EA\u9002\u5E94",icon:g,desc:"\u6309\u5C4F\u5E55\u6BD4\u4F8B\u81EA\u9002\u5E94 (\u7559\u767D\u53EF\u80FD\u53D8\u591A)"},{key:p.FULL,title:"\u94FA\u6EE1",icon:C,desc:"\u5F3A\u5236\u94FA\u6EE1 (\u5143\u7D20\u53EF\u80FD\u6324\u538B\u6216\u62C9\u4F38\u53D8\u5F62)"},{key:p.SCROLL_Y,title:"Y\u8F74\u6EDA\u52A8",icon:S,desc:"X\u8F74\u56FA\u5B9A\uFF0CY\u8F74\u81EA\u9002\u5E94\u6EDA\u52A8"}];Y(()=>e.selectColor,s=>{k.value=s?0:1},{immediate:!0});const f=s=>s>50;let i=1;const D=()=>{e.lockScale&&(e.height=Math.round(e.width/i)),c.computedScale()},y=()=>{e.lockScale&&(e.width=Math.round(e.height*i)),c.computedScale()},B=()=>{e.lockScale=!e.lockScale,i=1,e.lockScale&&(i=e.width/e.height)};return(s,l)=>{const _=u("n-text"),v=u("n-input"),b=u("n-icon"),L=u("n-form-item"),I=u("n-form"),T=u("n-radio"),r=u("n-space"),z=u("n-radio-group");return h(),F("div",R,[t(I,{inline:"","label-width":80,size:"small","label-placement":"left"},{default:o(()=>[t(L,{label:"\u753B\u5E03\u5C3A\u5BF8"},{default:o(()=>[t(v,{size:"small",value:n(e).width,"onUpdate:value":[l[0]||(l[0]=a=>n(e).width=a),D],disabled:n(m).lockScale,validator:f},{prefix:o(()=>[t(_,{depth:"3"},{default:o(()=>[W]),_:1})]),_:1},8,["value","disabled"]),t(v,{size:"small",value:n(e).height,"onUpdate:value":[l[1]||(l[1]=a=>n(e).height=a),y],disabled:n(m).lockScale,validator:f},{prefix:o(()=>[t(_,{depth:"3"},{default:o(()=>[j]),_:1})]),_:1},8,["value","disabled"]),t(b,{size:"16",component:n(e).lockScale?n(w):n(A),onClick:B},null,8,["component"])]),_:1})]),_:1}),t(r,{class:"detail",vertical:"",size:12},{default:o(()=>[t(r,null,{default:o(()=>[t(_,null,{default:o(()=>[X]),_:1}),t(z,{value:n(e).previewScaleType,"onUpdate:value":l[2]||(l[2]=a=>n(e).previewScaleType=a),name:"radiogroup"},{default:o(()=>[t(r,null,{default:o(()=>[(h(),F(M,null,N(x,a=>t(T,{key:a.key,value:a.key},{default:o(()=>[d(P(a.desc),1)]),_:2},1032,["value"])),64))]),_:1})]),_:1},8,["value"])]),_:1})]),_:1})])}}});var $=U(q,[["__scopeId","data-v-b50326e0"]]);export{$ as default};
