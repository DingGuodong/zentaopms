import{u as O}from"./chartEditStore-48dc4b85.js";import{j as U,d as H,v as V,aP as _,R as Y,r as u,o as F,m as h,e as t,w as n,f as o,p as s,F as M,A as N,t as P}from"./index.js";import{i as E}from"./icon-a9cd6c0d.js";import"./plugin-c1c16fc7.js";const R={class:"go-canvas-setting"},W=H({__name:"index",setup(j){const c=O(),e=c.getEditCanvasConfig,p=c.getEditCanvas,g=V(0),{ScaleIcon:k,FitToScreenIcon:C,FitToWidthIcon:S}=E.carbon,{LockOpenOutlineIcon:A,LockClosedOutlineIcon:w}=E.ionicons5,x=[{key:_.FIT,title:"\u81EA\u9002\u5E94",icon:k,desc:"\u6309\u5C4F\u5E55\u6BD4\u4F8B\u81EA\u9002\u5E94 (\u7559\u767D\u53EF\u80FD\u53D8\u591A)"},{key:_.FULL,title:"\u94FA\u6EE1",icon:C,desc:"\u5F3A\u5236\u94FA\u6EE1 (\u5143\u7D20\u53EF\u80FD\u6324\u538B\u6216\u62C9\u4F38\u53D8\u5F62)"},{key:_.SCROLL_Y,title:"Y\u8F74\u6EDA\u52A8",icon:S,desc:"X\u8F74\u56FA\u5B9A\uFF0CY\u8F74\u81EA\u9002\u5E94\u6EDA\u52A8"}];Y(()=>e.selectColor,i=>{g.value=i?0:1},{immediate:!0});const m=i=>i>50,f=e.width/e.height,D=()=>{e.lockScale&&(e.height=Math.round(e.width/f)),c.computedScale()},y=()=>{e.lockScale&&(e.width=Math.round(e.height*f)),c.computedScale()},B=()=>{e.lockScale=!e.lockScale};return(i,l)=>{const d=u("n-text"),v=u("n-input-number"),b=u("n-icon"),L=u("n-form-item"),I=u("n-form"),T=u("n-radio"),r=u("n-space"),z=u("n-radio-group");return F(),h("div",R,[t(I,{inline:"","label-width":80,size:"small","label-placement":"left"},{default:n(()=>[t(L,{label:"\u753B\u5E03\u5C3A\u5BF8"},{default:n(()=>[t(v,{size:"small",value:o(e).width,"onUpdate:value":[l[0]||(l[0]=a=>o(e).width=a),D],disabled:o(p).lockScale,validator:m},{prefix:n(()=>[t(d,{depth:"3"},{default:n(()=>[s("\u5BBD")]),_:1})]),_:1},8,["value","disabled"]),t(v,{size:"small",value:o(e).height,"onUpdate:value":[l[1]||(l[1]=a=>o(e).height=a),y],disabled:o(p).lockScale,validator:m},{prefix:n(()=>[t(d,{depth:"3"},{default:n(()=>[s("\u9AD8")]),_:1})]),_:1},8,["value","disabled"]),t(b,{size:"16",component:o(e).lockScale?o(w):o(A),onClick:B},null,8,["component"])]),_:1})]),_:1}),t(r,{class:"detail",vertical:"",size:12},{default:n(()=>[t(r,null,{default:n(()=>[t(d,null,{default:n(()=>[s("\u9002\u914D\u65B9\u5F0F")]),_:1}),t(z,{value:o(e).previewScaleType,"onUpdate:value":l[2]||(l[2]=a=>o(e).previewScaleType=a),name:"radiogroup"},{default:n(()=>[t(r,null,{default:n(()=>[(F(),h(M,null,N(x,a=>t(T,{key:a.key,value:a.key},{default:n(()=>[s(P(a.desc),1)]),_:2},1032,["value"])),64))]),_:1})]),_:1},8,["value"])]),_:1})]),_:1})])}}});var K=U(W,[["__scopeId","data-v-0c065615"]]);export{K as default};
