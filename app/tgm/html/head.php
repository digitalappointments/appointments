<META name="rating" content="General">
<META name="mssmarttagspreventparsing" content="true">
<META name="ROBOTS" content="index,follow">
<META name="REVISIT-AFTER" content="7 days">
<META name="ROBOTS" content="ALL">
<META http-equiv="content-type" content="text/html; charset=ISO-8859-1">

<link rel="shortcut icon" href="app/tgm/images/favicon.ico"/>

<LINK type=text/css rel=stylesheet href="app/tgm/css/styles.css">
<LINK type=text/css rel=stylesheet href="app/tgm/js/modal/subModal.css">

<!-- script type="text/javascript" src="app/tgm/js/modal/subModal.js"></script -->
<!-- script type="text/javascript" src="app/tgm/js/flowplayer-3.1.2.min.js"></script -->
<script type="text/javascript" src="app/tgm/js/jquery.js"></script>
<script type="text/javascript" src="app/tgm/js/common.js"></script>
<script type="text/javascript" src="app/tgm/js/jsutils.js"></script>
<script type="text/javascript" src="app/tgm/js/main_menu.js"></script>


<script type="text/javascript">

    main_menu_buttons = {};

    var active_page = "<?php echo CONTENT_PAGE?>";
    var last_active_page = active_page;
    var last_page_attempt = active_page;

    function menuClick(image_name) {
        if (image_name == active_page) {
            return false;
        }
        return true;
    }

    function setActivePage() {
        if (document.images && (main_menu_buttons[last_active_page])) {
            document[last_active_page].src = main_menu_buttons[active_page]['normal'].src;
        }
        if (document.images && (main_menu_buttons[active_page])) {
            document[active_page].src = main_menu_buttons[active_page]['active'].src;
        }
    }

    function fetchPage(page_name) {
        last_page_attempt = page_name;
        var data = {
            f:page_name
        }

        $.ajax({
            type:"GET",
            url:"/tgm/",
            data:data,
            dataType:"json",
            cache:false,
            error:function (XMLHttpRequest, textStatus, errorThrown) {
                // typically only one of textStatus or errorThrown will have info
                var httpStatus = XMLHttpRequest.status;
                if (httpStatus == 401) {
                    top.location.href = "index.php?SessionExpired";
                }
                else {
                    alert("Request Failed - HTTP Status:" + httpStatus);
                }
            },
            success:function (response, textStatus) {
                var data = '';
                debugger;
                if (textStatus === "success" && response.length) {
                    last_active_page = active_page;
                    active_page = last_page_attempt;
                    setActivePage();
                    // var resp = JSON.parse(response); //parse JSON
                    // var resp = $.parseJSON(response); //parse JSON
                    if (response.length > 0) {
                        data = response;
                    }
                }
                var el = document.getElementById("page_content");
                if (el) {
                    el.innerHTML = data;
                    // window.scroll(0,360);
                }
            }

        });

    }

    (function ($) {
        $(document).ready(function () {

            // PRELOADING MAIN_MENU_BUTTONS

            if (document.images) {
                setImage('home');
                setImage('about');
                setImage('products');
                setImage('download');
                setImage('community');
                setImage('contact');

                setActivePage();
            }


            function setImage(image_name) {
                var img_normal = new Image();
                img_normal.src = 'app/tgm/images/btn_' + image_name + '.png';
                var img_hover = new Image();
                img_hover.src = 'app/tgm/images/btn_' + image_name + '_h.png';
                var img_active = new Image();
                img_active.src = 'app/tgm/images/btn_' + image_name + '_a.png';

                main_menu_buttons[image_name] = { 'normal':img_normal,
                    'hover':img_hover,
                    'active':img_active }
            }

        });

    })(jQuery);
</script>
