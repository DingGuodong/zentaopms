var j=Object.defineProperty;var I=Object.getOwnPropertySymbols;var H=Object.prototype.hasOwnProperty,J=Object.prototype.propertyIsEnumerable;var D=(l,e,a)=>e in l?j(l,e,{enumerable:!0,configurable:!0,writable:!0,value:a}):l[e]=a,$=(l,e)=>{for(var a in e||(e={}))H.call(e,a)&&D(l,a,e[a]);if(I)for(var a of I(e))J.call(e,a)&&D(l,a,e[a]);return l};import{_ as P}from"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-2d82353d.js";import{S as k}from"./SettingItemBox-6529857c.js";import{u as E,l as G}from"./chartEditStore-2bd55bdd.js";import{d as C,r as s,o as c,c as x,w as n,z as L,n as B,e as t,j as q,m as h,p as U,t as K,aJ as M,a3 as Q,a5 as W,f as u,ab as X,A as V,F as S}from"./index.js";import{i as Y}from"./icon-741b1cd5.js";import"./plugin-bee79b4c.js";const Z=C({__name:"index",props:{name:{type:String,required:!0},expanded:{type:Boolean,required:!0,default:!1}},setup(l){const e=l,a=i=>{i.preventDefault(),i.stopPropagation()};return(i,_)=>{const g=s("n-collapse-item"),m=s("n-collapse");return c(),x(m,{"arrow-placement":"right","expanded-names":e.expanded?e.name:null,accordion:""},{"header-extra":n(()=>[L("div",{onClick:a},[B(i.$slots,"header")])]),default:n(()=>[t(g,{title:e.name,name:e.name},{default:n(()=>[B(i.$slots,"default")]),_:3},8,["title","name"])]),_:3},8,["expanded-names"])}}});const ee={class:"go-config-item-box"},te=C({__name:"index",props:{name:{type:String,required:!1},alone:{type:Boolean,default:!1,required:!1},itemRightStyle:{type:Object,default:()=>{},required:!1}},setup(l){return(e,a)=>{const i=s("n-text");return c(),h("div",ee,[t(i,{class:"item-left",depth:"2"},{default:n(()=>[U(K(l.name)+" ",1),B(e.$slots,"name",{},void 0,!0)]),_:3}),L("div",{class:"item-right",style:M($({gridTemplateColumns:l.alone?"1fr":"1fr 1fr"},l.itemRightStyle))},[B(e.$slots,"default",{},void 0,!0)],4)])}}});var ne=q(te,[["__scopeId","data-v-433ff70c"]]);const ae=C({__name:"index",props:{filter:{type:Object,required:!0},remove:{type:Function},idx:{type:Number,required:!0}},setup(l){const e=l,{RemoveIcon:a}=Y.ionicons5,{remove:i}=Q(e),_={margin:"12px 0"},g=p=>!e.filter.name||e.filter.name.length==0?"\u7B5B\u9009\u5668 - \u672A\u547D\u540D"+p:"\u7B5B\u9009\u5668 - "+e.filter.name,m=E(),b=W(()=>m.getComponentList.filter(d=>d.chartConfig.sourceID&&d.chartConfig.fields).map(d=>d.chartConfig).map(d=>{const y=[];for(const F in d.fields){const v=d.fields[F];y.push({value:v.field,label:v.name})}const f="diagram-"+d.sourceID;return{key:f,name:d.title,fields:y,expanded:e.filter.diagramIds.includes(f)}}));return(p,r)=>{const d=s("n-icon"),y=s("n-button"),f=s("n-select"),F=s("n-tree-select"),v=s("n-input"),N=s("n-checkbox"),z=s("n-gi"),w=s("n-grid"),A=s("n-checkbox-group"),O=s("n-space"),R=s("n-card");return c(),x(R,{title:g(e.idx),size:"small"},{"header-extra":n(()=>[t(y,{quaternary:"",title:"\u79FB\u9664",onClick:u(i)},{default:n(()=>[t(d,{size:"20"},{default:n(()=>[t(u(a))]),_:1})]),_:1},8,["onClick"])]),default:n(()=>[t(u(k),{name:"\u7C7B\u578B",alone:!0,itemBoxStyle:_},{default:n(()=>[t(f,{value:e.filter.type,"onUpdate:value":r[0]||(r[0]=o=>e.filter.type=o),options:e.filter.typeOptions,size:"small"},null,8,["value","options"])]),_:1}),e.filter.type===u(G).SELECT?(c(),x(u(k),{key:0,name:"\u7B5B\u9009\u503C",alone:!0,itemBoxStyle:_},{default:n(()=>[t(F,{options:e.filter.fieldOptions,value:e.filter.field,"onUpdate:value":r[1]||(r[1]=o=>e.filter.field=o),size:"small"},null,8,["options","value"])]),_:1})):X("",!0),t(u(k),{name:"\u540D\u79F0",alone:!0,itemBoxStyle:_},{default:n(()=>[t(v,{value:e.filter.name,"onUpdate:value":r[2]||(r[2]=o=>e.filter.name=o),size:"small",maxlength:"6",minlength:"1","show-count":"",placeholder:"\u8BF7\u8F93\u5165\u540D\u79F0"},null,8,["value"])]),_:1}),t(u(k),{name:"\u9ED8\u8BA4\u503C",alone:!0,itemBoxStyle:_},{default:n(()=>[t(v,{value:e.filter.defaultValue,"onUpdate:value":r[3]||(r[3]=o=>e.filter.defaultValue=o),size:"small"},null,8,["value"])]),_:1}),t(u(P),{name:"\u5173\u8054\u56FE\u8868",expanded:!0},{default:n(()=>[t(O,{vertical:"",size:[0,10]},{default:n(()=>[t(A,{value:e.filter.diagramIds,"onUpdate:value":r[4]||(r[4]=o=>e.filter.diagramIds=o)},{default:n(()=>[(c(!0),h(S,null,V(u(b),(o,se)=>(c(),x(w,{key:o.key,"x-gap":"12",cols:12},{default:n(()=>[t(z,{span:1},{default:n(()=>[t(N,{value:o.key},null,8,["value"])]),_:2},1024),t(z,{span:11},{default:n(()=>[t(u(Z),{name:o.name,expanded:e.filter.diagramIds.includes(o.key)},{default:n(()=>[t(u(ne),{name:"\u5173\u8054\u5B57\u6BB5",alone:!0},{default:n(()=>[t(f,{value:e.filter.diagramFields[o.key],"onUpdate:value":T=>e.filter.diagramFields[o.key]=T,options:o.fields,size:"small"},null,8,["value","onUpdate:value","options"])]),_:2},1024)]),_:2},1032,["name","expanded"])]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["value"])]),_:1})]),_:1})]),_:1},8,["title"])}}});const oe=U(" + \u6DFB\u52A0 "),le=C({__name:"index",setup(l){const e=E(),a=()=>{e.addFilterList()},i=_=>{e.removeFilter(_)};return(_,g)=>{const m=s("n-button"),b=s("n-space");return c(),h(S,null,[t(m,{class:"btn-add",type:"info",onClick:a},{default:n(()=>[oe]),_:1}),t(b,{size:[0,10]},{default:n(()=>[(c(!0),h(S,null,V(u(e).getFilters,(p,r)=>(c(),x(u(ae),{key:p.key,idx:r+1,filter:p,remove:()=>i(p.key)},null,8,["idx","filter","remove"]))),128))]),_:1})],64)}}});var me=q(le,[["__scopeId","data-v-0b9a15bd"]]);export{me as default};
