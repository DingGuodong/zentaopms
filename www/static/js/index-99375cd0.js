import{j as d,d as _,J as p,bl as f,r as o,o as n,c as l,w as r,m,F as v,A as g,f as w,ad as b,z as h,t as x}from"./index.js";import{u as C}from"./chartEditStore-286181fe.js";import"./plugin-b4545888.js";import"./icon-fe12b3e9.js";const k=_({__name:"index",setup(H){const a=C();p();const c=f([{select:!0,title:"\u5168\u5C4F",event:()=>{var e;(e=window.fullscreen)==null||e.call(window)},className:"btn-full"},{select:!0,title:"\u5B58\u4E3A\u8349\u7A3F",event:()=>{var t;const e=a.getStorageInfo;(t=window.saveAsDraft)==null||t.call(window,e)},className:"btn-full"},{select:!0,title:"\u53D1\u5E03",event:()=>{var t;const e=a.getStorageInfo;(t=window.saveAsPublish)==null||t.call(window,e)},className:"btn-publish"}]);return(e,t)=>{const u=o("n-button"),i=o("n-space");return n(),l(i,{class:"go-mt-0"},{default:r(()=>[(n(!0),m(v,null,g(w(c),s=>(n(),l(u,{key:s.title,class:b(s.className),ghost:"",onClick:s.event},{default:r(()=>[h("span",null,x(s.title),1)]),_:2},1032,["class","onClick"]))),128))]),_:1})}}});var F=d(k,[["__scopeId","data-v-80832d12"]]);export{F as default};
