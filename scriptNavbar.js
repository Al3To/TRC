document-addEventListener("DOMContentLoaded", function(){

        autohide = document.querySelector('.autohide');
        if(autohide){
            var last_scroll_top = 0;
            window.addEventListener('scroll', function(){
                let scroll_top = window.scrollY;
                if(scroll_top < last_scroll_top){
                    autohide.classList.remove('scrolled-down');
                    autohide.classList.add('scrolled-up');
                }
                else {
                    autohide.classList.remove('scrolled-up');
                    autohide.classList.add('scrolled-down');
                }
                last_scroll_top = scroll_top;
            })
        }
});