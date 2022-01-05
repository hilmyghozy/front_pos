


$( document ).ready(function() {
    //home 
    var time = 15; 
      var $progressBar,
          $bar,
          $elem,
          isPause,
          tick,
          percentTime;

        $("#owl-demo").owlCarousel({
          slideSpeed : 500,
          paginationSpeed : 500,
          singleItem : true,
          afterInit : progressBar,
          afterMove : moved,
          transitionStyle : "fade",
          startDragging : pauseOnDragging
        });
        $("#owl-logo").owlCarousel({
            navigation : true,
            items:6,
            autoPlay:true
        });
        $("#owl-demo-2").owlCarousel({
            navigation : true,
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            autoPlay:true
        });
        $("#owl-client").owlCarousel({
            navigation : true,
            items:3,
            navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"]
        });

        function progressBar(elem){
          $elem = elem;
          buildProgressBar();
          start();
        }

        function buildProgressBar(){
          $progressBar = $("<div>",{
            id:"progressBar"
          });
          $bar = $("<div>",{
            id:"bar"
          });
          $progressBar.append($bar).prependTo($elem);
        }

        function start() {
          percentTime = 0;
          isPause = false;
          tick = setInterval(interval, 10);
        };

        function interval() {
          if(isPause === false){
            percentTime += 1 / time;
            $bar.css({
               width: percentTime+"%"
             });
            if(percentTime >= 100){
              $elem.trigger('owl.next')
            }
          }
        }

        function pauseOnDragging(){
          isPause = true;
        }

        function moved(){
          clearTimeout(tick);
          start();
        }
    // profile
    $("#owl-demo-3").owlCarousel({
        navigation : true, // Show next and prev buttons
        slideSpeed : 300,
        paginationSpeed : 400,
        autoPlay:true,
        items:5,
        navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"]
    });
    
    //blog 
    $("#owl-demo-3").owlCarousel({
        navigation : true, // Show next and prev buttons
        slideSpeed : 300,
        paginationSpeed : 400,
        autoPlay:true,
        items:5,
        navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"]
    });

    //footer
    $('.container a').click(function(){ 
        var $target = $($(this).data('target')); 
        if(!$target.hasClass('in'))
            $('.container .in').removeClass('in').height(0);
    });

    //scroll up 
    $(function () {
        $.scrollUp({
            animation: 'fade',
            scrollImg: { active: true, type: 'background', src: '../images/top.png' }
        });
    });
});
