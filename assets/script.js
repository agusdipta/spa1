document.addEventListener("click", (e)=>{
  const target = e.target.closest("[data-service-id]");
  if(!target) return;
  const id = target.getAttribute("data-service-id");
  const name = target.getAttribute("data-service-name");
  const sel = document.querySelector("#service-select");
  if(sel){
    sel.value = id;
    document.querySelector("#reserve").scrollIntoView({behavior:"smooth"});
  }
});