var E=(p,e,s)=>new Promise((c,n)=>{var _=t=>{try{r(s.next(t))}catch(a){n(a)}},f=t=>{try{r(s.throw(t))}catch(a){n(a)}},r=t=>t.done?c(t.value):Promise.resolve(t.value).then(_,f);r((s=s.apply(p,e)).next())});import{M as F}from"./index-1e5dced4.js";import{_ as L,o as A}from"./index-cbb010ab.js";import{u as I,E as v}from"./chartEditStore-2bd55bdd.js";import{u as K,a as b}from"./chartLayoutStore-420ceb30.js";import{j as O,d as R,v as M,a5 as N,R as H,am as V,r as D,o as h,m as g,z as l,F as $,A as j,e as o,f as d,w as m,p as S,t as B,ad as z,cj as U,c0 as G}from"./index.js";import{a as J,b as Y,l as q}from"./plugin-bee79b4c.js";import{c as u}from"./index-1068da06.js";import{f as w,b as k,i as P}from"./index-38cf3409.js";import"./icon-741b1cd5.js";import"./index-1cf750cd.js";import"./index-06dc370a.js";import"./index-c8bda7ff.js";import"./table_scrollboard-e30c6082.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-2d82353d.js";import"./SettingItemBox-6529857c.js";import"./useTargetData.hook-b79440ae.js";const Q={class:"go-content-charts-item-animation-patch"},W=["onDragstart","onDblclick"],X={class:"list-header"},Z={class:"list-center go-flex-center go-transition"},tt={class:"list-bottom"},at=R({__name:"index",props:{menuOptions:{type:Array,default:()=>[]}},setup(p){const e=I(),s=K(),c=M(),n=N(()=>s.getChartType),_=(t,a)=>{u(a.chartKey,w(a)),u(a.conKey,k(a)),t.dataTransfer.setData(U.DRAG_KEY,G(A(a,["image"]))),e.setEditCanvas(v.IS_CREATE,!0)},f=()=>{e.setEditCanvas(v.IS_CREATE,!1)},r=t=>E(this,null,function*(){try{J(),u(t.chartKey,w(t)),u(t.conKey,k(t));let a=yield P(t);e.addComponentList(a,!1,!0),e.setTargetSelectChart(a.id),Y()}catch(a){q(),window.$message.warning("\u56FE\u8868\u6B63\u5728\u7814\u53D1\u4E2D, \u656C\u8BF7\u671F\u5F85...")}});return H(()=>n.value,t=>{t===b.DOUBLE&&V(()=>{c.value.classList.add("miniAnimation")})}),(t,a)=>{const C=D("n-ellipsis"),x=D("n-text");return h(),g("div",Q,[l("div",{ref_key:"contentChartsItemBoxRef",ref:c,class:z(["go-content-charts-item-box",[d(n)===d(b).DOUBLE?"double":"single"]])},[(h(!0),g($,null,j(p.menuOptions,(i,T)=>(h(),g("div",{class:"item-box",key:T,draggable:"",onDragstart:y=>_(y,i),onDragend:f,onDblclick:y=>r(i)},[l("div",X,[o(d(F),{class:"list-header-control-btn",mini:!0,disabled:!0}),o(x,{class:"list-header-text",depth:"3"},{default:m(()=>[o(C,null,{default:m(()=>[S(B(i.title),1)]),_:2},1024)]),_:2},1024)]),l("div",Z,[o(d(L),{class:"list-img",chartConfig:i},null,8,["chartConfig"])]),l("div",tt,[o(x,{class:"list-bottom-text",depth:"3"},{default:m(()=>[o(C,{style:{"max-width":"90%"}},{default:m(()=>[S(B(i.title),1)]),_:2},1024)]),_:2},1024)])],40,W))),128))],2)])}}});var xt=O(at,[["__scopeId","data-v-10c59b1b"]]);export{xt as default};
