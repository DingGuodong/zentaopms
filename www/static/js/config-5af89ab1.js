var u=Object.defineProperty,b=Object.defineProperties;var f=Object.getOwnPropertyDescriptors;var o=Object.getOwnPropertySymbols;var d=Object.prototype.hasOwnProperty,v=Object.prototype.propertyIsEnumerable;var t=(a,e,l)=>e in a?u(a,e,{enumerable:!0,configurable:!0,writable:!0,value:l}):a[e]=l,r=(a,e)=>{for(var l in e||(e={}))d.call(e,l)&&t(a,l,e[l]);if(o)for(var l of o(e))v.call(e,l)&&t(a,l,e[l]);return a},i=(a,e)=>b(a,f(e));import{al as n,ax as p}from"./index.js";import{e as c}from"./chartEditStore-286181fe.js";import{S as s}from"./index-24d355d9.js";import"./plugin-b4545888.js";import"./icon-fe12b3e9.js";import"./tables_list-71790294.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-618b90ad.js";import"./useTargetData.hook-18b4d332.js";var m=[{label:"\u8BF7\u9009\u62E9",value:""},{label:"\u8363\u6210",value:"26700"},{label:"\u6CB3\u5357",value:"20700",disabled:!0},{label:"\u6CB3\u5317",value:"18700"},{label:"\u5F90\u5DDE",value:"17800"},{label:"\u6F2F\u6CB3",value:"16756"},{label:"\u4E09\u95E8\u5CE1",value:"12343"},{label:"\u90D1\u5DDE",value:"9822"},{label:"\u5468\u53E3",value:"8912"},{label:"\u6FEE\u9633",value:"6834"},{label:"\u4FE1\u9633",value:"5875"},{label:"\u65B0\u4E61",value:"3832"},{label:"\u5927\u540C",value:"1811"}];const g={dataset:m,value:"",borderWidth:1,borderStyle:"solid",borderColor:"#1a77a5",background:"none",borderRadius:6,color:"#ffffff",textAlign:"center",fontWeight:"normal",backgroundColor:"transparent",fontSize:20,onChange(a,e){}};class w extends c{constructor(){super(...arguments),this.key=s.key,this.chartConfig=n(s),this.option=n(g),this.attr=i(r({},p),{w:200,h:36,zIndex:1})}}export{w as default,g as option};
