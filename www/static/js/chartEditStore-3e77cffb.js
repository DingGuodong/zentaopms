var rt=Object.defineProperty,st=Object.defineProperties;var it=Object.getOwnPropertyDescriptors;var W=Object.getOwnPropertySymbols;var ot=Object.prototype.hasOwnProperty,nt=Object.prototype.propertyIsEnumerable;var V=(t,e,a)=>e in t?rt(t,e,{enumerable:!0,configurable:!0,writable:!0,value:a}):t[e]=a,P=(t,e)=>{for(var a in e||(e={}))ot.call(e,a)&&V(t,a,e[a]);if(W)for(var a of W(e))nt.call(e,a)&&V(t,a,e[a]);return t},G=(t,e)=>st(t,it(e));import{ao as ct,ap as Q,aq as ht,ar as dt,as as ut,at as ft,au as lt,av as gt,aw as Ct,U as w,ax as pt,al as I,ay as F,az as Y,aA as J,aB as St,a4 as Tt,aC as bt,aD as vt,aE as mt,aF as Lt,i as M,aG as B,aj as _,ak as Et}from"./index.js";import{a as p,b as d,l as b}from"./plugin-83b37cde.js";var It=ct,kt=function(){return It.Date.now()},At=kt,Ot=/\s/;function wt(t){for(var e=t.length;e--&&Ot.test(t.charAt(e)););return e}var xt=wt,yt=xt,Dt=/^\s+/;function Rt(t){return t&&t.slice(0,yt(t)+1).replace(Dt,"")}var Ft=Rt,Bt=Ft,z=Q,_t=ht,X=0/0,Ut=/^[-+]0x[0-9a-f]+$/i,Nt=/^0b[01]+$/i,Pt=/^0o[0-7]+$/i,Gt=parseInt;function Mt(t){if(typeof t=="number")return t;if(_t(t))return X;if(z(t)){var e=typeof t.valueOf=="function"?t.valueOf():t;t=z(e)?e+"":e}if(typeof t!="string")return t===0?t:+t;t=Bt(t);var a=Nt.test(t);return a||Pt.test(t)?Gt(t.slice(2),a?2:8):Ut.test(t)?X:+t}var Ht=Mt,qt=Q,H=At,j=Ht,$t="Expected a function",Wt=Math.max,Vt=Math.min;function Yt(t,e,a){var r,s,c,h,i,n,l=0,T=!1,g=!1,m=!0;if(typeof t!="function")throw new TypeError($t);e=j(e)||0,qt(a)&&(T=!!a.leading,g="maxWait"in a,c=g?Wt(j(a.maxWait)||0,e):c,m="trailing"in a?!!a.trailing:m);function L(f){var E=r,x=s;return r=s=void 0,l=f,h=t.apply(x,E),h}function k(f){return l=f,i=setTimeout(v,e),T?L(f):h}function A(f){var E=f-n,x=f-l,$=e-E;return g?Vt($,c-x):$}function u(f){var E=f-n,x=f-l;return n===void 0||E>=e||E<0||g&&x>=c}function v(){var f=H();if(u(f))return R(f);i=setTimeout(v,A(f))}function R(f){return i=void 0,m&&r?L(f):(r=s=void 0,h)}function et(){i!==void 0&&clearTimeout(i),l=0,r=n=s=i=void 0}function at(){return i===void 0?h:R(H())}function N(){var f=H(),E=u(f);if(r=arguments,s=this,n=f,E){if(i===void 0)return k(n);if(g)return clearTimeout(i),i=setTimeout(v,e),L(n)}return i===void 0&&(i=setTimeout(v,e)),h}return N.cancel=et,N.flush=at,N}var zt=Yt,tt=(t=>(t.ECHARTS="echarts",t.NAIVE_UI="naiveUI",t.COMMON="common",t.STATIC="static",t))(tt||{}),Xt=(t=>(t.CHARTS="Charts",t.TABLES="Tables",t.INFORMATIONS="Informations",t.DECORATES="Decorates",t))(Xt||{}),jt=(t=>(t.CHARTS="\u56FE\u8868",t.TABLES="\u900F\u89C6\u8868",t.INFORMATIONS="\u4FE1\u606F",t.DECORATES="\u5C0F\u7EC4\u4EF6",t))(jt||{}),Kt=(t=>(t.INPUT="input",t.DATE="date",t.SELECT="select",t))(Kt||{}),Zt=(t=>(t[t.VIEW=0]="VIEW",t[t.CONFIG=1]="CONFIG",t))(Zt||{});const Qt=["#5470c6","#91cc75","#fac858","#ee6666","#73c0de","#3ba272","#fc8452","#9a60b4","#ea7ccc"];var Jt={color:Qt};const te=["#4992ff","#7cffb2","#fddd60","#ff6e76","#58d9f9","#05c091","#ff8a45","#8d48e3","#dd79ff"];var ee={color:te};const ae=["#fc97af","#87f7cf","#f7f494","#72ccff","#f7c5a0","#d4a4eb","#d2f5a6","#76f2f2"];var re={color:ae};const se=["#893448","#d95850","#eb8146","#ffb248","#f2d643","#ebdba4"];var ie={color:se};const oe=["#2ec7c9","#b6a2de","#5ab1ef","#ffb980","#d87a80","#8d98b3","#e5cf0d","#97b552","#95706d","#dc69aa","#07a2a4","#9a7fd1","#588dd5","#f5994e","#c05050","#59678c","#c9ab00","#7eb00a","#6f5553","#c14089"];var ne={color:oe};const ce=["#9b8bba","#e098c7","#8fd3e8","#71669e","#cc70af","#7cb4cc"];var he={color:ce};const de=["#e01f54","#5e4ea5","#f5e8c8","#b8d2c7","#c6b38e","#a4d8c2","#f3d999","#d3758f","#dcc392","#2e4783","#82b6e9","#ff6347","#a092f1","#0a915d","#eaf889","#6699FF","#ff6666","#3cb371","#d5b158","#38b6b6"];var ue={color:de};const fe=["#c12e34","#e6b600","#0098d9","#2b821d","#005eaa","#339ca8","#cda819","#32a487"];var le={color:fe};const ge=["#d87c7c","#919e8b","#d7ab82","#6e7074","#61a0a8","#efa18d","#787464","#cc7e63","#724e58","#4b565b"];var Ce={color:ge};const pe=["#3fb1e3","#6be6c1","#626c91","#a0a7e6","#c4ebad","#96dee8"];var Se={color:pe};const Te=["#516b91","#59c4e6","#edafda","#93b7e3","#a5e7f0","#cbb0e3"];var be={color:Te};const ve=["#4ea397","#22c3aa","#7bd9a5","#d0648a","#f58db2","#f2b3c9"];var me={color:ve};const Le={text:"",show:!0,textStyle:{color:"#BFBFBF",fontSize:18},subtextStyle:{color:"#A2A2A2",fontSize:14}},Ee={show:!0,name:"",nameGap:15,nameTextStyle:{color:"#B9B8CE",fontSize:12},inverse:!1,axisLabel:{show:!0,fontSize:12,color:"#B9B8CE",rotate:0},position:"bottom",axisLine:{show:!0,lineStyle:{color:"#B9B8CE",width:1},onZero:!0},axisTick:{show:!0,length:5},splitLine:{show:!1,lineStyle:{color:"#484753",width:1,type:"solid"}}},Ie={show:!0,name:"",nameGap:15,nameTextStyle:{color:"#B9B8CE",fontSize:12},inverse:!1,axisLabel:{show:!0,fontSize:12,color:"#B9B8CE",rotate:0},position:"left",axisLine:{show:!0,lineStyle:{color:"#B9B8CE",width:1},onZero:!0},axisTick:{show:!0,length:5},splitLine:{show:!0,lineStyle:{color:"#484753",width:1,type:"solid"}}},ke={show:!0,top:"5%",textStyle:{color:"#B9B8CE"}},Ae={show:!1,left:"10%",top:"60",right:"10%",bottom:"60"};var Oe={title:Le,xAxis:Ee,yAxis:Ie,legend:ke,grid:Ae};const Me={dark:ee,customed:Jt,macarons:ne,walden:Se,purplePassion:he,vintage:Ce,chalk:re,westeros:be,wonderland:me,essos:ie,shine:le,roma:ue},we="dark",He={dark:["#4992ff","#7cffb2","rgba(68, 181, 226, 0.3)","rgba(73, 146, 255, 0.5)","rgba(124, 255, 178, 0.5)"],customed:["#5470c6","#91cc75","rgba(84, 112, 198, 0.5)","rgba(84, 112, 198, 0.5)","rgba(145, 204, 117, 0.5)"],macarons:["#2ec7c9","#b6a2de","rgba(182, 162, 222, 0.3)","rgba(46, 199, 201, 0.5)","rgba(182, 162, 222, 0.5)"],walden:["#3fb1e3","#6be6c1","rgba(68, 181, 226, 0.3)","rgba(63, 177, 227, 0.5)","rgba(107, 230, 193, 0.5)"],purplePassion:["#9b8bba","#e098c7","rgba(182, 162, 222, 0.3)","rgba(155, 139, 186, 0.5)","rgba(237, 175, 218, 0.5)"],vintage:["#d87c7c","#919e8b","rgba(182, 162, 222, 0.3)","rgba(216, 124, 124, 0.5)","rgba(145, 158, 139, 0.5)"],chalk:["#fc97af","#87f7cf","rgba(135, 247, 207, 0.3)","rgba(252, 151, 175, 0.5)","rgba(135, 247, 207, 0.5)"],westeros:["#516b91","#edafda","rgba(81, 107, 145, 0.3)","rgba(81, 107, 145, 0.5)","rgba(89, 196, 230, 0.5)"],wonderland:["#4ea397","#22c3aa","rgba(68, 181, 226, 0.3)","rgba(78, 163, 151, 0.5)","rgba(34, 195, 170, 0.5)"],essos:["#893448","#d95850","rgba(137, 52, 72, 0.3)","rgba(137, 52, 72, 0.5)","rgba(217, 88, 80, 0.5)"],shine:["#c12e34","#0098d9","rgba(137, 52, 72, 0.3)","rgba(193, 46, 52, 0.5)","rgba(230, 182, 0, 0.5)"],roma:["#e01f54","#5e4ea5","rgba(137, 52, 72, 0.3)","rgba(224, 31, 84, 0.5)","rgba(94, 78, 165, 0.5)"]},xe=G(P({},Oe),{dataset:null}),ye={requestDataType:dt.STATIC,requestHttpType:ut.GET,requestUrl:"",requestInterval:void 0,requestIntervalUnit:ft.SECOND,requestContentType:lt.DEFAULT,requestParamsBodyType:gt.NONE,requestSQLContent:{sql:"select * from  where"},requestParams:{Body:{"form-data":{},"x-www-form-urlencoded":{},json:"",xml:""},Header:{},Params:{}}};class De{constructor(){this.id=w(),this.isGroup=!1,this.attr=G(P({},pt),{zIndex:-1}),this.styles={filterShow:!1,hueRotate:0,saturate:1,contrast:1,brightness:1,opacity:1,rotateZ:0,rotateX:0,rotateY:0,skewX:0,skewY:0,blendMode:"normal",animations:[]},this.preview={overFlowHidden:!1},this.status={lock:!1,hide:!1},this.request=I(ye),this.filter=void 0,this.events={baseEvent:{[F.ON_CLICK]:void 0,[F.ON_DBL_CLICK]:void 0,[F.ON_MOUSE_ENTER]:void 0,[F.ON_MOUSE_LEAVE]:void 0},advancedEvents:{[Y.VNODE_MOUNTED]:void 0,[Y.VNODE_BEFORE_MOUNT]:void 0}}}}class Re extends De{constructor(){super(...arguments),this.isGroup=!0,this.chartConfig={key:"group",chartKey:"group",conKey:"group",category:"group",categoryName:"group",package:"group",chartFrame:tt.COMMON,title:Ct,image:"",dataset:[]},this.groupList=[],this.key="group",this.option={},this.id=w(),this.attr={w:0,h:0,x:0,y:0,offsetX:0,offsetY:0,zIndex:-1,lockScale:!1}}}var o=(t=>(t.ADD="add",t.DELETE="delete",t.UPDATE="update",t.MOVE="move",t.COPY="copy",t.CUT="cut",t.PASTE="paste",t.TOP="top",t.BOTTOM="bottom",t.UP="up",t.DOWN="down",t.GROUP="group",t.UN_GROUP="unGroup",t.LOCK="lock",t.UNLOCK="unLock",t.HIDE="hide",t.SHOW="show",t))(o||{}),C=(t=>(t.CANVAS="canvas",t.CHART="chart",t))(C||{}),y=(t=>(t.ID="id",t.TARGET_TYPE="targetType",t.ACTION_TYPE="actionType",t.HISTORY_DATA="historyData",t))(y||{});const Fe=J({id:"useChartHistoryStore",state:()=>({backStack:[],forwardStack:[]}),getters:{getBackStack(){return this.backStack},getForwardStack(){return this.forwardStack}},actions:{createStackItem(t,e,a=C.CHART){this.pushBackStackItem(Object.freeze({[y.ID]:new Date().getTime().toString(),[y.HISTORY_DATA]:t,[y.ACTION_TYPE]:e,[y.TARGET_TYPE]:a}))},canvasInit(t){this.createStackItem([t],o.ADD,C.CANVAS)},pushBackStackItem(t,e=!1){t instanceof Array?this.backStack=[...this.backStack,...t]:this.backStack.push(t),this.backStack.splice(0,this.backStack.length-St),!e&&this.clearForwardStack()},pushForwardStack(t){t instanceof Array?this.forwardStack=[...this.forwardStack,...t]:this.forwardStack.push(t)},popBackStackItem(){if(this.backStack.length>0)return this.backStack.pop()},popForwardStack(){if(this.forwardStack.length>0)return this.forwardStack.pop()},clearForwardStack(){this.forwardStack=[]},clearBackStack(){const t=this.getBackStack[0];this.backStack=[t]},backAction(){try{if(p(),this.getBackStack.length>1){const t=this.popBackStackItem();if(!t){d();return}return this.pushForwardStack(t),d(),t}d()}catch(t){b()}},forwardAction(){try{if(p(),this.getForwardStack.length){const t=this.popForwardStack();if(!t){d();return}return this.pushBackStackItem(t,!0),d(),t}d()}catch(t){b()}},createAddHistory(t){this.createStackItem(t,o.ADD,C.CHART)},createUpdateHistory(t){this.createStackItem(t,o.UPDATE,C.CHART)},createDeleteHistory(t){this.createStackItem(t,o.DELETE,C.CHART)},createMoveHistory(t){this.createStackItem(t,o.MOVE,C.CHART)},createLayerHistory(t,e){this.createStackItem(t,e,C.CHART)},createPasteHistory(t){this.createStackItem(t,o.CUT,C.CHART)},createGroupHistory(t){this.createStackItem(t,o.GROUP,C.CHART)},createUnGroupHistory(t){this.createStackItem(t,o.UN_GROUP,C.CHART)},createLockHistory(t){this.createStackItem(t,o.LOCK,C.CHART)},createUnLockHistory(t){this.createStackItem(t,o.UNLOCK,C.CHART)},createHideHistory(t){this.createStackItem(t,o.HIDE,C.CHART)},createShowHistory(t){this.createStackItem(t,o.SHOW,C.CHART)}}});var q=(t=>(t.EDIT_LAYOUT_DOM="editLayoutDom",t.EDIT_CONTENT_DOM="editContentDom",t.OFFSET="offset",t.SCALE="scale",t.USER_SCALE="userScale",t.LOCK_SCALE="lockScale",t.SAVE_STATUS="saveStatus",t.IS_CREATE="isCreate",t.IS_DRAG="isDrag",t.IS_SELECT="isSelect",t))(q||{}),q=(t=>(t.START_X="startX",t.START_Y="startY",t.X="x",t.Y="y",t))(q||{}),U=(t=>(t.EDIT_RANGE="editRange",t.EDIT_CANVAS="editCanvas",t.RIGHT_MENU_SHOW="rightMenuShow",t.MOUSE_POSITION="mousePosition",t.TARGET_CHART="targetChart",t.RECORD_CHART="recordChart",t.EDIT_CANVAS_CONFIG="editCanvasConfig",t.REQUEST_GLOBAL_CONFIG="requestGlobalConfig",t.COMPONENT_LIST="componentList",t))(U||{}),O=(t=>(t.TEXT="text",t.DATE="date",t.DATETIME="datetime",t.SELECT="select",t))(O||{}),D=(t=>(t.TEXT="\u6587\u672C\u6846",t.DATE="\u65E5\u671F",t.DATETIME="\u65F6\u95F4",t.SELECT="\u4E0B\u62C9\u5217\u8868",t))(D||{});const Be=[{value:O.TEXT,label:D.TEXT},{value:O.DATE,label:D.DATE},{value:O.DATETIME,label:D.DATETIME},{value:O.SELECT,label:D.SELECT}];var Z;const K=(Z=window==null?void 0:window.fieldConfig)!=null?Z:[{key:"project",label:"\u9879\u76EE"},{key:"task",label:"\u4EFB\u52A1"},{key:"bug",label:"BUG"},{key:"product",label:"\u4EA7\u54C1"}];class _e{constructor(e){var a,r,s,c,h;this.key=w(),this.diagramIds=[],this.diagramFields={},this.diagramList=[],this.typeOptions=Be,this.fieldOptions=K,this.name=(a=e==null?void 0:e.name)!=null?a:"",this.type=(r=e==null?void 0:e.type)!=null?r:O.TEXT,this.field=(s=e==null?void 0:e.field)!=null?s:K[0].value,this.defaultValue=(c=e==null?void 0:e.defaultValue)!=null?c:this.type==O.SELECT?[]:"",this.diagram=(h=e==null?void 0:e.diagram)!=null?h:[],this.diagram.forEach(i=>{this.diagramIds.push(i.id),this.diagramFields[i.id]=i.field})}getStorageInfo(){return this.diagram=this.diagramIds.map(e=>({id:e,field:this.diagramFields[e]})),{name:this.name,type:this.type,field:this.field,defaultValue:this.defaultValue,diagram:this.diagram}}}const S=Fe(),Ue=Tt(),qe=J({id:"useChartEditStore",state:()=>({editCanvas:{editLayoutDom:null,editContentDom:null,offset:20,scale:1,userScale:1,lockScale:!1,isCreate:!1,isDrag:!1,isSelect:!1,saveStatus:bt.PENDING},rightMenuShow:!1,mousePosition:{startX:0,startY:0,x:0,y:0},targetChart:{hoverId:void 0,selectId:[]},recordChart:void 0,editCanvasConfig:{projectName:void 0,width:1920,height:1080,size:1920,lockScale:!1,filterShow:!1,hueRotate:0,saturate:1,contrast:1,brightness:1,opacity:1,rotateZ:0,rotateX:0,rotateY:0,skewX:0,skewY:0,blendMode:"normal",background:void 0,backgroundImage:void 0,selectColor:!0,chartThemeColor:we,chartThemeSetting:xe,previewScaleType:vt,globalFilter:[]},requestGlobalConfig:{requestDataPond:[],requestOriginUrl:"",requestInterval:mt,requestIntervalUnit:Lt,requestParams:{Body:{"form-data":{},"x-www-form-urlencoded":{},json:"",xml:""},Header:{},Params:{}}},componentList:[]}),getters:{getMousePosition(){return this.mousePosition},getRightMenuShow(){return this.rightMenuShow},getEditCanvas(){return this.editCanvas},getEditCanvasConfig(){return this.editCanvasConfig},getTargetChart(){return this.targetChart},getRecordChart(){return this.recordChart},getRequestGlobalConfig(){return this.requestGlobalConfig},getComponentList(){return this.componentList},getStorageInfo(){const t=I(this.getEditCanvasConfig);return t.globalFilter=t.globalFilter.map(a=>a.getStorageInfo()),I(this.getComponentList).forEach(a=>{delete a.chartConfig.fields,delete a.option.dataset}),{[U.EDIT_CANVAS_CONFIG]:t,[U.COMPONENT_LIST]:this.getComponentList,[U.REQUEST_GLOBAL_CONFIG]:this.getRequestGlobalConfig}},getFilters(){return this.editCanvasConfig.globalFilter}},actions:{setEditCanvas(t,e){this.editCanvas[t]=e},setEditCanvasConfig(t,e){this.editCanvasConfig[t]=e},setRightMenuShow(t){this.rightMenuShow=t},setTargetHoverChart(t){this.targetChart.hoverId=t},setTargetSelectChart(t,e=!1){if(!this.targetChart.selectId.find(a=>a===t)){if(!t){this.targetChart.selectId=[];return}if(e){if(M(t)){this.targetChart.selectId.push(t);return}if(B(t)){this.targetChart.selectId.push(...t);return}}else{if(M(t)){this.targetChart.selectId=[t];return}if(B(t)){this.targetChart.selectId=t;return}}}},setRecordChart(t){this.recordChart=I(t)},setMousePosition(t,e,a,r){t&&(this.mousePosition.x=t),e&&(this.mousePosition.y=e),a&&(this.mousePosition.startX=a),r&&(this.mousePosition.startY=r)},fetchTargetIndex(t){const e=t||this.getTargetChart.selectId.length&&this.getTargetChart.selectId[0]||void 0;if(!e)return d(),-1;const a=this.componentList.findIndex(r=>r.id===e);if(a!==-1)return a;{const r=this.getComponentList.length;for(let s=0;s<r;s++)if(this.getComponentList[s].isGroup){for(const c of this.getComponentList[s].groupList)if(c.id===e)return s}}return-1},idPreFormat(t){const e=[];return t?(M(t)&&e.push(t),B(t)&&e.push(...t),e):(e.push(...this.getTargetChart.selectId),e)},addComponentList(t,e=!1,a=!1){if(Array.isArray(t)){t.forEach(r=>{this.addComponentList(r,e,a)});return}if(a&&S.createAddHistory([t]),e){this.componentList.unshift(t);return}this.componentList.push(t)},removeComponentList(t,e=!0){try{const a=this.idPreFormat(t),r=[];if(!a.length)return;p(),a.forEach(s=>{const c=this.fetchTargetIndex(s);c!==-1&&(r.push(this.getComponentList[c]),this.componentList.splice(c,1))}),e&&S.createDeleteHistory(r),d();return}catch(a){b()}},resetComponentPosition(t,e){const a=this.fetchTargetIndex(t.id);if(a>-1){const r=this.getComponentList[a];e?r.attr=Object.assign(r.attr,{x:t.attr.x+t.attr.offsetX,y:t.attr.y+t.attr.offsetY}):r.attr=Object.assign(r.attr,{x:t.attr.x,y:t.attr.y})}},moveComponentList(t){S.createMoveHistory(t)},updateComponent(t,e){try{p();const a=this.fetchTargetIndex(t);if(a!==-1){const[r]=this.componentList.splice(a,1);e.id=w(),e.attr=r.attr,e.styles=r.styles,this.addComponentList(I(e),!1,!0)}d();return}catch(a){b()}},getChartConfig(t){const e=this.fetchTargetIndex(t);return this.componentList[e].chartConfig},updateComponentList(t,e){t<1&&t>this.getComponentList.length||(this.componentList[t]=e)},setPageStyle(t,e){const a=this.getEditCanvas.editContentDom;a&&(a.style[t]=e)},setBothEnds(t=!1,e=!0){try{if(this.getTargetChart.selectId.length>1)return;p();const a=this.getComponentList.length;if(a<2){d();return}const r=this.fetchTargetIndex(),s=this.getComponentList[r];if(r!==-1){if(t&&r===0||!t&&r===a-1){d();return}const c=(h,i)=>{const n=I(h);return n.attr.zIndex=i,n};e&&S.createLayerHistory([c(s,r)],t?o.BOTTOM:o.TOP),this.addComponentList(s,t),this.getComponentList.splice(t?r+1:r,1),d();return}}catch(a){b()}},setTop(t=!0){this.setBothEnds(!1,t)},setBottom(t=!0){this.setBothEnds(!0,t)},wrap(t=!1,e=!0){try{if(this.getTargetChart.selectId.length>1)return;p();const a=this.getComponentList.length;if(a<2){d();return}const r=this.fetchTargetIndex();if(r!==-1){if(t&&r===0||!t&&r===a-1){d();return}const s=t?r-1:r+1,c=this.getComponentList[r],h=this.getComponentList[s];e&&S.createLayerHistory([c],t?o.DOWN:o.UP),this.updateComponentList(r,h),this.updateComponentList(s,c),d();return}}catch(a){b()}},setUp(t=!0){this.wrap(!1,t)},setDown(t=!0){this.wrap(!0,t)},setCopy(t=!1){try{if(this.getTargetChart.selectId.length>1||document.getElementsByClassName("n-modal-body-wrapper").length)return;p();const e=this.fetchTargetIndex();if(e!==-1){const a={charts:this.getComponentList[e],type:t?o.CUT:o.COPY};this.setRecordChart(a),window.$message.success(t?"\u526A\u5207\u56FE\u8868\u6210\u529F":"\u590D\u5236\u56FE\u8868\u6210\u529F\uFF01"),d()}}catch(e){b()}},setCut(){this.setCopy(!0)},setParse(){try{p();const t=this.getRecordChart;if(t===void 0){d();return}const e=s=>(s=I(s),s.attr.x=this.getMousePosition.x+30,s.attr.y=this.getMousePosition.y+30,s.id=w(),s.isGroup&&s.groupList.forEach(c=>{c.id=w()}),s),a=t.type===o.CUT;(Array.isArray(t.charts)?t.charts:[t.charts]).forEach(s=>{this.addComponentList(e(s),void 0,!0),a&&(this.setTargetSelectChart(s.id),this.removeComponentList(void 0,!0))}),a&&this.setRecordChart(void 0),d()}catch(t){b()}},setBackAndSetForwardHandle(t,e=!1){if(t.targetType===C.CANVAS){this.editCanvas=t.historyData[0];return}this.setTargetSelectChart();let a=t.historyData;B(a)&&a.forEach(u=>{this.setTargetSelectChart(u.id,!0)});const r=t.actionType===o.ADD,s=t.actionType===o.DELETE;if(r||s){if(r&&e||s&&!e){a.forEach(u=>{this.addComponentList(u)});return}a.forEach(u=>{this.removeComponentList(u.id,!1)});return}if(t.actionType===o.MOVE){a.forEach(u=>{this.resetComponentPosition(u,e)});return}const h=t.actionType===o.TOP,i=t.actionType===o.BOTTOM;if(h||i){if(!e){h&&this.getComponentList.pop(),i&&this.getComponentList.shift(),this.getComponentList.splice(a[0].attr.zIndex,0,a[0]);return}h&&this.setTop(!1),i&&this.setBottom(!1)}const n=t.actionType===o.UP,l=t.actionType===o.DOWN;if(n||l){if(n&&e||l&&!e){this.setUp(!1);return}this.setDown(!1);return}const T=t.actionType===o.GROUP,g=t.actionType===o.UN_GROUP;if(T||g){if(T&&e||g&&!e){const u=[];a.length>1?a.forEach(v=>{u.push(v.id)}):a[0].groupList.forEach(R=>{u.push(R.id)}),this.setGroup(u,!1);return}a.length>1?this.setUnGroup([a[0].id],void 0,!1):this.setUnGroup([a[0].groupList[0].id],void 0,!1);return}const m=t.actionType===o.LOCK,L=t.actionType===o.UNLOCK;if(m||L){if(m&&e||L&&!e){a.forEach(u=>{this.setLock(!u.status.lock,!1)});return}a.forEach(u=>{this.setUnLock(!1)});return}const k=t.actionType===o.HIDE,A=t.actionType===o.SHOW;if(k||A){if(k&&e||A&&!e){a.forEach(u=>{this.setHide(!u.status.hide,!1)});return}a.forEach(u=>{this.setShow(!1)});return}},setBack(){try{p();const t=S.backAction();if(!t){d();return}this.setBackAndSetForwardHandle(t),d()}catch(t){b()}},setForward(){try{p();const t=S.forwardAction();if(!t){d();return}this.setBackAndSetForwardHandle(t,!0),d()}catch(t){b()}},setMove(t){const e=this.fetchTargetIndex();if(e===-1)return;const a=this.getComponentList[e].attr,r=Ue.getChartMoveDistance;switch(t){case _.ARROW_UP:a.y-=r;break;case _.ARROW_RIGHT:a.x+=r;break;case _.ARROW_DOWN:a.y+=r;break;case _.ARROW_LEFT:a.x-=r;break}},setGroup(t,e=!0){try{const a=this.idPreFormat(t)||this.getTargetChart.selectId;if(a.length<2)return;p();const r=new Re,s={l:this.getEditCanvasConfig.width,t:this.getEditCanvasConfig.height,r:0,b:0},c=[],h=[],i=[];a.forEach(n=>{const l=this.fetchTargetIndex(n);l!==-1&&this.getComponentList[l].isGroup?this.setUnGroup([n],T=>{T.forEach(g=>{this.addComponentList(g),i.push(g.id)})},!1):l!==-1&&i.push(n)}),i.forEach(n=>{const l=this.componentList.splice(this.fetchTargetIndex(n),1)[0],{x:T,y:g,w:m,h:L}=l.attr,{l:k,t:A,r:u,b:v}=s;s.l=k>T?T:k,s.t=A>g?g:A,s.r=u<T+m?T+m:u,s.b=v<g+L?g+L:v,c.push(l),h.push(Et(l))}),e&&S.createGroupHistory(h),c.forEach(n=>{n.attr.x=n.attr.x-s.l,n.attr.y=n.attr.y-s.t,r.groupList.push(n)}),r.attr.x=s.l,r.attr.y=s.t,r.attr.w=s.r-s.l,r.attr.h=s.b-s.t,r.attr.lockScale=!1,this.addComponentList(r),this.setTargetSelectChart(r.id),d()}catch(a){console.log(a),window.$message.error("\u521B\u5EFA\u5206\u7EC4\u5931\u8D25\uFF0C\u8BF7\u8054\u7CFB\u7BA1\u7406\u5458\uFF01"),d()}},setUnGroup(t,e,a=!0){try{const r=t||this.getTargetChart.selectId;if(r.length!==1)return;p();const s=h=>{const i=this.getComponentList[h];!i.isGroup||(a&&S.createUnGroupHistory(I([i])),i.groupList.forEach(n=>{n.attr.x=n.attr.x+i.attr.x,n.attr.y=n.attr.y+i.attr.y,e||this.addComponentList(n)}),this.setTargetSelectChart(i.id),this.removeComponentList(i.id,!1),e&&e(i.groupList))},c=this.fetchTargetIndex(r[0]);c!==-1&&s(c),d()}catch(r){console.log(r),window.$message.error("\u89E3\u9664\u5206\u7EC4\u5931\u8D25\uFF0C\u8BF7\u8054\u7CFB\u7BA1\u7406\u5458\uFF01"),d()}},setLock(t=!0,e=!0){try{if(this.getTargetChart.selectId.length>1)return;p();const a=this.fetchTargetIndex();if(a!==-1){const r=this.getComponentList[a];r.status.lock=t,e&&(t?S.createLockHistory([r]):S.createUnLockHistory([r])),this.updateComponentList(a,r),t&&this.setTargetSelectChart(void 0),d();return}}catch(a){b()}},setUnLock(t=!0){this.setLock(!1,t)},setHide(t=!0,e=!0){try{if(this.getTargetChart.selectId.length>1)return;p();const a=this.fetchTargetIndex();if(a!==-1){const r=this.getComponentList[a];r.status.hide=t,e&&(t?S.createHideHistory([r]):S.createShowHistory([r])),this.updateComponentList(a,r),d(),t&&this.setTargetSelectChart(void 0)}}catch(a){b()}},setShow(t=!0){this.setHide(!1,t)},setPageSize(t){this.setPageStyle("height",`${this.editCanvasConfig.height*t}px`),this.setPageStyle("width",`${this.editCanvasConfig.width*t}px`)},computedScale(){if(this.getEditCanvas.editLayoutDom){const t=this.getEditCanvas.editLayoutDom.clientWidth-this.getEditCanvas.offset*2-5,e=this.getEditCanvas.editLayoutDom.clientHeight-this.getEditCanvas.offset*4,a=this.editCanvasConfig.width,r=this.editCanvasConfig.height,s=parseFloat((a/r).toFixed(5));if(parseFloat((t/e).toFixed(5))>s){const h=parseFloat((e*s/a).toFixed(5));this.setScale(h>1?1:h)}else{const h=parseFloat((t/s/r).toFixed(5));this.setScale(h>1?1:h)}}else window.$message.warning("\u8BF7\u5148\u521B\u5EFA\u753B\u5E03\uFF0C\u518D\u8FDB\u884C\u7F29\u653E")},listenerScale(){const t=zt(this.computedScale,200);return t(),window.addEventListener("resize",t),()=>{window.removeEventListener("resize",t)}},setScale(t,e=!1){(!this.getEditCanvas.lockScale||e)&&(this.setPageSize(t),this.getEditCanvas.userScale=t,this.getEditCanvas.scale=t)},addFilterList(t){if(Array.isArray(t)){t.forEach(a=>{this.addFilterList(a)});return}const e=new _e(t);this.editCanvasConfig.globalFilter.push(e)},removeFilter(t){const e=this.editCanvasConfig.globalFilter.findIndex(a=>a.key===t);e!=-1&&this.editCanvasConfig.globalFilter.splice(e,1)}}});export{U as C,q as E,Kt as F,o as H,Xt as P,Fe as a,tt as b,Me as c,zt as d,De as e,He as f,xe as g,we as h,Zt as i,Re as j,jt as k,C as l,O as m,qe as u};
