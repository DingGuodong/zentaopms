import{d as i,r as n,o as p,m as c,e as a,w as _,z as u,n as l,F as m}from"./index.js";const x=i({__name:"CollapseItem",props:{name:{type:String,required:!0},expanded:{type:Boolean,required:!1,default:!1}},setup(t){const o=e=>{e.preventDefault(),e.stopPropagation()};return(e,v)=>{const r=n("n-divider"),s=n("n-collapse-item"),d=n("n-collapse");return p(),c(m,null,[a(r,{style:{margin:"10px 0"}}),a(d,{"arrow-placement":"right","default-expanded-names":t.expanded?t.name:null,accordion:""},{"header-extra":_(()=>[u("div",{onClick:o},[l(e.$slots,"header")])]),default:_(()=>[a(s,{title:t.name,name:t.name},{default:_(()=>[l(e.$slots,"default")]),_:3},8,["title","name"])]),_:3},8,["default-expanded-names"])],64)}}});export{x as _};
