const Timer = {

interval : null,
remaining : 0,

start(seconds){

/* hentikan timer lama */

if(this.interval){
clearInterval(this.interval);
}

this.remaining = seconds;

document
.getElementById("timer-box")
.classList.remove("hidden");

this.interval = setInterval(()=>{

this.remaining--;

let minutes = Math.floor(this.remaining / 60);
let seconds = this.remaining % 60;

document.getElementById("timer-display").innerText =
String(minutes).padStart(2,"0")+":"+String(seconds).padStart(2,"0");

if(this.remaining <= 0){

clearInterval(this.interval);

nextSection();

}

},1000);

},

stop(){

clearInterval(this.interval);

}

};