import{j as X,d as Y,a3 as Z,an as _,v as T,a5 as $,R as ee,r,o as n,c,w as t,e as a,f as u,m as x,F as D,A as N,z as R,t as b,L as d,aa as G}from"./index.js";import{l as i}from"./index-70f6f3e7.js";import{i as te}from"./icon-fe12b3e9.js";import{C as ae}from"./index-3b8b0a13.js";import{u as P,C as H}from"./chartLayoutStore-9be30d5f.js";import{u as oe}from"./chartEditStore-286181fe.js";import"./index-b94f6bac.js";import"./index-0abaf7e7.js";import"./plugin-b4545888.js";var l=(s=>(s.PAGE_SETTING="pageSetting",s.PAGE_SELECT="pageSelect",s.CHART_SETTING="chartSetting",s.CHART_ANIMATION="chartAnimation",s.CHART_DATA="chartData",s.CHART_EVENT="chartEvent",s))(l||{});const ne=Y({__name:"index",setup(s){const{getDetails:E}=Z(P()),{setItem:A}=P(),p=oe(),{ConstructIcon:w,DesktopOutlineIcon:z,LeafIcon:V,SearchIcon:B}=te.ionicons5,O=i(()=>_(()=>import("./index-b732de7a.js"),["static/js/index-b732de7a.js","static/css/index-556d5487.css","static/js/chartEditStore-286181fe.js","static/js/index.js","static/css/index-a1e82c00.css","static/js/plugin-b4545888.js","static/js/icon-fe12b3e9.js","static/js/index-70f6f3e7.js","static/css/index-8038d4fd.css","static/js/index-b94f6bac.js","static/js/index-0abaf7e7.js","static/css/index-24d4cc74.css","static/js/index-24d355d9.js","static/css/index-94ff02f2.css","static/js/tables_list-71790294.js","static/js/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-618b90ad.js","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-ca40cd4c.css","static/js/useTargetData.hook-18b4d332.js","static/js/chartLayoutStore-9be30d5f.js","static/js/index-3b8b0a13.js","static/css/index-3e214e52.css","static/js/index-f1e3e4d6.js","static/css/index-0b9d86bb.css"])),F=i(()=>_(()=>import("./index-403e5545.js"),["static/js/index-403e5545.js","static/css/index-4dc86e21.css","static/js/chartEditStore-286181fe.js","static/js/index.js","static/css/index-a1e82c00.css","static/js/plugin-b4545888.js","static/js/icon-fe12b3e9.js"])),M=i(()=>_(()=>import("./index-7b22d9f1.js"),["static/js/index-7b22d9f1.js","static/css/index-c9039cb2.css","static/js/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-618b90ad.js","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-ca40cd4c.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/chartEditStore-286181fe.js","static/js/plugin-b4545888.js","static/js/icon-fe12b3e9.js"])),U=i(()=>_(()=>import("./index-be1fe9c0.js"),["static/js/index-be1fe9c0.js","static/css/index-a04ef501.css","static/js/useTargetData.hook-18b4d332.js","static/js/chartEditStore-286181fe.js","static/js/index.js","static/css/index-a1e82c00.css","static/js/plugin-b4545888.js","static/js/icon-fe12b3e9.js"])),j=i(()=>_(()=>import("./index-66ed3afa.js"),["static/js/index-66ed3afa.js","static/css/index-6f5f5e19.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-618b90ad.js","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-ca40cd4c.css","static/js/useTargetData.hook-18b4d332.js","static/js/chartEditStore-286181fe.js","static/js/plugin-b4545888.js","static/js/icon-fe12b3e9.js"])),m=T(E.value),f=T(l.CHART_SETTING),y=T(l.PAGE_SETTING),C=()=>{m.value=!0,A(H.DETAILS,!0)},I=()=>{m.value=!1,A(H.DETAILS,!1)},g=$(()=>{if(p.getTargetChart.selectId.length!==1)return;const o=p.componentList[p.fetchTargetIndex()];return o!=null&&o.isGroup&&(f.value=l.CHART_SETTING),o});ee(E,v=>{v?C():I()});const q=[{key:l.PAGE_SETTING,title:"\u9875\u9762\u914D\u7F6E",icon:z,render:F},{key:l.PAGE_SELECT,title:"\u5168\u5C40\u7B5B\u9009\u5668",icon:B,render:M}],J=[{key:l.CHART_SETTING,title:"\u57FA\u7840\u4FE1\u606F",icon:w,render:U},{key:l.CHART_ANIMATION,title:"\u52A8\u753B",icon:V,render:j}];return(v,o)=>{const K=r("n-layout-content"),S=r("n-icon"),h=r("n-space"),L=r("n-tab-pane"),k=r("n-tabs"),Q=r("n-layout-sider"),W=r("n-layout");return n(),c(W,{"has-sider":"","sider-placement":"right"},{default:t(()=>[a(K,null,{default:t(()=>[a(u(O))]),_:1}),a(Q,{"collapse-mode":"transform","collapsed-width":0,width:296,collapsed:m.value,"native-scrollbar":!1,"show-trigger":"bar",onCollapse:C,onExpand:I},{default:t(()=>[a(u(ae),{class:"go-content-configurations go-boderbox","show-top":!1,depth:2},{default:t(()=>[u(g)?G("",!0):(n(),c(k,{key:0,value:y.value,"onUpdate:value":o[0]||(o[0]=e=>y.value=e),class:"tabs-box",size:"small",type:"segment"},{default:t(()=>[(n(),x(D,null,N(q,e=>a(L,{key:e.key,name:e.key,size:"small","display-directive":"show:lazy"},{tab:t(()=>[a(h,null,{default:t(()=>[R("span",null,b(e.title),1),a(S,{size:"16",class:"icon-position"},{default:t(()=>[(n(),c(d(e.icon)))]),_:2},1024)]),_:2},1024)]),default:t(()=>[(n(),c(d(e.render)))]),_:2},1032,["name"])),64))]),_:1},8,["value"])),u(g)?(n(),c(k,{key:1,value:f.value,"onUpdate:value":o[1]||(o[1]=e=>f.value=e),class:"tabs-box",size:"small",type:"segment"},{default:t(()=>[(n(),x(D,null,N(J,e=>a(L,{key:e.key,name:e.key,size:"small","display-directive":"show:lazy"},{tab:t(()=>[a(h,null,{default:t(()=>[R("span",null,b(e.title),1),a(S,{size:"16",class:"icon-position"},{default:t(()=>[(n(),c(d(e.icon)))]),_:2},1024)]),_:2},1024)]),default:t(()=>[(n(),c(d(e.render)))]),_:2},1032,["name"])),64))]),_:1},8,["value"])):G("",!0)]),_:1})]),_:1},8,["collapsed"])]),_:1})}}});var me=X(ne,[["__scopeId","data-v-c7645194"]]);export{me as default};
